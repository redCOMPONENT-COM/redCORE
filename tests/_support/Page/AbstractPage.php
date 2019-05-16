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
	public static $clientId = '#client_id';

	/**
	 * @var string
	 */
	public static $buttonSave = 'Save';

	/**
	 * @var string
	 */
	public static $buttonSaveAndClose = 'Save & Close';

	/**
	 * @var array
	 */
	public static $messageContainer = '#system-message-container';

	/**
	 * @var array
	 */
	public static $labelModulePosition = "label[data-original-title='Status Module Position']";

	/**
	 * @var array
	 */
	public static $linkAdvanced = 'Advanced';

	/**
	 * @var string
	 */
	public static $messageSaveSuccess = 'Save success';

	/**
	 * @var string
	 */
	public static $buttonInstall = 'Install';

	/**
	 * @var string
	 */
	public static $chooseNo = 'No';

	/**
	 * @var string
	 */
	public static $chooseYes = 'Yes';

	/**
	 * @var string
	 */
	public static $amAdmin = 'administrator';

	/**
	 * @var string
	 */
	public static $labelAdmin = 'Administrator';

	/**
	 * @var string
	 */
	public static $buttonExtensions = 'Extensions';

	/**
	 * @var string
	 */
	public static $buttonTemplates = 'Templates';

	/**
	 * @var string
	 */
	public static $statusModulePosition = 'Status Module Position';

	/**
	 * @var string
	 */
	public static $positionTop = 'Top';

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
