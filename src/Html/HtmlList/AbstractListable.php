<?php
/**
 * Part of Windwalker project. 
 *
 * @copyright  Copyright (C) 2008 - 2014 Asikart.com. All rights reserved.
 * @license    GNU Lesser General Public License version 2.1 or later.
 */

namespace Windwalker\Html\HtmlList;

use Windwalker\Dom\HtmlElement;

/**
 * The HtmlAbstractItem class.
 * 
 * @since  2.0
 */
abstract class AbstractListable extends HtmlElement
{
	/**
	 * Property text.
	 *
	 * @var  string
	 */
	protected $text = '';

	/**
	 * Property attributes.
	 *
	 * @var  string
	 */
	protected $attributes = array();

	/**
	 * @param $text
	 * @param $attributes
	 */
	public function __construct($tag, $text = null, $attributes = array())
	{
		$this->text = $text;

		parent::__construct($tag, $text, $attributes);
	}

	/**
	 * Method to get property Text
	 *
	 * @return  string
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * Method to set property text
	 *
	 * @param   string $text
	 *
	 * @return  static  Return self to support chaining.
	 */
	public function setText($text)
	{
		$this->text = $text;

		return $this;
	}
}
