<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2008 - 2014 Asikart.com. All rights reserved.
 * @license    GNU Lesser General Public License version 2.1 or later.
 */

namespace Windwalker\Application\Web;

/**
 * Class Response
 *
 * @since 2.0
 */
class Response implements ResponseInterface
{
	/**
	 * Property cachable.
	 *
	 * @var boolean
	 */
	protected $cachable = false;

	/**
	 * Property headers.
	 *
	 * @var  array
	 */
	protected $headers = array();

	/**
	 * Property body.
	 *
	 * @var  string
	 */
	protected $body = null;

	/**
	 * Property mimeType.
	 *
	 * @var string
	 */
	protected $mimeType = 'text/html';

	/**
	 * Property charSet.
	 *
	 * @var string
	 */
	protected $charSet = 'utf-8';

	/**
	 * The body modified date for response headers.
	 *
	 * @var \DateTime
	 */
	protected $modifiedDate = null;

	/**
	 * Method to send the application response to the client.  All headers will be sent prior to the main
	 * application output data.
	 *
	 * @param   boolean $returnBody
	 *
	 * @return  string
	 *
	 * @since   2.0
	 */
	public function respond($returnBody = false)
	{
		// Send the content-type header.
		$this->setHeader('Content-Type', $this->mimeType . '; charset=' . $this->charSet);

		// If the response is set to uncachable, we need to set some appropriate headers so browsers don't cache the response.
		if (!$this->isCachable())
		{
			// Expires in the past.
			$this->setHeader('Expires', 'Mon, 1 Jan 2001 00:00:00 GMT', true);

			// Always modified.
			$this->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT', true);
			$this->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0', false);

			// HTTP 1.0
			$this->setHeader('Pragma', 'no-cache');
		}
		else
		{
			// Expires.
			$this->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 900) . ' GMT');

			// Last modified.
			if ($this->modifiedDate instanceof \DateTime)
			{
				$this->modifiedDate->setTimezone(new \DateTimeZone('UTC'));

				$this->setHeader('Last-Modified', $this->modifiedDate->format('D, d M Y H:i:s') . ' GMT');
			}
		}

		$this->sendHeaders();

		if ($returnBody)
		{
			return $this->getBody();
		}
		else
		{
			echo $this->getBody();
		}

		return '';
	}

	/**
	 * Checks the accept encoding of the browser and compresses the data before
	 * sending it to the client if possible.
	 *
	 * @param string $encodings
	 *
	 * @return  static
	 *
	 * @since   2.0
	 */
	public function compress($encodings)
	{
		// Supported compression encodings.
		$supported = array(
			'x-gzip' => 'gz',
			'gzip' => 'gz',
			'deflate' => 'deflate'
		);

		// Get the supported encoding.
		$encodings = array_intersect($encodings, array_keys($supported));

		// If no supported encoding is detected do nothing and return.
		if (empty($encodings))
		{
			return $this;
		}

		// Verify that headers have not yet been sent, and that our connection is still alive.
		if ($this->checkHeadersSent() || !$this->checkConnectionAlive())
		{
			return $this;
		}

		// Iterate through the encodings and attempt to compress the data using any found supported encodings.
		foreach ($encodings as $encoding)
		{
			if (($supported[$encoding] == 'gz') || ($supported[$encoding] == 'deflate'))
			{
				// Verify that the server supports gzip compression before we attempt to gzip encode the data.
				if (!extension_loaded('zlib') || ini_get('zlib.output_compression'))
				{
					continue;
				}

				// Attempt to gzip encode the data with an optimal level 4.
				$data = $this->getBody();
				$gzdata = gzencode($data, 4, ($supported[$encoding] == 'gz') ? FORCE_GZIP : FORCE_DEFLATE);

				// If there was a problem encoding the data just try the next encoding scheme.
				if ($gzdata === false)
				{
					continue;
				}

				// Set the encoding headers.
				$this->setHeader('Content-Encoding', $encoding);
				$this->setHeader('X-Content-Encoded-By', 'Windwalker');

				// Replace the output with the encoded data.
				$this->setBody($gzdata);

				// Compression complete, let's break out of the loop.
				break;
			}
		}

		return $this;
	}

	/**
	 * getCachable
	 *
	 * @param boolean $cachable
	 *
	 * @return  boolean
	 */
	public function isCachable($cachable = null)
	{
		if (is_bool($cachable))
		{
			$this->cachable = $cachable;

			return $cachable;
		}

		return $this->cachable;
	}

	/**
	 * Method to set a response header.  If the replace flag is set then all headers
	 * with the given name will be replaced by the new one.  The headers are stored
	 * in an internal array to be sent when the site is sent to the browser.
	 *
	 * @param   string   $name     The name of the header to set.
	 * @param   string   $value    The value of the header to set.
	 * @param   boolean  $replace  True to replace any headers with the same name.
	 *
	 * @return  Response  Instance of $this to allow chaining.
	 *
	 * @since   2.0
	 */
	public function setHeader($name, $value, $replace = false)
	{
		// Sanitize the input values.
		$name = (string) $name;
		$value = (string) $value;

		// If the replace flag is set, unset all known headers with the given name.
		if ($replace)
		{
			foreach ($this->headers as $key => $header)
			{
				if ($name == $header['name'])
				{
					unset($this->headers[$key]);
				}
			}

			// Clean up the array as unsetting nested arrays leaves some junk.
			$this->headers = array_values($this->headers);
		}

		// Add the header to the internal array.
		$this->headers[] = array('name' => $name, 'value' => $value);

		return $this;
	}

	/**
	 * Method to clear any set response headers.
	 *
	 * @return  Response  Instance of $this to allow chaining.
	 *
	 * @since   2.0
	 */
	public function clearHeaders()
	{
		$this->headers = array();

		return $this;
	}

	/**
	 * getHeaders
	 *
	 * @return  array
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * setHeaders
	 *
	 * @param   array $headers
	 *
	 * @return  Response  Return self to support chaining.
	 */
	public function setHeaders($headers)
	{
		$this->headers = $headers;

		return $this;
	}

	/**
	 * Return the body content
	 *
	 * @param   boolean  $asArray  True to return the body as an array of strings.
	 *
	 * @return  mixed  The response body either as an array or concatenated string.
	 *
	 * @since   2.0
	 */
	public function getBody($asArray = false)
	{
		return $asArray ? $this->body : implode((array) $this->body);
	}

	/**
	 * Set body content.  If body content already defined, this will replace it.
	 *
	 * @param   string  $content  The content to set as the response body.
	 *
	 * @return  Response  Instance of $this to allow chaining.
	 *
	 * @since   2.0
	 */
	public function setBody($content)
	{
		$this->body = array((string) $content);

		return $this;
	}


	/**
	 * Prepend content to the body content
	 *
	 * @param   string  $content  The content to prepend to the response body.
	 *
	 * @return  Response  Instance of $this to allow chaining.
	 *
	 * @since   2.0
	 */
	public function prependBody($content)
	{
		array_unshift($this->body, (string) $content);

		return $this;
	}

	/**
	 * Append content to the body content
	 *
	 * @param   string  $content  The content to append to the response body.
	 *
	 * @return  Response  Instance of $this to allow chaining.
	 *
	 * @since   2.0
	 */
	public function appendBody($content)
	{
		array_push($this->body, (string) $content);

		return $this;
	}

	/**
	 * Method to send a header to the client.  We are wrapping this to isolate the header() function
	 * from our code base for testing reasons.
	 *
	 * @param   string   $string   The header string.
	 * @param   boolean  $replace  The optional replace parameter indicates whether the header should
	 *                             replace a previous similar header, or add a second header of the same type.
	 * @param   integer  $code     Forces the HTTP response code to the specified value. Note that
	 *                             this parameter only has an effect if the string is not empty.
	 *
	 * @return  static
	 *
	 * @codeCoverageIgnore
	 * @see     header()
	 * @since   2.0
	 */
	public function header($string, $replace = true, $code = null)
	{
		header($string, $replace, $code);

		return $this;
	}

	/**
	 * Send the response headers.
	 *
	 * @return  Response  Instance of $this to allow chaining.
	 *
	 * @since   2.0
	 */
	public function sendHeaders()
	{
		if (!$this->checkHeadersSent())
		{
			foreach ($this->getHeaders() as $header)
			{
				if ('status' == strtolower($header['name']))
				{
					// 'status' headers indicate an HTTP status, and need to be handled slightly differently
					$this->header(ucfirst(strtolower($header['name'])) . ': ' . $header['value'], null, (int) $header['value']);
				}
				else
				{
					$this->header($header['name'] . ': ' . $header['value']);
				}
			}
		}
	}

	/**
	 * Method to check to see if headers have already been sent.  We are wrapping this to isolate the
	 * headers_sent() function from our code base for testing reasons.
	 *
	 * @return  boolean  True if the headers have already been sent.
	 *
	 * @codeCoverageIgnore
	 * @see     headers_sent()
	 * @since   2.0
	 */
	public function checkHeadersSent()
	{
		return headers_sent();
	}

	/**
	 * Method to check the current client connection status to ensure that it is alive.  We are
	 * wrapping this to isolate the connection_status() function from our code base for testing reasons.
	 *
	 * @return  boolean  True if the connection is valid and normal.
	 *
	 * @codeCoverageIgnore
	 * @see     connection_status()
	 * @since   2.0
	 */
	public function checkConnectionAlive()
	{
		return (connection_status() === CONNECTION_NORMAL);
	}

	/**
	 * getMimeType
	 *
	 * @return  string
	 */
	public function getMimeType()
	{
		return $this->mimeType;
	}

	/**
	 * setMimeType
	 *
	 * @param   string $mimeType
	 *
	 * @return  Response  Return self to support chaining.
	 */
	public function setMimeType($mimeType)
	{
		$this->mimeType = $mimeType;

		return $this;
	}

	/**
	 * getCharSet
	 *
	 * @return  string
	 */
	public function getCharSet()
	{
		return $this->charSet;
	}

	/**
	 * setCharSet
	 *
	 * @param   string $charSet
	 *
	 * @return  Response  Return self to support chaining.
	 */
	public function setCharSet($charSet)
	{
		$this->charSet = $charSet;

		return $this;
	}

	/**
	 * getModifiedDate
	 *
	 * @return  \DateTime
	 */
	public function getModifiedDate()
	{
		return $this->modifiedDate;
	}

	/**
	 * setModifiedDate
	 *
	 * @param   \DateTime $modifiedDate
	 *
	 * @return  Response  Return self to support chaining.
	 */
	public function setModifiedDate($modifiedDate)
	{
		$this->modifiedDate = $modifiedDate;

		return $this;
	}
}

