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
 * Helper class for Payment calls
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.5
 */
class RApiPaymentHelper
{
	/**
	 * Payments container
	 * @var array
	 */
	protected static $payments = array();

	/**
	 * Extension payments container
	 * @var array
	 */
	protected static $extensionPayments = array();

	/**
	 * Plugin parameters container
	 * @var array
	 */
	protected static $pluginParams = array();

	/**
	 * Extension helper classes container
	 * @var array
	 */
	protected static $extensionHelperClasses = array();

	/**
	 * Gets Payment parameters
	 * If owner name config is not found it will use extension config, and if extension config is not found it will use default plugin config
	 *
	 * @param   string  $paymentName    Payment Name
	 * @param   string  $extensionName  Extension Name
	 * @param   string  $ownerName      Owner Name
	 *
	 * @return  object
	 */
	public static function getPaymentParams($paymentName = '', $extensionName = '', $ownerName = '')
	{
		if (isset(self::$pluginParams[$paymentName][$extensionName][$ownerName]))
		{
			return self::$pluginParams[$paymentName][$extensionName][$ownerName];
		}

		// This query will make a fallback
		// If owner name config is not found it will use extension config, and if extension config is not found it will use default plugin config
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('pc1.*, p.*, COALESCE(pc2.params, COALESCE(pc1.params, p.params)) as params, COALESCE(pc2.state, COALESCE(pc1.state, p.state)) as state')
			->select('CONCAT("plg_redpayment_", p.element) as plugin_path_name')
			->select('COALESCE(pc2.extension_name, COALESCE(pc1.extension_name, ' . $db->q('') . ')) as extension_name')
			->select('COALESCE(pc2.owner_name, COALESCE(pc1.owner_name, ' . $db->q('') . ')) as owner_name')
			->select('COALESCE(pc2.state, COALESCE(pc1.state, p.enabled)) as state')
			->select('p.params AS original_params')
			->from($db->qn('#__extensions', 'p'))
			->where($db->qn('p.type') . '= ' . $db->q('plugin'))
			->where($db->qn('p.folder') . '= ' . $db->q('redpayment'))
			->where($db->qn('p.element') . ' = ' . $db->q($paymentName))
			->leftJoin(
				$db->qn('#__redcore_payment_configuration', 'pc1') . ' ON pc1.payment_name = p.element AND pc1.extension_name = ' . $db->q($extensionName)
				. ' AND pc1.owner_name = ' . $db->q('')
			)
			->leftJoin(
				$db->qn('#__redcore_payment_configuration', 'pc2') . ' ON pc2.payment_name = p.element AND pc2.extension_name = ' . $db->q($extensionName)
				. ' AND pc2.owner_name = ' . $db->q($ownerName)
			);

		$db->setQuery($query);
		$item = $db->loadObject();

		$registry = new Joomla\Registry\Registry;
		$registry->loadString($item->original_params);
		$item->original_params = $registry;
		$originalParams = clone $registry;

		$registry = new Joomla\Registry\Registry;
		$registry->loadString($item->params);
		$originalParams->merge($registry);
		$item->params = $originalParams;

		self::$pluginParams[$paymentName][$extensionName][$ownerName] = $item;

		return self::$pluginParams[$paymentName][$extensionName][$ownerName];
	}

	/**
	 * Gets Payment data
	 *
	 * @param   int  $paymentId  Payment Id
	 *
	 * @return  object
	 */
	public static function getPaymentById($paymentId)
	{
		if (empty($paymentId))
		{
			return null;
		}

		if (isset(self::$payments[$paymentId]))
		{
			return self::$payments[$paymentId];
		}

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('p.*')
			->from($db->qn('#__redcore_payments', 'p'))
			->where('p.id = ' . (int) $paymentId);
		$db->setQuery($query);
		$item = $db->loadObject();
		self::$payments[$paymentId] = $item;

		if ($item && !empty($item->extension_name))
		{
			self::$extensionPayments[$item->extension_name][$item->order_id] = $paymentId;
		}

		return self::$payments[$paymentId];
	}

	/**
	 * Gets Payment data
	 *
	 * @param   string  $extensionName  Extension name (ex: com_content)
	 * @param   string  $orderId        Extension order Id
	 *
	 * @return  object
	 */
	public static function getPaymentByExtensionId($extensionName, $orderId)
	{
		if (empty($extensionName) || empty($orderId))
		{
			return null;
		}

		if (isset(self::$extensionPayments[$extensionName][$orderId]))
		{
			return self::getPaymentById(self::$extensionPayments[$extensionName][$orderId]);
		}

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('p.*')
			->from($db->qn('#__redcore_payments', 'p'))
			->where('p.order_id = ' . $db->q($orderId))
			->where('p.extension_name = ' . $db->q($extensionName));
		$db->setQuery($query);
		$payment = $db->loadObject();

		if ($payment && $payment->id)
		{
			self::$payments[$payment->id] = $payment;
			self::$extensionPayments[$extensionName][$orderId] = $payment->id;

			return self::getPaymentById(self::$extensionPayments[$extensionName][$orderId]);
		}

		return null;
	}

	/**
	 * Prepare Payment data for chart
	 *
	 * @param   array   $data       Data used for chart definition
	 * @param   string  $chartType  Chart types: Line, Bar, Radar, PolarArea, Pie, Doughnut
	 *
	 * @return  string
	 *
	 * @since   1.5
	 */
	public static function prepareChartData($data, $chartType = 'Line')
	{
		$chartType = RHtmlRchart::getChartType($chartType);
		$chartData = array();
		$amounts = $data['amounts'];
		$labels  = $data['labels'];

		switch ($chartType)
		{
			case 'PolarArea':
			case 'Pie':
			case 'Doughnut':

				foreach ($amounts as $extensionName => $amount)
				{
					$dataValues = 0;
					$color = implode(',', RHtmlRchart::getColorFromHash($extensionName));
					$strokeColor = implode(',', RHtmlRchart::getColorFromHash($extensionName, 'redcore'));

					foreach ($amount as $value)
					{
						$dataValues += $value['sum'];
					}

					$dataSet = new stdClass;
					$dataSet->value = $dataValues;
					$dataSet->color = 'rgba(' . $color . ',0.5)';
					$dataSet->highlight = 'rgba(' . $strokeColor . ',1)';
					$dataSet->label = $extensionName;

					$chartData[] = $dataSet;
				}

				break;

			case 'Line':
			case 'Radar':
			case 'Bar':
			default:
				$chartData['labels'] = $labels;
				$chartData['datasets'] = array();

				if (empty($amounts))
				{
					// Needed for proper chart display
					$chartData['datasets'] = array(array());
				}
				else
				{
					foreach ($amounts as $extensionName => $amount)
					{
						$dataValues = array();
						$color = implode(',', RHtmlRchart::getColorFromHash($extensionName));
						$strokeColor = implode(',', RHtmlRchart::getColorFromHash($extensionName, 'redcore'));

						foreach ($chartData['labels'] as $label)
						{
							$dataValues[] = !isset($amount[$label]) ? 0 : $amount[$label];
						}

						$dataSet = array(
							'label' => $extensionName,
							'fillColor' => 'rgba(' . $color . ',0.2)',
							'strokeColor' => 'rgba(' . $strokeColor . ',1)',
							'data' => $dataValues,
						);

						if ($chartType == 'Bar')
						{
							$dataSet['highlightFill'] = 'rgba(' . $color . ',0.75)';
							$dataSet['highlightStroke'] = 'rgba(' . $color . ',1)';
						}
						else
						{
							$dataSet['pointColor'] = 'rgba(' . $color . ',1)';
							$dataSet['pointStrokeColor'] = '#fff';
							$dataSet['pointHighlightFill'] = '#fff';
							$dataSet['pointHighlightStroke'] = 'rgba(' . $color . ',1)';
						}

						$chartData['datasets'][] = $dataSet;
					}
				}

				break;
		}

		return $chartData;
	}

	/**
	 * Prepare Payment data for chart
	 *
	 * @param   array   $filters   Filters for chart data
	 * @param   int     $interval  Chart interval points
	 * @param   string  $sortItem  On which field should it sort the values
	 *
	 * @return  mixed
	 *
	 * @since   1.5
	 */
	public static function getChartData($filters = array(), $interval = 7, $sortItem = 'all')
	{
		$db = JFactory::getDbo();

		$db->setQuery(self::getChartDataQuery($filters));
		$data = $db->loadObjectList();
		$chartData = array();
		$chartData['amounts'] = array();
		$chartData['labels'] = array();
		$chartData['currency'] = 'USD';

		// Is status is not set then we are in the statuses view graph and we are not looking only confirmed payments
		$dateField = !isset($filters['status']) ? 'created_date' : 'confirmed_date';

		if (empty($filters['start_date']))
		{
			if (!empty($filters['end_date']))
			{
				$startDate = date('Y-m-d', strtotime($filters['end_date'] . ' -7 weeks'));
			}
			else
			{
				$startDate = date('Y-m-d', strtotime('today -7 weeks'));
			}
		}
		else
		{
			$startDate = $filters['start_date'];
		}

		if (empty($filters['end_date']))
		{
			$endDate = date('Y-m-d', strtotime($startDate . ' +7 weeks'));
		}
		else
		{
			$endDate = $filters['end_date'];
		}

		$startDateNumber = strtotime($startDate);
		$endDateNumber = strtotime($endDate);
		$checkPoints = array();
		$chartData['days'] = round(($endDateNumber - $startDateNumber) / 86400);
		$point = round($chartData['days'] / $interval);

		for ($i = $interval; $i >= 1; $i--)
		{
			$startDateNumber = strtotime($endDate . ' -' . ($i * $point) . ' days');
			$endDateNumber = strtotime($endDate . ' -' . (($i - 1) * $point) . ' days');

			$startDatelabel = date('Y-m-d', $startDateNumber);
			$endDateLabel = date('Y-m-d', $endDateNumber);
			$chartData['labels'][] = $startDatelabel;

			$checkPoints[] = array(
				'startDate' => $startDatelabel,
				'endDate' => $endDateLabel,
			);
		}

		foreach ($data as $key => $item)
		{
			$sortName = $sortItem == 'all' ? 'all' : $item->{$sortItem};

			if (!isset($chartData['amounts'][$sortName]))
			{
				$chartData['amounts'][$sortName] = array();
			}

			$createdDate = explode('-', $item->{$dateField});
			$itemYear = $createdDate[0];
			$itemMonth = $createdDate[1];
			$itemDay = explode(' ', $createdDate[2]);
			$itemDay = $itemDay[0];

			if (!isset($chartData['amounts'][$sortName]['sum'][$itemYear]['val'][$itemMonth]['val'][$itemDay]))
			{
				$chartData['amounts'][$sortName]['sum'][$itemYear]['sum'] = 0;
				$chartData['amounts'][$sortName]['sum'][$itemYear]['count'] = 0;
				$chartData['amounts'][$sortName]['sum'][$itemYear]['val'][$itemMonth]['sum'] = 0;
				$chartData['amounts'][$sortName]['sum'][$itemYear]['val'][$itemMonth]['count'] = 0;
				$chartData['amounts'][$sortName]['sum'][$itemYear]['val'][$itemMonth]['val'][$itemDay]['sum'] = 0;
				$chartData['amounts'][$sortName]['sum'][$itemYear]['val'][$itemMonth]['val'][$itemDay]['count'] = 0;
				$chartData['amounts'][$sortName]['sum'][$itemYear]['val'][$itemMonth]['val'][$itemDay]['val'] = array();
			}

			$chartData['amounts'][$sortName]['sum'][$itemYear]['val'][$itemMonth]['val'][$itemDay]['val'][] = $item->amount_paid;
			$chartData['currency'] = $item->currency;

			foreach ($checkPoints as $checkPoint)
			{
				if ($item->{$dateField} > $checkPoint['startDate'] && $item->{$dateField} < $checkPoint['endDate'])
				{
					if (!isset($chartData['amounts'][$sortName][$checkPoint['startDate']]))
					{
						$chartData['amounts'][$sortName][$checkPoint['startDate']] = 0;
					}

					$chartData['amounts'][$sortName][$checkPoint['startDate']] += $item->amount_paid;
				}
			}

			unset($data[$key]);
		}

		$currentYear = date('Y');

		foreach ($chartData['amounts'] as $extensionName => $options)
		{
			$sum = 0;
			$count = 0;
			$maxCount = 0;
			$maxSum = 0;

			foreach ($options['sum'] as $year => $months)
			{
				foreach ($months['val'] as $month => $days)
				{
					foreach ($days['val'] as $day => $values)
					{
						$daySum = array_sum($values['val']);
						$dayCount = count($values['val']);
						$chartData['amounts'][$extensionName]['sum'][$year]['sum'] += $daySum;
						$chartData['amounts'][$extensionName]['sum'][$year]['count'] += $dayCount;
						$chartData['amounts'][$extensionName]['sum'][$year]['val'][$month]['sum'] += $daySum;
						$chartData['amounts'][$extensionName]['sum'][$year]['val'][$month]['count'] += $dayCount;
						$chartData['amounts'][$extensionName]['sum'][$year]['val'][$month]['val'][$day]['sum'] += $daySum;
						$chartData['amounts'][$extensionName]['sum'][$year]['val'][$month]['val'][$day]['count'] = $dayCount;
						$chartData['amounts'][$extensionName]['sum'][$year]['val'][$month]['val'][$day]['average'] = round($daySum / $dayCount, 2);
						$sum += $daySum;
						$count += $dayCount;

						if ($maxCount < $dayCount)
						{
							$maxCount = $dayCount;
						}

						if ($maxSum < $daySum)
						{
							$maxSum = $daySum;
						}
					}

					$daysInMonth = date('t', strtotime($year . '-' . $month . '-01'));
					$chartData['amounts'][$extensionName]['sum'][$year]['val'][$month]['averageCount']
									= round($chartData['amounts'][$extensionName]['sum'][$year]['val'][$month]['count'] / $daysInMonth, 2);
					$chartData['amounts'][$extensionName]['sum'][$year]['val'][$month]['averageSum']
									= round($chartData['amounts'][$extensionName]['sum'][$year]['val'][$month]['sum'] / $daysInMonth, 2);
				}

				// If this is current year then it is not over yet we need to get only up to current date
				if ($currentYear == $year)
				{
					$daysInYear = round((strtotime(date('Y-m-d')) - strtotime($year . '-01-01')) / 86400);
				}
				else
				{
					$daysInYear = date('z', strtotime($year . '-12-31')) + 1;
				}

				$chartData['amounts'][$extensionName]['sum'][$year]['averageCount']
								= round($chartData['amounts'][$extensionName]['sum'][$year]['count'] / $daysInYear, 2);
				$chartData['amounts'][$extensionName]['sum'][$year]['averageSum']
								= round($chartData['amounts'][$extensionName]['sum'][$year]['sum'] / $daysInYear, 2);
			}

			$chartData['amounts'][$extensionName]['sum']['sum'] = $sum;
			$chartData['amounts'][$extensionName]['sum']['count'] = $count;
			$chartData['amounts'][$extensionName]['sum']['maxCount'] = $maxCount;
			$chartData['amounts'][$extensionName]['sum']['maxSum'] = $maxSum;
			$chartData['amounts'][$extensionName]['sum']['averageCount'] = $chartData['days'] > 0 ? round($count / $chartData['days'], 2) : 0;
			$chartData['amounts'][$extensionName]['sum']['averageSum'] = $chartData['days'] > 0 ? round($sum / $chartData['days'], 2) : 0;
		}

		return $chartData;
	}

	/**
	 * Create Payments query for chart
	 *
	 * @param   array  $filters  Filters for chart data
	 *
	 * @return  mixed
	 *
	 * @since   1.5
	 */
	public static function getChartDataQuery($filters = array())
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('p.*')
			->from($db->qn('#__redcore_payments', 'p'));

		if (RBootstrap::getConfig('payment_enable_chart_sandbox_payments', 1) == 0)
		{
			$query->where('p.sandbox = 0');
		}

		// Is status is not set then we are in the statuses view graph and we are not looking only confirmed payments
		$dateField = !isset($filters['status']) ? 'created_date' : 'confirmed_date';

		// Filter search
		if (!empty($filters['search_payments']))
		{
			$search = $db->quote('%' . $db->escape($filters['search_payments'], true) . '%');
			$query->where('(p.order_name LIKE ' . $search . ')');
		}

		if (!empty($filters['payment_name']))
		{
			$paymentName = $db->quote($filters['payment_name']);
			$query->where('p.payment_name = ' . $paymentName);
		}

		if (!empty($filters['extension_name']))
		{
			$extensionName = $db->quote($filters['extension_name']);
			$query->where('p.extension_name = ' . $extensionName);
		}

		if (!empty($filters['owner_name']))
		{
			$ownerName = $db->quote($filters['owner_name']);
			$query->where('p.owner_name = ' . $ownerName);
		}

		elseif (!empty($filters['start_date']))
		{
			$filters['start_date'] = date('Y-m-d H:i:s', strtotime($filters['start_date']));
			$startDate = $db->quote($filters['start_date']);
			$query->where('p.' . $dateField . ' >= ' . $startDate);
		}

		if (!empty($filters['end_date']))
		{
			$filters['end_date'] = date('Y-m-d H:i:s', strtotime($filters['end_date']));
			$endDate = $db->quote($filters['end_date']);
			$query->where('p.' . $dateField . ' <= ' . $endDate);
		}

		if (!empty($filters['status']))
		{
			$status = $db->quote($filters['status']);
			$query->where('p.status = ' . $status);
		}

		// Ordering
		$order = !empty($filters['ordering']) ? $filters['ordering'] : 'p.payment_name';
		$direction = !empty($filters['direction']) ? $filters['direction'] : 'ASC';
		$query->order($db->escape($order) . ' ' . $db->escape($direction));

		return $query;
	}

	/**
	 * Update payment status
	 *
	 * @param   int  $paymentId     Id of the payment
	 * @param   int  $retryCounter  Id of the payment
	 *
	 * @return  bool
	 *
	 * @since   1.5
	 */
	public static function updatePaymentCounter($paymentId, $retryCounter)
	{
		$paymentOriginal = self::getPaymentById($paymentId);
		$paymentOriginal->retry_counter = $retryCounter;

		return self::updatePaymentData($paymentOriginal);
	}

	/**
	 * Update payment status
	 *
	 * @param   mixed  $paymentData  Payment data
	 *
	 * @return  bool
	 */
	public static function updatePaymentData($paymentData)
	{
		if (is_object($paymentData))
		{
			$paymentData = ArrayHelper::fromObject($paymentData);
		}

		// If there is no payment Id, we are checking if that payment data is saved under another row
		if (empty($paymentData['id']))
		{
			$oldPayment = self::getPaymentByExtensionId($paymentData['extension_name'], $paymentData['order_id']);

			// We add all relevant data to the object
			if ($oldPayment)
			{
				$paymentData['id'] = $oldPayment->id;
			}
		}
		else
		{
			$oldPayment = self::getPaymentById($paymentData['id']);
		}

		if ($oldPayment)
		{
			// We are in different payment
			if (isset($paymentData['payment_name']) && !empty($oldPayment->payment_name) && $oldPayment->payment_name != $paymentData['payment_name'])
			{
				// We check for status in old Payment data, if it is confirmed, then this order is already processed and should not be processed again
				if (in_array($oldPayment->status, array(RApiPaymentStatus::getStatusCompleted(), RApiPaymentStatus::getStatusCanceled_Reversal())))
				{
					return false;
				}
			}
		}

		// Set status to created if not set
		if (empty($paymentData['status']))
		{
			$paymentData['status'] = RApiPaymentStatus::getStatusCreated();
		}

		/** @var RedcoreModelPayment $model */
		$model = RModelAdmin::getAdminInstance('Payment', array(), 'com_redcore');

		if (!$model->save($paymentData))
		{
			return false;
		}

		$paymentId = (isset($paymentData['id']) ? $paymentData['id'] : (int) $model->getState('payment.id'));

		if (!empty($paymentData['id']))
		{
			// We unset the object so we can load a fresh one on next request
			unset(self::$payments[$paymentData['id']]);
		}

		return $paymentId;
	}

	/**
	 * Update payment status
	 *
	 * @param   int  $paymentId  Id of the payment
	 *
	 * @return  mixed
	 *
	 * @since   1.5
	 */
	public static function updatePaymentStatus($paymentId)
	{
		if (empty($paymentId))
		{
			return false;
		}

		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('pl.*')
			->from($db->qn('#__redcore_payment_log', 'pl'))
			->where('pl.payment_id = ' . (int) $paymentId)
			->order('pl.created_date ASC');

		$db->setQuery($query);
		$paymentLogs = $db->loadObjectList();

		if ($paymentLogs)
		{
			$paymentOriginal = self::getPaymentById($paymentId);
			$payment = ArrayHelper::fromObject($paymentOriginal);
			$customerNote = array();
			$amountPaid = 0;
			$currency = '';
			$status = RApiPaymentStatus::getStatusCreated();

			foreach ($paymentLogs as $paymentLog)
			{
				if ($paymentLog->status == RApiPaymentStatus::getStatusCompleted())
				{
					if (!empty($currency) && $currency != $paymentLog->currency)
					{
						// We have a problem. Two different confirmed payments but in different currencies.
						// We will only set latest payment data
						$amountPaid = $paymentLog->amount;
						$currency = $paymentLog->currency;
					}
					else
					{
						if ($payment['transaction_id'] != $paymentLog->transaction_id || $amountPaid == 0)
						{
							$amountPaid += $paymentLog->amount;
						}

						if (!empty($paymentLog->currency))
						{
							$currency = $paymentLog->currency;
						}
					}

					$payment['coupon_code'] = $paymentLog->coupon_code;
					$payment['confirmed_date'] = $paymentLog->created_date;
				}

				if (!empty($paymentLog->transaction_id))
				{
					$payment['transaction_id'] = $paymentLog->transaction_id;
				}

				// We will take customer note from every log but not duplicates
				if (!empty($paymentLog->customer_note) && !in_array($paymentLog->customer_note, $customerNote))
				{
					$customerNote[] = $paymentLog->customer_note;
				}

				$status = RApiPaymentStatus::changeStatus($status, $paymentLog->status);

				// Handle statuses that subtract paid amount
				if (in_array($status, array(RApiPaymentStatus::getStatusRefunded(), RApiPaymentStatus::getStatusReversed())))
				{
					$amountPaid -= $paymentLog->amount;

					if ($amountPaid < 0)
					{
						$amountPaid = 0;
					}
				}
			}

			$payment['amount_paid'] = $amountPaid;
			$payment['customer_note'] = implode("\r\n", $customerNote);

			if ($status == RApiPaymentStatus::getStatusCompleted() && $payment['amount_paid'] != $payment['amount_total'])
			{
				// If not ePay partial payment capture
				if (self::isInstantCapture((object) $payment) || $payment['amount_original'] <= $payment['amount_total'])
				{
					$status = RApiPaymentStatus::getStatusPending();
				}
			}

			$payment['status'] = $status;

			if (empty($payment['currency']))
			{
				$payment['currency'] = $currency;
			}

			// Currency should not be numeric
			if (!empty($payment['currency']) && is_numeric($payment['currency']))
			{
				$payment['currency'] = RHelperCurrency::getIsoCode($payment['currency']);
			}

			if (!self::updatePaymentData($payment))
			{
				return false;
			}

			// If we changed status we need to call extension helper file to trigger its update method
			if ($paymentOriginal->status != $payment['status'])
			{
				$paymentNew = self::getPaymentById($paymentId);

				self::triggerExtensionHelperMethod($paymentNew->extension_name, 'paymentStatusChanged', $paymentOriginal, $paymentNew);
			}
		}

		return true;
	}

	/**
	 * Check if ePay is using  instant capture
	 *
	 * @param   object	$payment	Payment object
	 *
	 * @return  boolean
	 */
	public static function isInstantCapture($payment)
	{
		$plugin 		= RApiPaymentHelper::getPaymentParams($payment->payment_name, $payment->extension_name, $payment->owner_name);
		$instantcapture = false;

		if ($plugin && strcmp($plugin->element, 'epay') === 0)
		{
			$instantcapture = (boolean) $plugin->params->get('instantcapture', 0);
		}

		return $instantcapture;
	}

	/**
	 * Display payment
	 *
	 * @param   string  $paymentName    Payment name
	 * @param   string  $extensionName  Extension name
	 * @param   string  $ownerName      Owner name
	 * @param   array   $data           Data for the payment form
	 *
	 * @return  string|false
	 *
	 * @since   1.5
	 */
	public static function displayPayment($paymentName, $extensionName, $ownerName = '', $data = array())
	{
		JPluginHelper::importPlugin('redpayment');
		$app = JFactory::getApplication();
		$html = '';
		$app->triggerEvent('onRedpaymentDisplayPayment', array($paymentName, $extensionName, $ownerName, $data, &$html));

		return $html;
	}

	/**
	 * Create new payment
	 *
	 * @param   string  $paymentName    Payment name
	 * @param   string  $extensionName  Extension name
	 * @param   string  $ownerName      Owner name
	 * @param   array   $data           Data for the payment form
	 *
	 * @return  string|false
	 *
	 * @since   1.5
	 */
	public static function createNewPayment($paymentName, $extensionName, $ownerName, $data = array())
	{
		JPluginHelper::importPlugin('redpayment');
		$app = JFactory::getApplication();
		$paymentId = null;
		$app->triggerEvent('onRedpaymentCreatePayment', array($paymentName, $extensionName, $ownerName, $data, &$paymentId));

		return $paymentId;
	}

	/**
	 * List payments
	 *
	 * @param   string  $extensionName  Extension name
	 * @param   string  $ownerName      Owner name
	 * @param   string  $listType       List type can be between radio and dropdown (if parameter not set then default redcore plugin option is used)
	 * @param   string  $name           Name of the field
	 * @param   string  $value          Selected value of the field
	 * @param   string  $id             Id of the field
	 * @param   string  $attributes     Attributes for the field
	 *
	 * @return  string|false
	 *
	 * @since   1.5
	 */
	public static function listPayments($extensionName = null, $ownerName = null, $listType = null, $name = '', $value = '', $id = '', $attributes = '')
	{
		JPluginHelper::importPlugin('redpayment');
		$app = JFactory::getApplication();

		if (empty($listType))
		{
			$listType = RBootstrap::getConfig('payment_list_payments_type', 'radio');
		}

		if (empty($extensionName))
		{
			$extensionName = $app->input->get->getString('option', '');
		}

		$payments = array();

		$app->triggerEvent('onRedpaymentListPayments', array($extensionName, $ownerName, &$payments));

		return RLayoutHelper::render(
			'redpayment.list.' . strtolower($listType),
			array(
				'options' => array(
					'payments' => $payments,
					'extensionName' => $extensionName,
					'ownerName' => $ownerName,
					'name' => $name,
					'value' => $value,
					'id' => $id,
					'attributes' => $attributes,
					'selectSingleOption' => true,
				)
			)
		);
	}

	/**
	 * Check payment status
	 *
	 * @param   int  $paymentId  Id of the payment
	 *
	 * @return  array|false
	 *
	 * @since   1.5
	 */
	public static function checkPayment($paymentId)
	{
		JPluginHelper::importPlugin('redpayment');
		$status = array();

		if ($item = self::getPaymentById($paymentId))
		{
			JFactory::getApplication()->triggerEvent('onRedpaymentCheckPayment', array($item->payment_name, $paymentId, &$status));
		}

		return $status;
	}

	/**
	 * Refund payment
	 *
	 * @param   int  $paymentId  Id of the payment
	 *
	 * @return  bool
	 *
	 * @since   1.5
	 */
	public static function refundPayment($paymentId)
	{
		JPluginHelper::importPlugin('redpayment');
		$isRefunded = null;

		if ($item = self::getPaymentById($paymentId))
		{
			JFactory::getApplication()->triggerEvent(
				'onRedpaymentRefundPayment', array($item->payment_name, $item->extension_name, $item->owner_name, $item, &$isRefunded)
			);
		}

		return $isRefunded;
	}

	/**
	 * Capture payment
	 *
	 * @param   int  $paymentId  Id of the payment
	 *
	 * @return  bool
	 *
	 * @since   1.5
	 */
	public static function capturePayment($paymentId)
	{
		JPluginHelper::importPlugin('redpayment');
		$isCaptured = null;

		if ($item = self::getPaymentById($paymentId))
		{
			if ((float) $item->amount_total > (float) $item->amount_original)
			{
				$item->amount_total = $item->amount_original;
			}

			$item->amount_paid = $item->amount_total;

			JFactory::getApplication()->triggerEvent(
				'onRedpaymentCapturePayment', array($item->payment_name, $item->extension_name, $item->owner_name, $item, &$isCaptured)
			);
		}

		return $isCaptured;
	}

	/**
	 * Delete payment
	 *
	 * @param   int  $paymentId  Id of the payment
	 *
	 * @return  bool
	 *
	 * @since   1.5
	 */
	public static function deletePayment($paymentId)
	{
		JPluginHelper::importPlugin('redpayment');
		$isDeleted = null;

		if ($item = self::getPaymentById($paymentId))
		{
			JFactory::getApplication()->triggerEvent(
				'onRedpaymentDeletePayment', array($item->payment_name, $item->extension_name, $item->owner_name, $item, &$isDeleted)
			);
		}

		return $isDeleted;
	}

	/**
	 * Logs the relevant data from payment gateway to file
	 *
	 * @param   string   $paymentName    Payment name
	 * @param   string   $extensionName  Extension name
	 * @param   mixed    $data           Request data
	 * @param   boolean  $isValid        Is Valid payment
	 * @param   string   $statusText     Status text
	 *
	 * @return  void
	 */
	public static function logToFile($paymentName, $extensionName, $data, $isValid = true, $statusText = '')
	{
		if (RBootstrap::getConfig('payment_enable_file_logger', 0))
		{
			return;
		}

		JLoader::import('joomla.filesystem.file');
		$config = JFactory::getConfig();
		$logpath = $config->get('log_path');

		$logFilename = $logpath . '/redpayment/' . $paymentName . '/' . $extensionName . '/'
			. date('Y-m-') . strtolower($paymentName) . '-' . strtolower($extensionName) . '_log';
		$logFile = $logFilename . '.php';

		if (JFile::exists($logFile))
		{
			// If file is over 1MB we break it in new file
			if (@filesize($logFile) > 1048576)
			{
				$i = 1;

				while (true)
				{
					$newFilename = $logFilename . '-' . $i . '.php';

					if (!JFile::exists($newFilename))
					{
						// Copy old file contents to a new location
						JFile::copy($logFile, $newFilename);
						JFile::delete($logFile);

						// We start our logger from start
						$dummy = "<?php die(); ?>\n";
						JFile::write($logFile, $dummy);

						break;
					}

					$i++;
				}
			}
		}
		else
		{
			// New log file in a month
			$dummy = "<?php die(); ?>\n";
			JFile::write($logFile, $dummy);
		}

		// Current file contents
		$logData = @file_get_contents($logFile);

		if ($logData === false)
		{
			$logData = '';
		}

		$logData .= "\n" . str_repeat('=', 20);
		$logData .= $isValid ? ' VALID ' . $paymentName . ' ' : ' INVALID ' . $paymentName . ' *** FRAUD ATTEMPT OR INVALID NOTIFICATION *** ';
		$logData .= str_repeat('=', 20);

		if (!empty($statusText))
		{
			$logData .= "\n" . str_repeat('=', 20);
			$logData .= "\n" . $statusText;
			$logData .= "\n" . str_repeat('=', 20);
		}

		$logData .= "\nDatetime : " . gmdate('Y-m-d H:i:s') . " GMT\n\n";

		if (is_array($data))
		{
			foreach ($data as $key => $value)
			{
				$logData .= str_pad($key, 30, ' ') . $value . "\n";
			}
		}
		elseif (is_object($data))
		{
			$logData .= (json_encode($data)) . "\n";
		}
		else
		{
			$logData .= $data . "\n";
		}

		$logData .= "\n";

		JFile::write($logFile, $logData);
	}

	/**
	 * Generate Payment Log depending on the status
	 *
	 * @param   string        $status   Status string
	 * @param   array|object  $data     Data from gateway
	 * @param   string        $message  Message Text
	 *
	 * @return array
	 */
	public static function generatePaymentLog($status, $data, $message = null)
	{
		if (is_object($data))
		{
			$data = ArrayHelper::fromObject($data);
		}

		$paymentLog = array();

		$paymentLog['payment_id'] = !empty($data['payment_id']) ? $data['payment_id'] : @$data['id'];
		$paymentLog['ip_address'] = $_SERVER['REMOTE_ADDR'];
		$paymentLog['referrer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
		$paymentLog['transaction_id'] = !empty($data['transaction_id']) ? $data['transaction_id'] : '';
		$paymentLog['status'] = RApiPaymentStatus::getStatus($status);
		$paymentLog['message_uri'] = JUri::getInstance()->toString();
		$paymentLog['message_post'] = json_encode($data);
		$paymentLog['message_text'] = !is_null($message) ?
			$message : JText::sprintf('LIB_REDCORE_PAYMENT_LOG_DEFAULT_MESSAGE', $data['extension_name'], $data['payment_name']);
		$paymentLog['coupon_code'] = !empty($data['coupon_code']) ? $data['coupon_code'] : '';

		return $paymentLog;
	}

	/**
	 * Generate Payment Log depending on the status
	 *
	 * @param   array  $paymentLog           Data for payment log storage
	 * @param   bool   $updatePaymentStatus  Update Payment Status
	 *
	 * @return bool
	 */
	public static function saveNewPaymentLog($paymentLog, $updatePaymentStatus = true)
	{
		if (empty($paymentLog['payment_id']))
		{
			return false;
		}

		// Forcing default set of statuses
		$paymentLog['status'] = RApiPaymentStatus::getStatus($paymentLog['status']);

		// Currency should not be numeric
		if (!empty($paymentLog['currency']) && is_numeric($paymentLog['currency']))
		{
			$paymentLog['currency'] = RHelperCurrency::getIsoCode($paymentLog['currency']);
		}

		/** @var RedcoreModelPayment_Log $logModel */
		$logModel = RModelAdmin::getAdminInstance('Payment_Log', array(), 'com_redcore');

		// Avoid ghost id from URL
		$paymentLog['id'] = 0;

		if ($logModel->save($paymentLog))
		{
			if ($updatePaymentStatus)
			{
				self::updatePaymentStatus($paymentLog['payment_id']);
			}
		}

		return true;
	}

	/**
	 * Calls method from extension helper file if exists
	 *
	 * @param   string  $extensionName  Extension name
	 * @param   string  $functionName   Function name
	 *
	 * @return  mixed It will return Extension helper class function result or false if it does not exists
	 */
	public static function triggerExtensionHelperMethod($extensionName, $functionName)
	{
		$apiHelperClass = self::getExtensionHelperObject($extensionName);
		$args = func_get_args();

		// Remove extension and function name from arguments
		array_shift($args);
		array_shift($args);

		// PHP 5.3 workaround
		$temp = array();

		foreach ($args as &$arg)
		{
			$temp[] = &$arg;
		}

		// Checks if that method exists in helper file and executes it
		if (method_exists($apiHelperClass, $functionName))
		{
			return call_user_func_array(array($apiHelperClass, $functionName), $temp);
		}

		return false;
	}

	/**
	 * Gets instance of extension helper object if exists
	 *
	 * @param   string  $extensionName  Extension name
	 *
	 * @return  mixed It will return Extension helper class or false if it does not exists
	 */
	public static function getExtensionHelperObject($extensionName)
	{
		if (!$extensionName)
		{
			return false;
		}

		if (isset(self::$extensionHelperClasses[$extensionName]))
		{
			return self::$extensionHelperClasses[$extensionName];
		}

		// Helper file location to search inside of the extension
		$helperFile = JPath::clean(JPATH_ADMINISTRATOR . '/components/' . strtolower($extensionName) . '/helpers/redpayment/extension_helper.php');

		if (file_exists($helperFile))
		{
			require_once $helperFile;
		}

		$helperClassName = 'RApiPaymentExtensionHelper' . ucfirst(strtolower($extensionName));

		if (class_exists($helperClassName))
		{
			self::$extensionHelperClasses[$extensionName] = new $helperClassName;
		}
		else
		{
			self::$extensionHelperClasses[$extensionName] = null;
		}

		return self::$extensionHelperClasses[$extensionName];
	}

	/**
	 * Gets last payment log object
	 *
	 * @param   int  $paymentId  Id of the payment
	 *
	 * @return  object
	 *
	 * @since   1.5
	 */
	public static function getLastPaymentLog($paymentId)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select('pl.*')
			->from($db->qn('#__redcore_payment_log', 'pl'))
			->where('pl.payment_id = ' . (int) $paymentId)
			->order('pl.created_date DESC');

		$db->setQuery($query, 0, 1);

		return $db->loadObject();
	}
}
