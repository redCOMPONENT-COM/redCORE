<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('JPATH_BASE') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Class to represent a Payment standard object.
 *
 * @since  1.5
 */
class RApiPaymentPayment extends RApi
{
	/**
	 * Payment server response
	 *
	 * @var    string  HTML value for Payment operation
	 * @since  1.5
	 */
	public $paymentResponse = null;

	/**
	 * @var    string  Payment name
	 */
	public $paymentName = null;

	/**
	 * @var    string  Extension name
	 */
	public $extensionName = null;

	/**
	 * @var    string  Owner name
	 */
	public $ownerName = null;

	/**
	 * @var    string  Order Id
	 */
	public $orderId = null;

	/**
	 * @var    int  Payment Id
	 */
	public $paymentId = 0;

	/**
	 * @var    object  Payment Object
	 */
	public $paymentObject = 0;

	/**
	 * @var    object  Output data that will be rendered out on render
	 */
	public $outputData = null;

	/**
	 * @var    array|object  Request data from the payment gateway
	 */
	public $requestData = null;

	/**
	 * Method to instantiate the file-based api call.
	 *
	 * @param   mixed  $options  Optional custom options to load. JRegistry or array format
	 *
	 * @throws Exception
	 * @since   1.5
	 */
	public function __construct($options = null)
	{
		parent::__construct($options);

		JPluginHelper::importPlugin('redcore');
		JPluginHelper::importPlugin('redpayment');

		// Init Environment
		$this->triggerFunction('setApiOperation');

		$dataGet             = $this->options->get('dataGet', array());
		$this->paymentName   = isset($dataGet->payment_name) ? $dataGet->payment_name : null;
		$this->extensionName = isset($dataGet->extension_name) ? $dataGet->extension_name : null;
		$this->ownerName     = isset($dataGet->owner_name) ? $dataGet->owner_name : null;
		$this->orderId       = isset($dataGet->order_id) ? $dataGet->order_id : null;
		$this->paymentId     = isset($dataGet->payment_id) ? (int) $dataGet->payment_id : 0;

		// Set initial status code
		$this->setStatusCode($this->statusCode);

		$this->requestData = $this->options->get('data', array());

		if (is_object($this->requestData))
		{
			$this->requestData = ArrayHelper::fromObject($this->requestData);
		}
	}

	/**
	 * Set Method for Api to be performed
	 *
	 * @return  RApi
	 *
	 * @since   1.5
	 */
	public function setApiOperation()
	{
		$task = $this->options->get('task', '');

		// Set proper operation for given method
		switch ($task)
		{
			case 'accept':
				$method = 'accept';
				break;
			case 'cancel':
				$method = 'cancel';
				break;
			case 'callback':
				$method = 'callback';
				break;
			case 'process':
				$method = 'process';
				break;

			default:
				$method = 'callback';
				break;
		}

		$this->operation = strtolower($method);

		return $this;
	}

	/**
	 * Execute the Api operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.5
	 * @throws  Exception
	 */
	public function execute()
	{
		// We do not want some unwanted text to appear before output
		ob_start();

		try
		{
			switch ($this->operation)
			{
				case 'accept':
					$this->triggerFunction('apiAccept');
					break;

				case 'cancel':
					$this->triggerFunction('apiCancel');
					break;

				case 'callback':
					$this->triggerFunction('apiCallback');
					break;

				case 'process':
					$this->triggerFunction('apiProcess');
					break;

				default:
					$this->triggerFunction('apiCallback');
				break;
			}

			$messages = JFactory::getApplication()->getMessageQueue();

			$executionErrors = ob_get_contents();
			ob_end_clean();
		}
		catch (Exception $e)
		{
			$executionErrors = ob_get_contents();
			ob_end_clean();

			throw $e;
		}

		if (!empty($executionErrors))
		{
			$messages[] = array('message' => $executionErrors, 'type' => 'notice');
		}

		if (!empty($messages))
		{
			$this->messages = $messages;
		}

		return $this;
	}

	/**
	 * Execute the Api Accept operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 */
	public function apiAccept()
	{
		$app     = JFactory::getApplication();
		$payment = $this->getPayment();

		$logData = RApiPaymentHelper::generatePaymentLog(
			RApiPaymentStatus::getStatusProcessed(),
			$this->requestData,
			JText::sprintf('LIB_REDCORE_PAYMENT_LOG_ACCEPT_MESSAGE', $this->extensionName, $this->paymentName)
		);

		// This method can process data from payment request more if needed
		$app->triggerEvent('onRedpaymentRequestAccept', array($this->paymentName, $this->extensionName, $this->ownerName, $this->requestData, &$logData));

		// Save payment log and do not update change for payment
		RApiPaymentHelper::saveNewPaymentLog($logData);

		$redirect = !empty($payment->url_accept) ? $payment->url_accept : JUri::root() . 'index.php?option=' . $payment->extension_name;

		// Redirect to extension Accept URL
		$app->redirect($redirect);
		$app->close();
	}

	/**
	 * Execute the Api Cancel operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 */
	public function apiCancel()
	{
		$app     = JFactory::getApplication();
		$payment = $this->getPayment();

		$logData = RApiPaymentHelper::generatePaymentLog(
			RApiPaymentStatus::getStatusCreated(),
			$this->requestData,
			JText::sprintf('LIB_REDCORE_PAYMENT_LOG_CANCEL_MESSAGE', $this->extensionName, $this->paymentName)
		);

		// This method can process data from payment request more if needed
		$app->triggerEvent('onRedpaymentRequestCancel', array($this->paymentName, $this->extensionName, $this->ownerName, $this->requestData, &$logData));

		// Save payment log and do not update change for payment
		RApiPaymentHelper::saveNewPaymentLog($logData);

		$redirect = !empty($payment->url_cancel) ? $payment->url_cancel : JUri::root() . 'index.php?option=' . $payment->extension_name;

		// Redirect to extension Cancel URL
		$app->redirect($redirect);
		$app->close();
	}

	/**
	 * Execute the Api Callback operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 */
	public function apiCallback()
	{
		$app = JFactory::getApplication();
		$this->getPayment();
		$logData           = array();
		$logData['status'] = RApiPaymentStatus::getStatusUndefined();

		// This method can process data from payment request more if needed
		$app->triggerEvent('onRedpaymentRequestCallback', array($this->paymentName, $this->extensionName, $this->ownerName, $this->requestData, &$logData));

		$this->outputData = $logData;

		return $this;
	}

	/**
	 * Execute the Api Process operation.
	 * Process operation is used on a Payment gateways that require direct request query without interface (ex. credit cards)
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 */
	public function apiProcess()
	{
		$app        = JFactory::getApplication();
		$payment    = $this->getPayment();
		$isAccepted = null;
		$logData    = null;

		// This method can process data from payment request
		$app->triggerEvent(
			'onRedpaymentRequestProcess',
			array($this->paymentName, $this->extensionName, $this->ownerName, $this->requestData, &$logData, &$isAccepted)
		);

		if ($isAccepted)
		{
			$redirect = !empty($payment->url_accept) ? $payment->url_accept : JUri::root() . 'index.php?option=' . $payment->extension_name;

			// Redirect to extension Accept URL
			$app->redirect($redirect);
		}
		else
		{
			$redirect = !empty($payment->url_cancel) ? $payment->url_cancel : JUri::root() . 'index.php?option=' . $payment->extension_name;

			// Redirect to extension Cancel URL
			$app->redirect($redirect);
		}

		$app->close();
	}

	/**
	 * Gets payment for current operation
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 */
	public function getPayment()
	{
		if (!empty($this->paymentObject))
		{
			return $this->paymentObject;
		}

		if (!empty($this->paymentId))
		{
			$this->paymentObject = RApiPaymentHelper::getPaymentById($this->paymentId);
		}

		if (empty($this->paymentObject) && (!empty($this->orderId) && !empty($this->extensionName)))
		{
			$this->paymentObject = RApiPaymentHelper::getPaymentByExtensionId($this->extensionName, $this->orderId);
		}

		// Set up default variables if not set
		if ($this->paymentObject)
		{
			if (empty($this->paymentName))
			{
				$this->paymentName = $this->paymentObject->payment_name;
			}

			$this->extensionName = $this->paymentObject->extension_name;
			$this->ownerName     = $this->paymentObject->owner_name;
			$this->orderId       = $this->paymentObject->order_id;
			$this->paymentId     = $this->paymentObject->id;
		}

		return $this->paymentObject;
	}

	/**
	 * Method to send the application response to the client.  All headers will be sent prior to the main
	 * application output data.
	 *
	 * @return  void
	 *
	 * @since   1.5
	 */
	public function render()
	{
		$documentOptions = array(
			'absoluteHrefs' => $this->options->get('absoluteHrefs', false),
			'documentFormat' => 'json',
		);

		$body               = $this->getBody();
		JFactory::$document = new RApiPaymentDocumentDocument($documentOptions);
		$body               = $this->triggerFunction('prepareBody', $body);

		// Push results into the document.
		JFactory::$document
			->setApiObject($this)
			->setBuffer($body)
			->render(false);
	}

	/**
	 * Method to fill response with requested data
	 *
	 * @return  string  Api call output
	 *
	 * @since   1.5
	 */
	public function getBody()
	{
		return $this->outputData;
	}

	/**
	 * Prepares body for response
	 *
	 * @param   string  $message  The return message
	 *
	 * @return  string	The message prepared
	 *
	 * @since   1.5
	 */
	public function prepareBody($message)
	{
		return $message;
	}

	/**
	 * Calls method from helper file if exists or method from this class,
	 * Additionally it Triggers plugin call for specific function in a format RApiHalFunctionName
	 *
	 * @param   string  $functionName  Function name
	 *
	 * @return mixed Result from callback function
	 */
	public function triggerFunction($functionName)
	{
		$apiHelperClass = RApiPaymentHelper::getExtensionHelperObject($this->extensionName);
		$args           = func_get_args();

		// Remove function name from arguments
		array_shift($args);

		// PHP 5.3 workaround
		$temp = array();

		foreach ($args as &$arg)
		{
			$temp[] = &$arg;
		}

		// We will add this instance of the object as last argument for manipulation in plugin and helper
		$temp[] = &$this;

		JFactory::getApplication()->triggerEvent('RApiRedpaymentBefore' . $functionName, array($functionName, $temp));

		// Checks if that method exists in helper file and executes it
		if (method_exists($apiHelperClass, $functionName))
		{
			call_user_func_array(array($apiHelperClass, $functionName), $temp);
		}

		$result = call_user_func_array(array($this, $functionName), $temp);

		JFactory::getApplication()->triggerEvent('RApiRedpaymentAfter' . $functionName, $temp);

		return $result;
	}
}
