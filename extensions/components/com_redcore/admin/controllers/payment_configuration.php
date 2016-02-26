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
 * Payment Configuration Controller
 *
 * @package     Redcore.Backend
 * @subpackage  Controllers
 * @since       1.5
 */
class RedcoreControllerPayment_Configuration extends RControllerForm
{
	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);

		$data = $this->input->get('jform', array(), 'array');

		if (!empty($data['payment_name']))
		{
			$append .= '&payment_name=' . $data['payment_name'];
		}

		return $append;
	}
}
