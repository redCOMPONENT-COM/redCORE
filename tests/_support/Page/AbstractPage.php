<?php
/**
 * @package     redCORE
 * @subpackage  Page
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Page;

/**
 * Class AbstractPage
 * @package Page
 */
class AbstractPage
{
	/**
	 * @var array
	 */
	public static $h1 = ['css' => 'h1'];

	/**
	 * @var string
	 */
	public static $buttonSave = 'Save';

	/**
	 * @var array
	 */
	public static $messageContainer = '#system-message-container';

	/**
	 * @var string
	 */
	public static $messageSaveSuccess = 'Save success';

	/**
	 * @var string
	 */
	public static $buttonInstall = 'Install';

	/**
	 * @param $value
	 * @return string
	 */
	public function returnXpath($value)
	{
		$xpath = "//span[contains(text(),'".$value."')]";
		return $xpath;
	}
}
