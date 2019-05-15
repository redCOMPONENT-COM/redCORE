<?php
/**
 * @package     redCORE
 * @subpackage  Page
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Page;

/**
 * Class OAuthClientsPage
 * @package Page
 */
class OAuthClientsPage extends AbstractPage
{
	/**
	 * @var string
	 */
	public static $URL = 'administrator/index.php?option=com_redcore&view=oauth_clients';

	/**
	 * @var string
	 */
	public static $titleOAuth = 'OAuth Clients';

	/**
	 * @var string
	 */
	public static $titleOAuthClient = 'OAuth Client';

	/**
	 * @var string
	 */
	public static $labelClientID = '#jform_client_id-lbl';

	/**
	 * @var string
	 */
	public static $labelRedirectURI = '#jform_redirect_uri-lbl';

	/**
	 * @var string
	 */
	public static $fieldClientID = '#jform_client_id';

	/**
	 * @var string
	 */
	public static $fieldURI = '#jform_redirect_uri';

	/**
	 * @var string
	 */
	public static $fieldSearch = '#filter_search_oauth_clients';

	/**
	 * @var string
	 */
	public static $oauthClientsList = '#oauthClientsList';

	/**
	 * @var string
	 */
	public static $textDeleteSuccess = '1 item successfully deleted';

	/**
	 * @var string
	 */
	public static $message = 'Message';
}
