<?php
/**
 * Part of Windwalker project Test files.
 *
 * @copyright  Copyright (C) 2011 - 2014 SMS Taiwan, Inc. All rights reserved.
 * @license    GNU Lesser General Public License version 2.1 or later.
 */

namespace Windwalker\Html\Test;

use Windwalker\Dom\Helper\DomHelper;
use Windwalker\Html\HtmlList\Description;

/**
 * Test class of Description
 *
 * @since 2.1
 */
class DescriptionTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test instance.
	 *
	 * @var Description
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
		$this->instance = new Description('wolf', array('class' => 'animal'));
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
	 * @covers Windwalker\Html\Description::toString
	 *
	 */
	public function testToString()
	{
		$this->assertEquals(
			DomHelper::minify('<dd class="animal">wolf</dd>'),
			DomHelper::minify($this->instance)
		);
	}

	/**
	 * Method to test getText().
	 *
	 * @return void
	 *
	 * @covers Windwalker\Html\Description::getText
	 */
	public function testGetAndSetValue()
	{
		$this->instance->setText('lion');

		$this->assertEquals('lion', $this->instance->getText());
	}
}
