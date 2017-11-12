<?php
/**
 * @package     Redcore
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Handles Paypal payments
 *
 * @package     Redcore
 * @subpackage  Payment.epay
 * @since       2.5
 */
class PaymentHelperPaypal extends RApiPaymentPluginHelperPayment
{
	/**
	 * URL to the payment gateway
	 * @var string
	 */
	public $paymentUrl = 'https://www.paypal.com';

	/**
	 * Path on host of the payment gateway to post request to (ex. '/cgi-bin/webscr')
	 * @var string
	 */
	protected $requestPath = '/cgi-bin/webscr';

	/**
	 * Sandbox URL to the payment gateway
	 * @var string
	 */
	public $paymentUrlSandbox = 'https://www.sandbox.paypal.com';

	/**
	 * Sandbox path on host of the payment gateway to post request to (ex. '/cgi-bin/webscr')
	 * @var string
	 */
	protected $requestPathSandbox = '/cgi-bin/webscr';

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
		$params = array(
			"cmd"                   => "_xclick",
			"business"              => $this->getMerchantID(),
			"receiver_email"        => $this->getMerchantID(),
			"item_name"             => $payment->order_name,
			"no_shipping"           => '1',
			"invoice"               => $payment->order_id,
			"item_number"           => $payment->id,
			"tax_cart"              => $payment->amount_order_tax,
			"amount"                => $payment->amount_total,
			"currency_code"         => $payment->currency,
			"return"                => $this->getResponseUrl($payment, 'accept'),
			"notify_url"            => $this->getResponseUrl($payment, 'callback'),
			"cancel_return"         => $this->getResponseUrl($payment, 'cancel'),
			"undefined_quantity"    => '0',
			"no_note"               => '1',
			"rm"                    => '2',
			'charset'               => 'utf-8',
			'bn'                    => 'redCOMPONENT_SP',
		);

		// Handle user data
		$paramsFromData = array('first_name', 'last_name', 'address1', 'city', 'country', 'zip', 'email', 'night_phone_b');

		foreach ($paramsFromData as $dataParam)
		{
			if (isset($data[$dataParam]))
			{
				$params[$dataParam] = $data[$dataParam];
			}
		}

		// Handle items data
		if (isset($data['items']) && is_array($data['items']))
		{
			foreach ($data['items'] as $index => $item)
			{
				foreach ($item as $name => $value)
				{
					$params[$name . '_' . $index] = $value;
				}
			}
		}

		return $params;
	}

	/**
	 * Handle the reception of notification from the payment gateway
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
		if (version_compare(JVERSION, 3) >= 0)
		{
			$post = JFactory::getApplication()->input->post->getArray();
		}
		else
		{
			$post = JRequest::get('post');
		}

		$postData = array();

		// Read the post from PayPal system and add 'cmd'
		$postData[] = 'cmd=_notify-validate';

		foreach ($post as $key => $value)
		{
			$value      = urlencode(stripslashes($value));
			$postData[] = "$key=$value";
		}

		$request = implode('&', $postData);

		$response = $this->getRequestFromGateway($request);

		if (strcmp($response, "VERIFIED") == 0)
		{
			/*
			 Check the payment_status is Completed
			   check that txn_id has not been previously processed
			   check that receiver_email is your Primary PayPal email
			   check that payment_amount/payment_currency are correct */

			// Remap order_id
			$data['order_id'] = $data['invoice'];

			$payment = $this->getPaymentByExtensionOrderData($extensionName, $data);

			if ($post['mc_gross'] != $payment->amount_total)
			{
				$statusText  = JText::sprintf(
					'LIB_REDCORE_PAYMENT_ERROR_PRICE_MISMATCH', $extensionName, $this->paymentName, $payment->amount_total, $post['mc_gross']
				);
				RApiPaymentHelper::logToFile(
					$this->paymentName,
					$extensionName,
					$data,
					$isValid = false,
					$statusText
				);

				$logData['status']       = RApiPaymentStatus::getStatusCreated();
				$logData['message_text'] = $statusText;

				return false;
			}
			elseif ($post['mc_currency'] != $payment->currency)
			{
				$statusText  = JText::sprintf(
					'LIB_REDCORE_PAYMENT_ERROR_CURRENCY_MISMATCH', $extensionName, $this->paymentName, $payment->currency, $post['mc_currency']
				);
				RApiPaymentHelper::logToFile(
					$this->paymentName,
					$extensionName,
					$data,
					$isValid = false,
					$statusText
				);

				$logData['status']       = RApiPaymentStatus::getStatusCreated();
				$logData['message_text'] = $statusText;

				return false;
			}

			// We are clear to log successful payment log now
			// Update logData with payment id
			$logData['payment_id'] = $payment->id;

			// Paypal have very similar structure of Status response so we can actually get them directly
			$logData['status'] = RApiPaymentStatus::getStatus($post['payment_status']);

			if ($logData['status'] == RApiPaymentStatus::getStatusCompleted())
			{
				$statusText = JText::sprintf('LIB_REDCORE_PAYMENT_SUCCESSFUL', $extensionName, $this->paymentName);
			}
			else
			{
				$statusText = JText::sprintf('LIB_REDCORE_PAYMENT_CALLBACK_STATUS', $extensionName, $logData['status'], $this->paymentName);
			}

			RApiPaymentHelper::logToFile(
				$this->paymentName,
				$extensionName,
				$data,
				$isValid = true,
				$statusText
			);
		}
		elseif (strcmp($response, "INVALID") == 0)
		{
			$statusText  = JText::sprintf('LIB_REDCORE_PAYMENT_ERROR_IN_PAYMENT_GATEWAY', $extensionName, $this->paymentName, 'INVALID IPN');
			RApiPaymentHelper::logToFile(
				$this->paymentName,
				$extensionName,
				$data,
				$isValid = false,
				$statusText
			);

			$logData['status']       = RApiPaymentStatus::getStatusCreated();
			$logData['message_text'] = $statusText;

			return false;
		}
		else
		{
			$statusText  = JText::sprintf('LIB_REDCORE_PAYMENT_ERROR_IN_PAYMENT_GATEWAY', $extensionName, $this->paymentName, 'HTTP ERROR');
			RApiPaymentHelper::logToFile(
				$this->paymentName,
				$extensionName,
				$data,
				$isValid = false,
				$statusText
			);

			$logData['status']       = RApiPaymentStatus::getStatusCreated();
			$logData['message_text'] = $statusText;

			return false;
		}

		$logData['message_text']   = $statusText;
		$logData['currency']       = $payment->currency;
		$logData['amount']         = $payment->amount_total;
		$logData['transaction_id'] = $data['txn_id'];

		return true;
	}
}
