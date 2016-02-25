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
 * Payment Configurations Controller
 *
 * @package     Redcore.Backend
 * @subpackage  Controllers
 * @since       1.5
 */
class RedcoreControllerPayment_Configurations extends RControllerAdmin
{
	/**
	 * Creates test payment for specific payment name
	 *
	 * @return  void
	 */
	public function test()
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$paymentName = $input->getString('payment_name');
		$paymentConfigurationId = $input->getInt('payment_id', 0);

		$this->setRedirect(
			JRoute::_('index.php?option=' . $this->option
				. '&view=' . $this->view_list
				. '&payment_name=' . $paymentName
				. '&payment_id=' . $paymentConfigurationId
				. '&layout=test', false
			)
		);
	}
}
