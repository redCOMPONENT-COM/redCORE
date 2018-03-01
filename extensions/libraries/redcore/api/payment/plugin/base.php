<?php
/**
 * @package     Redcore
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * redCORE Payment plugin base Class
 *
 * @package     Redcore
 * @subpackage  Payment
 * @since       1.5
 */
abstract class RApiPaymentPluginBase extends JPlugin
{
	/**
	 * Payment gateway helper class
	 * @var RApiPaymentPluginHelperPayment
	 */
	public $paymentHelper = null;

	/**
	 * Name of the plugin gateway
	 * @var string
	 */
	protected $paymentName = null;

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  1.5
	 */
	protected $autoloadLanguage = true;

	protected $offlinePayment = false;

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'group', 'params', 'language'
	 *                             (this list is not meant to be comprehensive).
	 *
	 * @since   2.0
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		// Load default helper file or use the plugin helper file
		$this->loadRedpaymentHelper();

		$this->offlinePayment = $this->params->get('offline_payment', 0);
		$this->paymentHelper->offlinePayment = $this->offlinePayment;
		$this->paymentHelper->paymentName = $this->paymentName;
	}

	/**
	 * Collects all payment plugins for given extension and owner name
	 *
	 * @param   string  $extensionName  Name of the extension
	 * @param   string  $ownerName      Name of the owner
	 * @param   array   &$payments      Payments list
	 *
	 * @return string
	 */
	public function onRedpaymentListPayments($extensionName, $ownerName, &$payments)
	{
		$this->setRedpaymentOptions($extensionName, $ownerName);

		// Plugin is disabled (optionally for this extension)
		if (!$this->paymentHelper->pluginEnabled)
		{
			return null;
		}

		$payments[$this->paymentName . $extensionName . $ownerName] = (object) array(
			'value' => $this->paymentName,
			'text' => $this->paymentHelper->params->get('payment_title', $this->params->get('payment_title', $this->paymentName)),
			'logo' => $this->paymentHelper->params->get('payment_logo', $this->params->get('payment_logo', '')),
			'params' => $this->paymentHelper->params,
			'helper' => $this->paymentHelper
		);
	}

	/**
	 * Displays payment plugin layout with filled data for given extension
	 *
	 * @param   string  $paymentName    Payment name
	 * @param   string  $extensionName  Extension name
	 * @param   string  $ownerName      Owner name
	 * @param   array   $data           Data for the payment form
	 * @param   string  &$html          Html output
	 *
	 * @return string
	 */
	public function onRedpaymentDisplayPayment($paymentName, $extensionName, $ownerName, $data, &$html)
	{
		if (!$this->isPaymentEnabled($paymentName, $extensionName, $ownerName))
		{
			return null;
		}

		return $this->paymentHelper->displayPayment($extensionName, $ownerName, $data, $html);
	}

	/**
	 * This method stores payment information before submit to the Payment Gateway
	 *
	 * @param   string  $paymentName    Payment name
	 * @param   string  $extensionName  Extension name
	 * @param   string  $ownerName      Owner name
	 * @param   array   $data           Data to fill out Payment form
	 * @param   array   &$paymentId     New Payment Id
	 *
	 * @return mixed Id of the payment or false
	 */
	public function onRedpaymentCreatePayment($paymentName, $extensionName, $ownerName, $data, &$paymentId)
	{
		$data['extension_name'] = array_key_exists('extension_name', $data) ? $data['extension_name'] : $extensionName;
		$data['owner_name'] = array_key_exists('owner_name', $data) ? $data['owner_name'] : $ownerName;

		if (!$this->isPaymentEnabled($paymentName, $data['extension_name'], $data['owner_name']))
		{
			return false;
		}

		// Even if the plugin is disabled we will create the record in the database
		$paymentId = $this->paymentHelper->createPayment($extensionName, $ownerName, $data);

		return $paymentId;
	}

	/**
	 * Check for notifications from Payment Gateway
	 *
	 * @param   string  $paymentName  Name of the extension
	 * @param   int     $paymentId    Payment id to check
	 * @param   array   &$status      Payment check status
	 *
	 * @return mixed
	 */
	public function onRedpaymentCheckPayment($paymentName, $paymentId, &$status)
	{
		if ($paymentName != $this->paymentName)
		{
			return null;
		}

		$payment = RApiPaymentHelper::getPaymentById($paymentId);

		$this->setRedpaymentOptions($payment->extension_name, $payment->owner_name);

		// Plugin is disabled (optionally for this extension)
		if (!$this->paymentHelper->pluginEnabled)
		{
			return null;
		}

		// Check for new status
		$this->paymentHelper->handleCheckPayment($paymentId, $status);

		// We call extension helper file to trigger afterHandleCheckPayment method if needed
		RApiPaymentHelper::triggerExtensionHelperMethod(
			$payment->extension_name, 'afterHandleCheckPayment', $payment->owner_name, $paymentName, $payment, $status
		);
	}

	/**
	 * This method tries to make a refund (credit) to the customer through Payment Gateway
	 * Note: Not all Payment Gateways support this feature
	 *
	 * @param   string  $paymentName    Payment name
	 * @param   string  $extensionName  Extension name
	 * @param   string  $ownerName      Owner name
	 * @param   object  $data           Data needed to preform refund
	 * @param   bool    &$isRefunded    If refund is successful then this flag should be true
	 *
	 * @return void
	 */
	public function onRedpaymentRefundPayment($paymentName, $extensionName, $ownerName, $data, &$isRefunded)
	{
		if (!$this->isPaymentEnabled($paymentName, $extensionName, $ownerName))
		{
			return;
		}

		$logData = RApiPaymentHelper::generatePaymentLog(
			RApiPaymentStatus::getStatusCreated(),
			$data,
			JText::sprintf('LIB_REDCORE_PAYMENT_LOG_REFUND_MESSAGE', $this->paymentName)
		);

		// Refund payment
		$this->paymentHelper->handleRefundPayment($extensionName, $ownerName, $data, $logData, $isRefunded);

		// Save payment log and update change for payment
		RApiPaymentHelper::saveNewPaymentLog($logData);

		// We call extension helper file to trigger afterHandleRefundPayment method if needed
		RApiPaymentHelper::triggerExtensionHelperMethod($extensionName, 'afterHandleRefundPayment', $ownerName, $paymentName, $data, $isRefunded);
	}

	/**
	 * This method tries to make a capture on the authorized payment through Payment Gateway
	 * Note: Not all Payment Gateways support this feature
	 *
	 * @param   string  $paymentName    Payment name
	 * @param   string  $extensionName  Extension name
	 * @param   string  $ownerName      Owner name
	 * @param   object  $data           Data needed to preform capture
	 * @param   bool    &$isCaptured    If capture is successful then this flag should be true
	 *
	 * @return void
	 */
	public function onRedpaymentCapturePayment($paymentName, $extensionName, $ownerName, $data, &$isCaptured)
	{
		if (!$this->isPaymentEnabled($paymentName, $extensionName, $ownerName))
		{
			return;
		}

		$logData = RApiPaymentHelper::generatePaymentLog(
			RApiPaymentStatus::getStatusCreated(),
			$data,
			JText::sprintf('LIB_REDCORE_PAYMENT_LOG_CAPTURE_MESSAGE', $this->paymentName)
		);

		// Capture payment
		$this->paymentHelper->handleCapturePayment($extensionName, $ownerName, $data, $logData, $isCaptured);

		// Save payment log and update change for payment
		RApiPaymentHelper::saveNewPaymentLog($logData);

		// We call extension helper file to trigger afterHandleCapturePayment method if needed
		RApiPaymentHelper::triggerExtensionHelperMethod($extensionName, 'afterHandleCapturePayment', $ownerName, $paymentName, $data, $isCaptured);
	}

	/**
	 * This method tries to make a delete on the authorized payment through Payment Gateway
	 * Note: Not all Payment Gateways support this feature
	 *
	 * @param   string  $paymentName    Payment name
	 * @param   string  $extensionName  Extension name
	 * @param   string  $ownerName      Owner name
	 * @param   object  $data           Data needed to preform delete
	 * @param   bool    &$isDeleted     If delete is successful then this flag should be true
	 *
	 * @return void
	 */
	public function onRedpaymentDeletePayment($paymentName, $extensionName, $ownerName, $data, &$isDeleted)
	{
		if (!$this->isPaymentEnabled($paymentName, $extensionName, $ownerName))
		{
			return;
		}

		$logData = RApiPaymentHelper::generatePaymentLog(
			RApiPaymentStatus::getStatusCreated(),
			$data,
			JText::sprintf('LIB_REDCORE_PAYMENT_LOG_DELETE_MESSAGE', $this->paymentName)
		);

		// Delete payment
		$this->paymentHelper->handleDeletePayment($extensionName, $ownerName, $data, $logData, $isDeleted);

		// Save payment log and update change for payment
		RApiPaymentHelper::saveNewPaymentLog($logData);

		// We call extension helper file to trigger afterHandleDeletePayment method if needed
		RApiPaymentHelper::triggerExtensionHelperMethod($extensionName, 'afterHandleDeletePayment', $ownerName, $paymentName, $data, $isDeleted);
	}

	/**
	 * This method tries to make a accept process
	 * This method is called when Payment gateway redirect customer on success but we still do not know if the payment is confirmed
	 *
	 * @param   string  $paymentName    Payment name
	 * @param   string  $extensionName  Extension name
	 * @param   string  $ownerName      Owner name
	 * @param   array   $data           Data from payment gateway
	 * @param   array   &$logData       Log data for payment api
	 *
	 * @return void
	 */
	public function onRedpaymentRequestAccept($paymentName, $extensionName, $ownerName, $data, &$logData)
	{
		if (!$this->isPaymentEnabled($paymentName, $extensionName, $ownerName))
		{
			return;
		}

		// Accept request
		$this->paymentHelper->handleAcceptRequest($extensionName, $ownerName, $data, $logData);

		// We call extension helper file to trigger afterHandleAcceptRequest method if needed
		RApiPaymentHelper::triggerExtensionHelperMethod($extensionName, 'afterHandleAcceptRequest', $ownerName, $paymentName, $data);
	}

	/**
	 * This method tries to make a cancel process
	 * This method is called when Payment gateway redirect customer on cancel button in Payment Gateway
	 *
	 * @param   string  $paymentName    Payment name
	 * @param   string  $extensionName  Extension name
	 * @param   string  $ownerName      Owner name
	 * @param   array   $data           Data from payment gateway
	 * @param   array   &$logData       Log data for payment api
	 *
	 * @return void
	 */
	public function onRedpaymentRequestCancel($paymentName, $extensionName, $ownerName, $data, &$logData)
	{
		if (!$this->isPaymentEnabled($paymentName, $extensionName, $ownerName))
		{
			return;
		}

		// Cancel request
		$this->paymentHelper->handleCancelRequest($extensionName, $ownerName, $data, $logData);

		// We call extension helper file to trigger afterHandleCancelRequest method if needed
		RApiPaymentHelper::triggerExtensionHelperMethod($extensionName, 'afterHandleCancelRequest', $ownerName, $paymentName, $data);
	}

	/**
	 * This method handles payment process for IPN or later notifications from Payment Gateway
	 *
	 * @param   string  $paymentName    Payment name
	 * @param   string  $extensionName  Name of the extension
	 * @param   string  $ownerName      Name of the owner
	 * @param   array   $data           Data to fill out Payment form
	 * @param   array   &$logData       Log data for payment api
	 *
	 * @return void
	 */
	public function onRedpaymentRequestCallback($paymentName, $extensionName, $ownerName, $data, &$logData)
	{
		if (!$this->isPaymentEnabled($paymentName, $extensionName, $ownerName))
		{
			return;
		}

		$logData = RApiPaymentHelper::generatePaymentLog(
			RApiPaymentStatus::getStatusCreated(),
			$data,
			JText::sprintf('LIB_REDCORE_PAYMENT_LOG_CALLBACK_MESSAGE', $this->paymentName)
		);

		// Process callback
		$this->paymentHelper->handleCallback($extensionName, $ownerName, $data, $logData);

		// Save payment log and update change for payment
		RApiPaymentHelper::saveNewPaymentLog($logData);

		// We call extension helper file to trigger afterHandleCallback method if needed
		RApiPaymentHelper::triggerExtensionHelperMethod($extensionName, 'afterHandleCallback', $ownerName, $paymentName, $data);
	}

	/**
	 * This method handles payment process for creating payments in Payment Gateway
	 *
	 * @param   string  $paymentName    Payment name
	 * @param   string  $extensionName  Name of the extension
	 * @param   string  $ownerName      Name of the owner
	 * @param   array   $data           Request data
	 * @param   array   &$logData       Log data
	 * @param   bool    &$isAccepted    If process is successful then this flag should be true
	 *
	 * @return void
	 */
	public function onRedpaymentRequestProcess($paymentName, $extensionName, $ownerName, $data, &$logData, &$isAccepted)
	{
		if (!$this->isPaymentEnabled($paymentName, $extensionName, $ownerName))
		{
			return;
		}

		$logData = RApiPaymentHelper::generatePaymentLog(
			RApiPaymentStatus::getStatusCreated(),
			$data,
			JText::sprintf('LIB_REDCORE_PAYMENT_LOG_PROCESS_MESSAGE', $this->paymentName)
		);

		// Handle process
		$this->paymentHelper->handleProcess($extensionName, $ownerName, $data, $logData, $isAccepted);

		// If plugin did not set the message text we will set it
		if (empty($logData['message_text']))
		{
			if ($isAccepted === true)
			{
				$logData['message_text'] = JText::sprintf('LIB_REDCORE_PAYMENT_LOG_ACCEPT_MESSAGE', $extensionName, $this->paymentName);
			}
			elseif ($isAccepted === false)
			{
				$logData['message_text'] = JText::sprintf('LIB_REDCORE_PAYMENT_LOG_CANCEL_MESSAGE', $extensionName, $this->paymentName);
			}
			else
			{
				$logData['message_text'] = JText::sprintf('LIB_REDCORE_PAYMENT_LOG_DEFAULT_MESSAGE', $extensionName, $this->paymentName);
			}
		}

		// Save payment log and update change for payment
		RApiPaymentHelper::saveNewPaymentLog($logData);

		// We call extension helper file to trigger afterHandleProcess method if needed
		RApiPaymentHelper::triggerExtensionHelperMethod($extensionName, 'afterHandleProcess', $ownerName, $paymentName, $data, $isAccepted);
	}

	/**
	 * Sets plugin parameters specific to given extension name (if extension have its own configuration)
	 *
	 * @param   string  $extensionName  Name of the extension
	 * @param   string  $ownerName      Name of the owner
	 *
	 * @return void
	 */
	protected function setRedpaymentOptions($extensionName, $ownerName)
	{
		$pluginOptions = RApiPaymentHelper::getPaymentParams($this->paymentName, $extensionName, $ownerName);
		$this->paymentHelper->pluginEnabled = (bool) $pluginOptions->state;
		$this->paymentHelper->params = $pluginOptions->params;
	}

	/**
	 * Loads Payment Helper object
	 *
	 * @return RApiPaymentPluginHelperPayment
	 */
	protected function loadRedpaymentHelper()
	{
		if (!$this->paymentHelper)
		{
			$reflector = new ReflectionClass(get_class($this));
			$helperPath   = dirname($reflector->getFileName());

			if (file_exists($helperPath . '/helpers/payment.php'))
			{
				require_once $helperPath . '/helpers/payment.php';

				$helperClass = 'PaymentHelper' . ucfirst($this->paymentName);
				$this->paymentHelper = new $helperClass($this->params);
			}
		}

		return $this->paymentHelper;
	}

	/**
	 * Sets plugin parameters specific to given extension name (if extension have its own configuration)
	 *
	 * @param   string  $paymentName    Payment name
	 * @param   string  $extensionName  Name of the extension
	 * @param   string  $ownerName      Name of the owner
	 *
	 * @return bool
	 */
	protected function isPaymentEnabled($paymentName, $extensionName, $ownerName)
	{
		if ($paymentName != $this->paymentName)
		{
			return false;
		}

		$this->setRedpaymentOptions($extensionName, $ownerName);

		// Plugin is disabled (optionally for this extension)
		if (!$this->paymentHelper->pluginEnabled)
		{
			return false;
		}

		return true;
	}
}
