<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU Lesser General Public License version 2.1 or later.
 */

namespace Windwalker\Html\Test;

use Windwalker\Dom\Helper\DomHelper;
use Windwalker\Html\HtmlList\Item;

/**
 * Test class of Item
 *
 * @since 2.0
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test instance.
	 *
	 * @var Item
	 */
	protected $instance;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp()
	{
		$this->instance = new Item('wolf', array('class' => 'animal'));
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown()
	{
	}

	/**
	 * testToString
	 *
	 * @return  void
	 *
	 * @covers Windwalker\Html\Item::toString
	 *
	 */
	public function testToString()
	{
		$this->assertEquals(
			DomHelper::minify('<li class="animal">wolf</li>'),
			DomHelper::minify($this->instance)
		);
	}

	/**
	 * Method to test getText().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Html\Item::getText
	 */
	public function testGetAndSetValue()
	{
		$this->instance->setText('lion');

		$this->assertEquals('lion', $this->instance->getText());
	}
}
