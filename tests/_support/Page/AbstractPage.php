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
 * @since 1.10.7
 */
class AbstractPage
{
	/**
	 * @var array
	 * @since 1.10.7
	 */
	public static $h1 = ['css' => 'h1'];

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $clientId = '#client_id';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $buttonSave = 'Save';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $buttonSaveAndClose = 'Save & Close';

	/**
	 * @var array
	 * @since 1.10.7
	 */
	public static $messageContainer = '#system-message-container';

	/**
	 * @var array
	 * @since 1.10.7
	 */
	public static $labelModulePosition = "label[data-original-title='Status Module Position']";

	/**
	 * @var array
	 * @since 1.10.7
	 */
	public static $linkAdvanced = 'Advanced';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $messageSuccess = 'success';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $messageSaveSuccess = 'Save success';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $buttonInstall = 'Install';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $buttonNew = 'New';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $buttonSaveClose = 'Save & Close';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $buttonClear = 'Clear';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $search = '.icon-search';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $buttonDelete = 'Delete';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $check = '#cb0';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $chooseNo = 'No';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $chooseYes = 'Yes';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $amAdmin = 'administrator';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $labelAdmin = 'Administrator';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $buttonExtensions = 'Extensions';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $buttonTemplates = 'Templates';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $statusModulePosition = 'Status Module Position';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $positionTop = 'Top';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $enableTranslations = 'Enable translations';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $enableWebservices = 'Enable webservices';

	/**
	 * @param $value
	 * @return string
	 * @since 1.10.7
	 */
	public function returnXpath($value)
	{
		$xpath = "//span[contains(text(),'".$value."')]";
		return $xpath;
	}

}
