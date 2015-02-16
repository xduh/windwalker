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
 * The HtmlTerm class.
 * 
 * @since  2.1
 */
class Term extends AbstractDefinition
{
	
	/**
	 * @param $text
	 * @param $attributes
	 */
	public function __construct($text = null, $attributes = array())
	{
		$this->text = $text;

		parent::__construct('dt', $text, $attributes);
	}

}