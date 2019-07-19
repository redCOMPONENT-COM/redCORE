<?php
/**
 * @package     redCORE
 * @subpackage  Page
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Page;

/**
 * Class WebserviceManagerPage
 * @package Page
 */
class WebserviceManagerPage extends AbstractPage
{
	/**
	 * @var string
	 */
	public static $URL2 = 'administrator/index.php?option=com_redcore&view=webservices';

	/**
	 * @var string
	 */
	public static $textManager = 'Webservice Manager';

	/**
	 * @var string
	 */
	public static $buttonNotInstall = 'Not installed webservices ';

	/**
	 * @var array
	 */
	public static $installElement = '.lc-install_all_webservices';

	/**
	 * @var string
	 */
	public static $tableForm = '//table[@id=\'oauthClientsList\']';

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
	public static $pathUserWebservice = 'site.users.1.0.0.xml';
}
