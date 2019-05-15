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
 * @since 1.10.7
 */
class OAuthClientsPage extends AbstractPage
{
	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $URL = 'administrator/index.php?option=com_redcore&view=oauth_clients';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $titleOAuth = 'OAuth Clients';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $titleOAuthClient = 'OAuth Client';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $labelClientID = '#jform_client_id-lbl';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $labelRedirectURI = '#jform_redirect_uri-lbl';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $fieldClientID = '#jform_client_id';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $fieldURI = '#jform_redirect_uri';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $fieldSearch = '#filter_search_oauth_clients';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $oauthClientsList = '#oauthClientsList';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $textDeleteSuccess = '1 item successfully deleted';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $message = 'Message';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $grantTypes = 'Grant types';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $userCredentials = 'User Credentials';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $checkbox = '#jform_grant_types2';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $fieldSet = '#jform_grant_types';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $labelGrant = '#jform_grant_types-lbl';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $clientScopes = 'Client scopes';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $labelScopes = '#jform_scope-lbl';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $allWebservices = 'All webservices';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $scopeCheckAll = '.scopes-check-all';

	/**
	 * @var string
	 * @since 1.10.7
	 */
	public static $labelAllWeb = '#all-webservices';
}
