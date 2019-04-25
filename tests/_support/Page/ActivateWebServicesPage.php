<?php
/**
 * @package redCORE
 * @subpacket Page
 * @copyright   Copyright (C) 2008 - 2018 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 **/
namespace Page;

/**
 * Class ActivateWebservices
 * @package Page
**/

class ActivateWebServicesPage
{

	/**
	 * @var string
	 */
	public static $URL                    = 'administrator/index.php?option=com_redcore&view=config&layout=edit&component=com_redcore';

	/**
	 * @var string
	 */
	public static $URL2                   = 'administrator/index.php?option=com_redcore&view=webservices';

	/**
	 * @var string
	 */
	public static $buttonWebservice       = 'Webservice options';

	/**
	 * @var array
	 */
	public static $h1                     = ['css' => 'h1'];

	/**
	 * @var string
	 */
	public static $titleRedConf           = 'redCORE Config';

	/**
	 * @var array
	 */
	public static $form                   = '#REDCORE_WEBSERVICES_OPTIONS';

	/**
	 * @var string
	 */
	public static $labelWebServices       = 'Enable webservices';

	/**
	 * @var string
	 */
	public static $choose                 = 'Yes';

	/**
	 * @var string
	 */
	public static $labelCheckUser         = 'Check user permission against';

	/**
	 * @var string
	 */
	public static $optional               = 'Joomla - Use already defined authorization checks in Joomla';

	/**
	 * @var string
	 */
	public static $selectorFormScroll     = "#jform_enable_soap-lbl";

	/**
	 * @var string
	 */
	public static $labelSOAP              = 'Enable SOAP Server';

	/**
	 * @var string
	 */
	public static $buttonSave             = 'Save';

	/**
	 * @var string
	 */
	public static $messageSaveSuccess     = 'Save success';

	/**
	 * @var array
	 */
	public static $messageContainer       = '#system-message-container';

	/**
	 * @var string
	 */
	public static $textManager            = 'Webservice Manager';

	/**
	 * @var string
	 */
	public static $buttonNotInstall       = 'Not installed webservices ';

	/**
	 * @var array
	 */
	public static $installElement         = '.lc-install_all_webservices';

	/**
	 * @var string
	 */
	public static $buttonInstall          = 'Install';

	/**
	 * @var string
	 */
	public static $tableForm              = '//table[@id=\'oauthClientsList\']';

	/**
	 * @var string
	 */
	public static $pathContactWebservice1 = 'administrator.contact.1.0.0.xml';

	/**
	 * @var string
	 */
	public static $pathContactWebservice2 = 'site.contact.1.0.0.xml';

	/**
	 * @var string
	 */
	public static $pathUserWebservice      = 'site.users.1.0.0.xml';

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
