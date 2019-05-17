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
 * @since 1.10.7
 */
class redCOREConfigPage extends AbstractPage
{
	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $URL = 'administrator/index.php?option=com_redcore&view=config&layout=edit&component=com_redcore';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $titleRedConf = 'redCORE Config';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $buttonWebservice = 'Webservice options';

	/**
	 * @var array
	 * @since 1.10.7
	 */
	public static $form = '#REDCORE_WEBSERVICES_OPTIONS';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $labelWebServices = 'Enable webservices';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $labelCheckUser = 'Check user permission against';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $optional = 'Joomla - Use already defined authorization checks in Joomla';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $selectorFormScroll = "#jform_enable_soap-lbl";

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $labelSOAP = 'Enable SOAP Server';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $tabTranslations = '//ul[@id="configTabs"]/li[2]/a';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $labelOauth2 = 'Enable Oauth2 Server';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $id = '//ul[@id="configTabs"]/li[2]/a';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $buttonOAuth2 = 'OAuth2 Server options';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $tabWebServices = '//ul[@id="configTabs"]/li[3]/a';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $formOAuth2 = '#REDCORE_OAUTH2_SERVER_OPTIONS';

}
