<?php
/**
 * redCORE lib currency helper test
 *
 * @package    Redcore.UnitTest
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com
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
		$this->assertEquals(RHelperCurrency::getIsoCode(233445), false);
		$this->assertEquals(RHelperCurrency::getIsoCode('asdasd'), false);
	}

	/**
	 * Test GetIsoNumber
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function testGetIsoNumber()
	{
		$this->assertEquals(RHelperCurrency::getIsoNumber('EUR'), 978);

		try
		{
			RHelperCurrency::getIsoNumber('A');
			throw new Exception('there should have been another exception');
		}
		catch (OutOfRangeException $e)
		{
			// It was expected
		}
	}

	/**
	 * Test getPrecision
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function testGetPrecision()
	{
		$this->assertEquals(RHelperCurrency::getPrecision('EUR'), 2);

		try
		{
			RHelperCurrency::getPrecision('A');
			throw new Exception('there should have been another exception');
		}
		catch (OutOfRangeException $e)
		{
			// It was expected
		}
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

	/**
	 * Test getOptions
	 *
	 * @return void
	 */
	public function testGetOptions()
	{
		$options = RHelperCurrency::getCurrencyOptions();
		$this->assertTrue(is_array($options) && count($options));
	}
}
