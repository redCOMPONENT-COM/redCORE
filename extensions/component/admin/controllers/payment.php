<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Payment Controller
 *
 * @package     Redcore.Backend
 * @subpackage  Controllers
 * @since       1.5
 */
class RedcoreControllerPayment extends RControllerForm
{
	/**
	 * Ajax call to get logs tab content.
	 *
	 * @return  void
	 */
	public function ajaxlogs()
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$paymentId = $input->getInt('id');

		if ($paymentId)
		{
			/** @var RedcoreModelPayment_Logs $logsModel */
			$logsModel = RModelAdmin::getAdminInstance('Payment_Logs', array(), 'com_redcore');
			$state = $logsModel->getState();

			$logsModel->setState('filter.payment_id', $paymentId);
			$app->setUserState('log.payment_id', $paymentId);

			$formName = 'logsForm';
			$pagination = $logsModel->getPagination();
			$pagination->set('formName', $formName);

			echo RLayoutHelper::render('payment.logs', array(
					'state' => $state,
					'items' => $logsModel->getItems(),
					'pagination' => $pagination,
					'filterForm' => $logsModel->getForm(),
					'activeFilters' => $logsModel->getActiveFilters(),
					'formName' => $formName,
					'showToolbar' => true,
					'action' => RRoute::_('index.php?option=com_redcore&view=payment&model=payment_logs'),
					'return' => base64_encode('index.php?option=com_redcore&view=payment&layout=edit&id='
						. $paymentId . '&tab=logs&from_payment=1')
				)
			);
		}

		$app->close();
	}

	/**
	 * Method called to save a model state
	 *
	 * @return  void
	 */
	public function saveModelState()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$returnUrl = $input->get('return', '', 'Base64');
		$returnUrl = $returnUrl ? base64_decode($returnUrl) : 'index.php';

		if ($model = $input->get('model', null))
		{
			$context = $input->getCmd('context', '');

			$model = RModel::getAdminInstance(ucfirst($model), array('context' => $context));

			$state = $model->getState();
		}

		$app->redirect($returnUrl);
	}

	/**
	 * Force Check payment now
	 *
	 * @return  void
	 */
	public function checkPayment()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$ids = $input->get('cid', array(), 'array');

		foreach ($ids as $id)
		{
			$status = RApiPaymentHelper::checkPayment($id);

			if (!empty($status))
			{
				$app->enqueueMessage($status['message'], !empty($status['type']) ? $status['type'] : 'message');
			}
		}

		// Redirect to the list screen
		$this->setRedirect(
			$this->getRedirectToListRoute($this->getRedirectToListAppend())
		);
	}

	/**
	 * Capture payment
	 *
	 * @return  void
	 */
	public function capturePayment()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$ids = $input->get('cid', array(), 'array');

		foreach ($ids as $id)
		{
			$status = RApiPaymentHelper::capturePayment($id);

			if ($status)
			{
				$app->enqueueMessage(JText::_('COM_REDCORE_PAYMENT_CAPTURE_PAYMENT_SUCCESS'));
			}
			else
			{
				$lastLog = RApiPaymentHelper::getLastPaymentLog($id);
				$app->enqueueMessage(JText::sprintf('COM_REDCORE_PAYMENT_CAPTURE_PAYMENT_FAILED', $lastLog->message_text), 'error');
			}
		}

		// Redirect to the list screen
		$this->setRedirect(
			$this->getRedirectToListRoute($this->getRedirectToListAppend())
		);
	}

	/**
	 * Refund payment
	 *
	 * @return  void
	 */
	public function refundPayment()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$ids = $input->get('cid', array(), 'array');

		foreach ($ids as $id)
		{
			$status = RApiPaymentHelper::refundPayment($id);

			if ($status)
			{
				$app->enqueueMessage(JText::_('COM_REDCORE_PAYMENT_REFUND_PAYMENT_SUCCESS'));
			}
			else
			{
				$lastLog = RApiPaymentHelper::getLastPaymentLog($id);
				$app->enqueueMessage(JText::sprintf('COM_REDCORE_PAYMENT_REFUND_PAYMENT_FAILED', $lastLog->message_text), 'error');
			}
		}

		// Redirect to the list screen
		$this->setRedirect(
			$this->getRedirectToListRoute($this->getRedirectToListAppend())
		);
	}

	/**
	 * Delete payment
	 *
	 * @return  void
	 */
	public function deletePayment()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$ids = $input->get('cid', array(), 'array');

		foreach ($ids as $id)
		{
			$status = RApiPaymentHelper::deletePayment($id);

			if ($status)
			{
				$app->enqueueMessage(JText::_('COM_REDCORE_PAYMENT_DELETE_PAYMENT_SUCCESS'));
			}
			else
			{
				$lastLog = RApiPaymentHelper::getLastPaymentLog($id);
				$app->enqueueMessage(JText::sprintf('COM_REDCORE_PAYMENT_DELETE_PAYMENT_FAILED', $lastLog->message_text), 'error');
			}
		}

		// Redirect to the list screen
		$this->setRedirect(
			$this->getRedirectToListRoute($this->getRedirectToListAppend())
		);
	}
}
