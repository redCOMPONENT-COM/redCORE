<?php
/**
 * @package    Redcore.Cli
 *
 * @copyright  Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

error_reporting(0);
ini_set('display_errors', 0);

// Initialize Joomla framework
require_once dirname(__FILE__) . '/joomla_framework.php';

// Configure error reporting to maximum for CLI output.
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * This script will check for status change for all payments on all payment plugins
 *
 * @package  Redcore.Cli
 * @since    1.5.0
 */
class CheckpaymentsApplicationCli extends JApplicationCli
{
	/**
	 * Status codes
	 * @var array
	 */
	public $finalStatuses = array();

	/**
	 * Entry point for the script
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function doExecute()
	{
		JFactory::getApplication('site');
		JPluginHelper::importPlugin('redcore');
		JPluginHelper::importPlugin('redpayment');

		// Set up statuses that are in their final stage
		$this->finalStatuses = array(
			RApiPaymentStatus::getStatusCompleted(),
			RApiPaymentStatus::getStatusCanceled_Reversal(),
			RApiPaymentStatus::getStatusDenied(),
			RApiPaymentStatus::getStatusExpired(),
			RApiPaymentStatus::getStatusRefunded(),
			RApiPaymentStatus::getStatusReversed(),
		);

		$this->out('============================');
		$this->out('Check Payments status change');
		$this->out('============================');

		$payments = $this->getPaymentsForChecking();

		$this->out('Number of payments for checking:' . count($payments));
		$this->out('============================');

		if (!empty($payments))
		{
			foreach ($payments as $payment)
			{
				// Print out payment info
				$this->out('============================');
				$this->out(
					sprintf('Check for order name "%s" for extension "%s" using "%s" payment plugin:',
						$payment->order_name,
						$payment->extension_name,
						$payment->payment_name
					)
				);
				$this->out('============================');

				// Preform check
				$status = RApiPaymentHelper::checkPayment($payment->id);

				// Print out status result
				foreach ($status as $statusKey => $message)
				{
					if (is_array($message))
					{
						foreach ($status as $key => $value)
						{
							$this->out($key . ': ' . $value);
						}
					}
					else
					{
						$this->out($statusKey . ': ' . $message);
					}
				}

				// Subtract retry count or reset it
				$paymentNew = RApiPaymentHelper::getPaymentById($payment->id);

				if (!in_array($paymentNew->status, $this->finalStatuses))
				{
					// We are still not done, we will subtract retry counter for this payment
					$paymentNew->retry_counter -= 1;

					RApiPaymentHelper::updatePaymentCounter($paymentNew->id, $paymentNew->retry_counter);

					$this->out('Retry checks left: ' . $paymentNew->retry_counter);
				}

				$this->out('============================');
			}
		}

		$this->out('============================');
		$this->out('Done !');
	}

	/**
	 * Get payments pending for checking
	 *
	 * @return mixed
	 */
	public function getPaymentsForChecking()
	{
		$db = JFactory::getDbo();
		$finalStatuses = array();
		$retryTime = RBootstrap::getConfig('payment_time_between_payment_check_requests', 24);

		foreach ($this->finalStatuses as $status)
		{
			$finalStatuses[] = $db->q($status);
		}

		$query = $db->getQuery(true)
			->select('p.*')
			->from($db->qn('#__redcore_payments', 'p'))
			->where('p.status NOT IN (' . implode(',', $finalStatuses) . ')')
			->where('p.retry_counter > 0')
			->where('TIMESTAMPDIFF(HOUR, p.modified_date, NOW()) > ' . (int) $retryTime);
		$db->setQuery($query);
		$items = $db->loadObjectList();

		return $items;
	}
}

JApplicationCli::getInstance('CheckpaymentsApplicationCli')->execute();
