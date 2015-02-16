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
 * @since  2.1
 */
abstract class AbstractDefinition extends AbstractListable
{
	public function __construct($tag, $text = null, $attributes = array())
	{
		parent::__construct($tag, $text, $attributes);
	}

}
