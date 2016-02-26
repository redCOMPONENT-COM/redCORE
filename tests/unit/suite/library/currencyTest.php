<?php
/**
 * redCORE lib currency helper test
 *
 * @package    Redcore.UnitTest
 * @copyright  Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

// Register library prefix
require_once JPATH_LIBRARIES . '/redcore/bootstrap.php';

// Register the classes for autoload.
JLoader::registerPrefix('R', JPATH_REDCORE);

/**
 * Test class for currency lib helper class
 *
 * @package  Redcore.UnitTest
 * @since    1.2.0
 */
class currencyTest extends TestCaseDatabase
{
	/**
	 * This method is called before the first test of this test class is run.
	 *
	 * @return  void
	 *
	 * @since   12.1
	 */
	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		$db = JFactory::getDbo();

		foreach (JDatabaseDriver::splitSql(file_get_contents(REDCORE_UNIT_PATH . '/schema/currency.sql')) as $query)
		{
			if (trim($query))
			{
				$db->setQuery($query);
				$db->execute();
			}
		}
	}

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
		$this->assertEquals(RHelperCurrency::getIsoNumber('A'), 'A');
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
		$this->assertEquals(RHelperCurrency::getPrecision('A'), 0);
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
