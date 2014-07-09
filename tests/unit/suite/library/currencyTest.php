<?php
/**
 * redCORE lib currency helper test
 *
 * @package    Redcore.UnitTest
 * @copyright  Copyright (C) 2014 redCOMPONENT.com
 * @license    GNU General Public License version 2 or later
 */

// Register library prefix
require_once JPATH_LIBRARIES . '/redcore/bootstrap.php';

// Bootstraps redCORE
RBootstrap::bootstrap(false);

/**
 * Test class for Redevent lib helper class
 *
 * @package  Redevent.UnitTest
 * @since    1.2.0
 */
class currencyTest extends JoomlaTestCase
{
	/**
	 * Test GetIsoCode
	 *
	 * @return void
	 */
	public function testGetIsoCode()
	{
		$this->assertEquals(RHelperCurrency::getIsoCode(978), 'EUR');
	}

	/**
	 * Test GetIsoNumber
	 *
	 * @return void
	 */
	public function testGetIsoNumber()
	{
		$this->assertEquals(RHelperCurrency::getIsoNumber('EUR'), 978);
	}

	/**
	 * Test getPrecision
	 *
	 * @return void
	 */
	public function testGetPrecision()
	{
		$this->assertEquals(RHelperCurrency::getPrecision('EUR'), 2);
	}

	/**
	 * Test isValid
	 *
	 * @return void
	 */
	public function testIsValid()
	{
		$this->assertTrue(RHelperCurrency::isValid('EUR'));
		$this->assertFalse(RHelperCurrency::isValid('A'));
	}
}
