<?php
/**
 * @package     RedRad
 * @subpackage  OAuth2
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDRAD') or die;

/**
 * OAuth2 response data object class.
 *
 * @package     RedRad
 * @subpackage  OAuth2
 * @since       1.0
 */
class ROAuth2Response
{
	/**
	 * @var    integer  The server response code.
	 * @since  11.3
	 */
	public $code;

	/**
	 * @var    array  Response headers.
	 * @since  11.3
	 */
	public $description;

	/**
	 * @var    string  Server response body.
	 * @since  11.3
	 */
	public $uri;
}
