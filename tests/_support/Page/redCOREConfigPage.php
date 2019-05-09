<?php
/**
 * @package     redCORE
 * @subpackage  Page
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Page;

/**
 * Class redCOREConfigPage
 * @package Page
 */
class redCOREConfigPage extends AbstractPage
{
	/**
	 * @var string
	 */
	public static $URL = 'administrator/index.php?option=com_redcore&view=config&layout=edit&component=com_redcore';

	/**
	 * @var string
	 */
	public static $titleRedConf = 'redCORE Config';

	/**
	 * @var string
	 */
	public static $buttonWebservice = 'Webservice options';

	/**
	 * @var array
	 */
	public static $form = '#REDCORE_WEBSERVICES_OPTIONS';

	/**
	 * @var string
	 */
	public static $labelWebServices = 'Enable webservices';

	/**
	 * @var string
	 */
	public static $choose = 'Yes';

	/**
	 * @var string
	 */
	public static $labelCheckUser = 'Check user permission against';

	/**
	 * @var string
	 */
	public static $optional = 'Joomla - Use already defined authorization checks in Joomla';

	/**
	 * @var string
	 */
	public static $selectorFormScroll = "#jform_enable_soap-lbl";

	/**
	 * @var string
	 */
	public static $labelSOAP = 'Enable SOAP Server';
}
