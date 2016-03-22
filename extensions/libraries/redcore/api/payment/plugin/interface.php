<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * Interface for payment helper class
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.5
 */
interface RApiPaymentPluginInterface
{
	/**
	 * Handle the reception of notification from the payment gateway
	 * This method validates request that came from Payment gateway to check if it is valid and that it came through Payment gateway
	 *
	 * @param   string  $extensionName  Name of the extension
	 * @param   string  $ownerName      Name of the owner
	 * @param   array   $data           Data to fill out Payment form
	 * @param   array   &$logData       Log data for payment api
	 *
	 * @return bool paid status
	 */
	public function handleCallback($extensionName, $ownerName, $data, &$logData);
}
