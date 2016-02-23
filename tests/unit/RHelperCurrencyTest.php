<?php
// require_once  './joomla-cms3/libraries/redcore/bootstrap.php';
// RBootstrap::bootstrap(false);
require_once __DIR__ . '/../joomla-cms3/libraries/redcore/helper/currency.php';

class RHelperCurrencyTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function dummy()
    {
        $this->assertTrue(true, 'true is true');
    }

    public function getCurrencyFalseCurrencyReturnsNull()
    {
        Stub::make('JFactory', array('getDbo' => function () { return true; }));
        $this->assertNull(RHelperCurrency::getCurrency(false), 'Get empty currency returns null');
    }
}