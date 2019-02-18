<?php
/**
 * @package     Redcore
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * redCORE Payment plugin base Class
 *
 * @package     Redcore
 * @subpackage  Payment
 * @since       1.5
 */
abstract class RApiPaymentPluginHelperPayment extends JObject implements RApiPaymentPluginInterface
{
	/**
	 * Payment Name
	 * @var string
	 */
	public $paymentName = '';

	public $offlinePayment = false;

	/**
	 * Plugin parameters
	 * @var JRegistry
	 */
	public $params = null;

	/**
	 * URL to the payment gateway
	 * @var string
	 */
	public $paymentUrl = '';

	/**
	 * Path on host of the payment gateway to post request to (ex. '/cgi-bin/webscr')
	 * @var string
	 */
	protected $requestPath = '';

	/**
	 * Sandbox URL to the payment gateway
	 * @var string
	 */
	public $paymentUrlSandbox = '';

	/**
	 * Sandbox path on host of the payment gateway to post request to (ex. '/cgi-bin/webscr')
	 * @var string
	 */
	protected $requestPathSandbox = '';

	/**
	 * Port on FTP host of the payment gateway to post request to. Used only in fsockopen request method. (443 for https and 80 for http)
	 * @var string
	 */
	protected $fsockPort = '443';

	/**
	 * Plugin can be disabled for specific extension this is set in payment configuration
	 * @var bool
	 */
	public $pluginEnabled = true;

	/**
	 * This flag is sent to the layout where it can be used to auto submit the form.
	 * It can be overridden from passed parameters with $data['autoSubmit']
	 * @var bool
	 */
	public $autoSubmit = false;

	/**
	 * There are two types of calculating fee based on percentage:
	 * 'add' adds a percentage based fee to the total amount (ex. 100$ + 10% = 110$)
	 * 'include' Fee is calculated as a commission rate (ex. 100$ + include commission = 111,11$ that will result of giving shopper full amount of 100$)
	 * @var string
	 */
	public $paymentFeeType = 'include';

	/**
	 * Payment api will use given folder path to load custom (not official) omnipay payment classes
	 * @var string
	 */
	protected $omnipayCustomClassPath = '';

	/**
	 * If using omnipay this property must be the name of the omnipay payment gateway ex. Stripe
	 * @var string
	 */
	protected $omnipayGatewayName = '';

	/**
	 * Constructor
	 *
	 * @param   JRegistry  $params  Parameters from the plugin
	 *
	 * @since   1.5
	 */
	public function __construct($params = null)
	{
		$this->params = $params;
	}

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
	public function handleCallback($extensionName, $ownerName, $data, &$logData)
	{
		// This function should be implemented in the child class if needed
		// This method is not set as abstract because many of the payment gateways do not support it
		$logData['message_text'] = JText::sprintf('LIB_REDCORE_PAYMENT_LOG_METHOD_NOT_SUPPORTED', 'handleCallback', $this->paymentName);

		return false;
	}

	/**
	 * Handle the reception of notification from the payment gateway
	 * This method validates request that came from Payment gateway to check if it is valid and that it came through Payment gateway
	 *
	 * @param   string  $extensionName  Name of the extension
	 * @param   string  $ownerName      Name of the owner
	 * @param   array   $data           Request data
	 * @param   array   &$logData       Log data
	 * @param   bool    &$isAccepted    If process is successful then this flag should be true
	 *
	 * @return bool paid status
	 */
	public function handleProcess($extensionName, $ownerName, $data, &$logData, &$isAccepted)
	{
		// This function should be implemented in the child class if needed
		// This method is not set as abstract because many of the payment gateways do not support it
		$logData['message_text'] = JText::sprintf('LIB_REDCORE_PAYMENT_LOG_METHOD_NOT_SUPPORTED', 'handleProcess', $this->paymentName);
		$isAccepted = false;

		return false;
	}

	/**
	 * Handle the accept notification from the payment gateway
	 * This method is called when Payment gateway redirect customer on success but we still do not know if the payment is confirmed
	 *
	 * @param   string  $extensionName  Name of the extension
	 * @param   string  $ownerName      Name of the owner
	 * @param   array   $data           Request data
	 * @param   array   &$logData       Log data
	 *
	 * @return void
	 */
	public function handleAcceptRequest($extensionName, $ownerName, $data, &$logData)
	{
		// This function should be implemented in the child class if needed
		// This method is not set as abstract because many of the payment gateways do not support/need it
	}

	/**
	 * Handle the cancel notification from the payment gateway
	 * This method is called when Payment gateway redirect customer on cancel button
	 *
	 * @param   string  $extensionName  Name of the extension
	 * @param   string  $ownerName      Name of the owner
	 * @param   array   $data           Request data
	 * @param   array   &$logData       Log data
	 *
	 * @return void
	 */
	public function handleCancelRequest($extensionName, $ownerName, $data, &$logData)
	{
		// This function should be implemented in the child class if needed
		// This method is not set as abstract because many of the payment gateways do not support/need it
	}

	/**
	 * Handle the refund of the payment
	 * This method tries to call Refund process on the Payment plugin
	 *
	 * @param   string  $extensionName  Name of the extension
	 * @param   string  $ownerName      Name of the owner
	 * @param   object  $data           Payment data
	 * @param   array   &$logData       Log data
	 * @param   bool    &$isRefunded    If process is successful then this flag should be true
	 *
	 * @return bool paid status
	 */
	public function handleRefundPayment($extensionName, $ownerName, $data, &$logData, &$isRefunded)
	{
		// This function should be implemented in the child class if needed
		// This method is not set as abstract because many of the payment gateways do not support it
		$logData['message_text'] = JText::sprintf('LIB_REDCORE_PAYMENT_LOG_METHOD_NOT_SUPPORTED', 'handleRefundPayment', $this->paymentName);
		$isRefunded = false;

		return false;
	}

	/**
	 * Handle the capture of the payment
	 * This method tries to call Capture process on the Payment plugin
	 *
	 * @param   string  $extensionName  Name of the extension
	 * @param   string  $ownerName      Name of the owner
	 * @param   object  $data           Payment data
	 * @param   array   &$logData       Log data
	 * @param   bool    &$isCaptured    If process is successful then this flag should be true
	 *
	 * @return bool paid status
	 */
	public function handleCapturePayment($extensionName, $ownerName, $data, &$logData, &$isCaptured)
	{
		// This function should be implemented in the child class if needed
		// This method is not set as abstract because many of the payment gateways do not support it
		$logData['message_text'] = JText::sprintf('LIB_REDCORE_PAYMENT_LOG_METHOD_NOT_SUPPORTED', 'handleCapturePayment', $this->paymentName);
		$isCaptured = false;

		return false;
	}

	/**
	 * Handle the deletion of the payment on Payment Gateway
	 * This method tries to call Delete process on the Payment plugin
	 *
	 * @param   string  $extensionName  Name of the extension
	 * @param   string  $ownerName      Name of the owner
	 * @param   object  $data           Payment data
	 * @param   array   &$logData       Log data
	 * @param   bool    &$isDeleted     If process is successful then this flag should be true
	 *
	 * @return bool paid status
	 */
	public function handleDeletePayment($extensionName, $ownerName, $data, &$logData, &$isDeleted)
	{
		// This function should be implemented in the child class if needed
		// This method is not set as abstract because many of the payment gateways do not support it
		$logData['message_text'] = JText::sprintf('LIB_REDCORE_PAYMENT_LOG_METHOD_NOT_SUPPORTED', 'handleDeletePayment', $this->paymentName);
		$isDeleted = false;

		return false;
	}

	/**
	 * Check for new status change from Payment Gateway
	 *
	 * @param   int    $paymentId  Payment Id
	 * @param   array  &$status    Payment check status
	 *
	 * @return array
	 */
	public function handleCheckPayment($paymentId, &$status)
	{
		$status = array(
			'message' => JText::sprintf('LIB_REDCORE_PAYMENT_ERROR_MANUAL_PAYMENT_CHECK_NOT_IMPLEMENTED', $this->paymentName),
			'type'    => 'error'
		);
	}

	/**
	 * Process data before outputting it to the form
	 *
	 * @param   string  $extensionName  Extension name
	 * @param   string  $ownerName      Owner name
	 * @param   array   $data           Data for the payment form
	 * @param   object  $payment        Payment object from saved payment table
	 *
	 * @return array on success
	 */
	public function preparePaymentSubmitData($extensionName, $ownerName, $data, $payment)
	{
		return $data;
	}

	/**
	 * Prepare data for omnipay from payment object
	 *
	 * @param   object  $payment  Payment object from saved payment table
	 *
	 * @return array
	 */
	public function prepareOmnipayData($payment)
	{
		$data = !is_array($payment) ? ArrayHelper::fromObject($payment) : $payment;

		$data['amount']     = $data['amount_total'];
		$data['transactionId'] = $data['transaction_id'];
		$data['returnUrl'] = $this->getResponseUrl($payment, 'callback');
		$data['cancelUrl'] = $this->getResponseUrl($payment, 'cancel');

		return $data;
	}

	/**
	 * Create new payment
	 *
	 * @param   string  $extensionName  Extension name
	 * @param   string  $ownerName      Owner name
	 * @param   array   $data           Data for the payment
	 *
	 * @return int|boolean Id of the payment or false
	 */
	public function createPayment($extensionName, $ownerName, $data)
	{
		// Is payment new
		$isNew = empty($data['id']);

		// Only calculate amount if set in provided data
		if (isset($data['calculate_total_amount_manually_redpayment']) && $data['calculate_total_amount_manually_redpayment'] === true)
		{
			// Calculate price
			$data['amount_total'] = (float) $data['amount_original'];

			// Add tax
			if (!empty($data['amount_order_tax']))
			{
				$data['amount_total'] += (float) $data['amount_order_tax'];
			}

			// Add shipping
			if (!empty($data['amount_shipping']))
			{
				$data['amount_total'] += (float) $data['amount_shipping'];
			}

			// Calculate payment fee
			$paymentFee = $this->getPaymentFee($data['amount_total'], $data['currency']);
			$data['amount_payment_fee'] = $paymentFee;

			$data['amount_total'] += $data['amount_payment_fee'];
		}
		elseif (empty($data['amount_total']) && isset($data['amount_original']))
		{
			$data['amount_total'] = (float) $data['amount_original'];
		}

		// Set cancel URL
		if (empty($data['url_cancel']))
		{
			$data['url_cancel'] = JUri::root() . 'index.php?option=' . $data['extension_name'];
		}

		// Set accept URL
		if (empty($data['url_accept']))
		{
			$data['url_accept'] = JUri::root() . 'index.php?option=' . $data['extension_name'];
		}

		// Set sandbox flag
		if (empty($data['sandbox']))
		{
			$data['sandbox'] = $this->params->get('sandbox', 0);
		}

		// Set order name
		if (empty($data['order_name']))
		{
			$data['order_name'] = $data['order_id'];
		}

		// Set payment name
		if (empty($data['payment_name']))
		{
			$data['payment_name'] = $this->paymentName;
		}

		// Set extension name
		if (empty($data['extension_name']))
		{
			$data['extension_name'] = $extensionName;
		}

		// Set owner name
		if (empty($data['owner_name']))
		{
			$data['owner_name'] = $ownerName;
		}

		// This field sets how many times does the plugin try to get response from Payment Gateway for the transaction status.
		if (!isset($data['retry_counter']))
		{
			$data['retry_counter'] = $this->params->get('retry_counter', RBootstrap::getConfig('payment_number_of_payment_check_retries', 30));
		}

		$paymentId = RApiPaymentHelper::updatePaymentData($data);

		if (empty($paymentId))
		{
			return false;
		}

		$data['id'] = $paymentId;
		$updateMainPaymentStatus = true;

		if (empty($data['payment_log']))
		{
			$paymentStatus = RApiPaymentStatus::getStatusCreated();

			if (!$isNew)
			{
				// If the payment not new and any other log not found, then current event must inherit the last payment status
				$prevLog = RApiPaymentHelper::getLastPaymentLog($paymentId);

				if ($prevLog)
				{
					$paymentStatus = $prevLog->status;
					$updateMainPaymentStatus = false;
				}
			}

			$data['payment_log'] = RApiPaymentHelper::generatePaymentLog(
				$paymentStatus,
				$data,
				JText::sprintf('LIB_REDCORE_PAYMENT_LOG_' . ($isNew ? 'CREATE' : 'UPDATE') . '_MESSAGE', $data['extension_name'], $this->paymentName)
			);
		}

		RApiPaymentHelper::saveNewPaymentLog($data['payment_log'], $updateMainPaymentStatus);

		return $paymentId;
	}

	/**
	 * Returns details about the payment
	 *
	 * @param   string  $extensionName  Extension name (ex: com_content)
	 * @param   array   $orderData      Extension Order data
	 *
	 * @return object
	 */
	protected function getPaymentByExtensionOrderData($extensionName, $orderData)
	{
		$orderId = empty($orderData['order_id']) ? $orderData['id'] : $orderData['order_id'];
		$payment = RApiPaymentHelper::getPaymentByExtensionId($extensionName, $orderId);
		$restrictedData = array(
			'id', 'extension_name', 'payment_name', 'sandbox', 'created_date', 'modified_date', 'confirmed_date', 'transaction_id',
			'amount_paid', 'coupon_code', 'customer_note', 'status'
		);

		if (!$payment)
		{
			array_push($restrictedData, 'amount_total');

			$ownerName = !empty($orderData['owner_name']) ? $orderData['owner_name'] : '';
			$createData = array();

			foreach ($orderData as $key => $value)
			{
				if (!in_array($key, $restrictedData))
				{
					$createData[$key] = $orderData[$key];
				}
			}

			// Create new payment based on order data
			$this->createPayment($extensionName, $ownerName, $createData);

			$payment = RApiPaymentHelper::getPaymentByExtensionId($extensionName, $orderId);
		}
		else
		{
			array_push($restrictedData, 'amount_original');

			// We will update payment with provided data if it is different than originally provided
			$ownerName = !empty($orderData['owner_name']) ? $orderData['owner_name'] : $payment->owner_name;
			$updateData = ArrayHelper::fromObject($payment);

			foreach ($orderData as $key => $value)
			{
				if (!in_array($key, $restrictedData))
				{
					$updateData[$key] = $orderData[$key];
				}
			}

			// Create new payment based on order data
			$this->createPayment($extensionName, $ownerName, $updateData);
		}

		return $payment;
	}

	/**
	 * Gets request method as it is defined in plugin, some servers do not support specific request methods
	 *
	 * @return string
	 */
	public function getRequestMethod()
	{
		// If defined on a level of the payment plugin (no alternative methods but use specific one)
		if (!empty($this->requestMethod))
		{
			return $this->requestMethod;
		}
		// If defined in payment plugin parameters we use this one then
		elseif ($this->params->get('payment_request_method', null) !== null)
		{
			return $this->params->get('payment_request_method', 'curl');
		}

		// If no alternative, we use global settings for request method
		return RBootstrap::getConfig('payment_request_method', 'curl');
	}

	/**
	 * Gets the form action URL for the payment
	 *
	 * @return string
	 */
	protected function getPaymentURL()
	{
		$sandbox = $this->params->get('sandbox', 0);

		if ($sandbox && !empty($this->paymentUrlSandbox))
		{
			return $this->paymentUrlSandbox . $this->requestPathSandbox;
		}
		else
		{
			return $this->paymentUrl . $this->requestPath;
		}
	}

	/**
	 * Gets the Merchant ID (usually the email address)
	 *
	 * @return string
	 */
	protected function getMerchantID()
	{
		$sandbox = $this->params->get('sandbox', 0);

		if ($sandbox && $this->params->get('merchant_id_sandbox', ''))
		{
			return $this->params->get('merchant_id_sandbox', '');
		}
		else
		{
			return $this->params->get('merchant_id', '');
		}
	}

	/**
	 * Gets the URL for Payment api
	 *
	 * @param   object  $payment  Payment data
	 *
	 * @return string
	 */
	protected function getPaymentApiUrl($payment)
	{
		return JUri::root()
			. 'index.php?option=com_redcore&api=payment'
			. '&payment_id=' . $payment->id
			. '&order_id=' . $payment->order_id
			. '&payment_name=' . $payment->payment_name
			. '&owner_name=' . $payment->owner_name
			. '&extension_name=' . $payment->extension_name;
	}

	/**
	 * returns state uri object (notify, cancel, etc...)
	 *
	 * @param   object  $payment  Payment data
	 * @param   string  $state    The state for the url
	 *
	 * @return string
	 */
	protected function getResponseUrl($payment, $state)
	{
		$uri = $this->getPaymentApiUrl($payment);

		switch ($state)
		{
			case 'cancel':
				$uri .= '&task=cancel';
				break;
			case 'accept':
				$uri .= '&task=accept';
				break;
			case 'process':
				$uri .= '&task=process';
				break;
			case 'callback':
				$uri .= '&task=callback';
				break;
			default:
				$uri .= '&task=callback';
				break;
		}

		return $uri;
	}

	/**
	 * Check for notifications from Payment Gateway
	 *
	 * @param   string  $data  Data to post
	 *
	 * @return string
	 */
	public function getRequestFromGateway($data = '')
	{

		if ($this->getRequestMethod() == 'fsockopen')
		{
			if ($this->params->get('sandbox', 0) && !empty($this->paymentUrlSandbox))
			{
				$requestPath = $this->requestPathSandbox;
				$paymentUrl = $this->paymentUrlSandbox;
			}
			else
			{
				$requestPath = $this->requestPath;
				$paymentUrl = $this->paymentUrl;
			}

			$http_request  = "POST $requestPath HTTP/1.0\r\n";
			$http_request .= "Host: $paymentUrl\r\n";
			$http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
			$http_request .= "Content-Length: " . strlen($data) . "\r\n";
			$http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
			$http_request .= "\r\n";
			$http_request .= $data;

			$response = '';

			if (($fs = @fsockopen($paymentUrl, $this->fsockPort, $errno, $errstr, 10)) == false )
			{
				JFactory::getApplication()->enqueueMessage('LIB_REDCORE_PAYMENT_ERROR_FSOCK_COULD_NOT_OPEN', 'error');

				return false;
			}

			fwrite($fs, $http_request);

			while (!feof($fs))
			{
				// One TCP-IP packet
				$response .= fgets($fs, 1160);
			}

			fclose($fs);
		}
		else
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->getPaymentURL());
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

			$response = curl_exec($ch);
			curl_close($ch);
		}

		return $response;
	}

	/**
	 * Check if we can use this plugin for given currency
	 *
	 * @param   string  $currencyCode  3 letters iso code
	 *
	 * @return true if plugin supports this currency
	 */
	public function currencyIsAllowed($currencyCode)
	{
		$allowed = trim($this->params->get('allowed_currencies'));

		if (!$allowed) // Allow everything
		{
			return true;
		}

		// Otherwise returns only currencies specified in allowed_currencies plugin parameters
		$allowed = explode(',', $allowed);
		$allowed = array_map('trim', $allowed);

		if (!in_array($currencyCode, $allowed))
		{
			return false;
		}

		return true;
	}

	/**
	 * Check if the currency is supported by the gateway (otherwise might require conversion)
	 *
	 * @param   string  $currencyCode  3 letters iso code
	 *
	 * @return true if currency is supported
	 */
	protected function currencyIsSupported($currencyCode)
	{
		return true;
	}

	/**
	 * Convert price to another currency @todo Bring the logic from redshop to a plugin maybe?
	 *
	 * @param   float   $price         price to convert
	 * @param   string  $currencyFrom  currency to convert from
	 * @param   string  $currencyTo    currency to convert to
	 *
	 * @return float converted price
	 */
	protected function convertPrice($price, $currencyFrom, $currencyTo)
	{
		JPluginHelper::importPlugin('currencyconverter');

		$result = false;
		JFactory::getApplication()->triggerEvent('onCurrencyConvert', array($price, $currencyFrom, $currencyTo, &$result));

		return $result;
	}

	/**
	 * Display or redirect to the payment page for the gateway
	 *
	 * @param   string  $extensionName  Extension name
	 * @param   string  $ownerName      Owner name
	 * @param   array   $data           Data for the payment form
	 * @param   string  &$html          Html output
	 *
	 * @return string
	 */
	public function displayPayment($extensionName, $ownerName, $data, &$html)
	{
		if (is_object($data))
		{
			$data = ArrayHelper::fromObject($data);
		}

		if (empty($data['order_id']))
		{
			JFactory::getApplication()->enqueueMessage(JText::sprintf('LIB_REDCORE_PAYMENT_ERROR_ORDER_ID_NOT_PROVIDED', $this->paymentName), 'error');
		}

		$payment = $this->getPaymentByExtensionOrderData($extensionName, $data);

		if (!isset($data['hiddenFields']))
		{
			// Prepare the data
			$data['hiddenFields'] = $this->preparePaymentSubmitData($extensionName, $ownerName, $data, $payment);
		}

		// Handle shipping data @todo we will insert static method here to call up shipping api
		JPluginHelper::importPlugin('redshipping');
		JFactory::getApplication()->triggerEvent('onRedshippingPrepareSubmitData', array($extensionName, $ownerName, &$data, $payment));

		$paths = $this->getPaymentLayoutPaths($extensionName);

		$fullPath = JPath::find($paths, 'form/form.php');

		$displayData = array(
			'options' => array(
				'paymentData'   => $data,
				'extensionName' => $extensionName,
				'ownerName'     => $ownerName,
				'paymentName'   => $this->paymentName,
				'paymentTitle'  => $this->params->get('payment_title', $this->paymentName),
				'action'        => $this->getPaymentURL(),
				'autoSubmit'    => isset($data['autoSubmit']) ? $data['autoSubmit'] : $this->autoSubmit,
				'params'        => $this->params,
				'payment'       => $payment,
				'formName'      => 'redpaymentForm' . $payment->id,
			)
		);

		$html .= $this->renderLayout($fullPath, $displayData);

		return $html;
	}

	/**
	 * Get layout paths including plugin path and path to template. We are not using JLayouts because they do not support plugin layouts auto find
	 *
	 * @param   string  $extensionName  Extension name
	 *
	 * @return  array
	 */
	protected function getPaymentLayoutPaths($extensionName)
	{
		$app = JFactory::getApplication();
		$paths = array();

		// Extension Template override
		$paths[] = JPATH_THEMES . '/' . $app->getTemplate() . '/html/layouts/' . $extensionName . '/plugins/redpayment/' . $this->paymentName;

		// Plugin Template override
		$paths[] = JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/layouts/plugins/redpayment/' . $this->paymentName;

		// Extension override
		$paths[] = JPATH_SITE . '/components/' . $extensionName . '/layouts/plugins/redpayment/' . $this->paymentName;

		// Root Joomla override
		$paths[] = JPATH_ROOT . '/layouts/plugins/redpayment/' . $this->paymentName;

		// Current Plugin
		$paths[] = JPATH_PLUGINS . '/redpayment/' . $this->paymentName;

		// If not found plugin then use default layout
		$paths[] = JPATH_LIBRARIES . '/redcore/layouts/redpayment';

		return $paths;
	}

	/**
	 * Method to render the layout.
	 *
	 * @param   string  $path         Path to the layout file
	 * @param   array   $displayData  Object which properties are used inside the layout file to build displayed output
	 *
	 * @return  string  The necessary HTML to display the layout
	 *
	 * @since   1.0
	 */
	protected function renderLayout($path, $displayData)
	{
		$layoutOutput = '';

		// If there exists such a layout file, include it and collect its output
		if (!empty($path))
		{
			ob_start();
			include $path;
			$layoutOutput = ob_get_contents();
			ob_end_clean();
		}

		return $layoutOutput;
	}

	/**
	 * Calculates fee based on given amount and currency
	 *
	 * @param   float   $amount    Amount to calculate fee
	 * @param   string  $currency  Iso 4217 3 letters currency code
	 *
	 * @return float
	 */
	public function getPaymentFee($amount, $currency = '')
	{
		$paymentFee = $this->params->get('payment_fee', 0);
		$paymentFeeType = $this->params->get('payment_fee_type', $this->paymentFeeType);

		if (strpos($paymentFee, '%') === false || $paymentFee == 0)
		{
			return (float) $paymentFee;
		}

		$paymentFee = (float) $paymentFee;

		if ($paymentFeeType == 'add')
		{
			// Adds a percentage based fee to the total amount
			$fee = $amount * ($paymentFee / 100);
		}
		else
		{
			// The fee is calculated as a commission rate
			$fee = ($amount / (1 - ($paymentFee / 100))) - $amount;
		}

		return $fee;
	}

	/**
	 * Load omnipay library and this payment gateway classes if needed
	 *
	 * @return boolean
	 */
	public function loadOmnipay()
	{
		// Load omnipay libraries
		$omnipayLibraries = JPATH_LIBRARIES . '/redpayment_omnipay/vendor/autoload.php';

		if (!file_exists($omnipayLibraries))
		{
			return false;
		}

		$loader = require $omnipayLibraries;

		// If custom class path is set we will load additional classes for omnipay
		if (!empty($this->omnipayCustomClassPath))
		{
			$map = array(
				'Omnipay\\' . $this->omnipayGatewayName . '\\' => array($this->omnipayCustomClassPath),
			);

			foreach ($map as $namespace => $path)
			{
				$loader->setPsr4($namespace, $path);
			}

			$loader->register(true);
		}

		return true;
	}

	/**
	 * Get the payment amount as an integer.
	 *
	 * @param   float   $amount    Amount
	 * @param   string  $currency  Iso 4217 3 letters currency code
	 *
	 * @return integer
	 */
	public function getAmountInteger($amount, $currency)
	{
		return (int) round($amount * pow(10, RHelperCurrency::getPrecision($currency)));
	}

	/**
	 * Get the payment amount as an float.
	 *
	 * @param   int     $amount    Amount
	 * @param   string  $currency  Iso 4217 3 letters currency code
	 *
	 * @return float
	 */
	public function getAmountToFloat($amount, $currency)
	{
		return (float) round($amount / pow(10, RHelperCurrency::getPrecision($currency)));
	}
}
