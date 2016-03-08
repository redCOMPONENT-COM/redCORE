<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Payment Log Controller
 *
 * @package     Redcore.Backend
 * @subpackage  Controllers
 * @since       1.5
 */
class RedcoreControllerPayment_Log extends RControllerForm
{
	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 */
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$input = JFactory::getApplication()->input;

		$fromPayment = $input->getBool('from_payment', false);

		// Append the tab name for the payment view
		if ($fromPayment)
		{
			$append .= '&tab=logs';
		}

		return $append;
	}
}
