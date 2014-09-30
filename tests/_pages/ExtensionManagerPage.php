<?php
/**
 * @package     RedShop
 * @subpackage  Page Class
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Class ExtensionManagerPage
 *
 * @since  1.4
 *
 * @link   http://codeception.com/docs/07-AdvancedUsage#PageObjects
 */
class ExtensionManagerPage
{
	// Include url of current page
	public static $URL = '/administrator/index.php?option=com_installer';

	public static $extensionDirectoryPath = "#install_directory";

	public static $installButton = "//input[contains(@onclick,'Joomla.submitbutton3()')]";

	public static $installSuccessMessage = "Installing component was successful.";

	public static $installDemoContent = "#install-demo-content";

	public static $demoDataInstallSuccessMessage = "Sample data successfully installed";

}
