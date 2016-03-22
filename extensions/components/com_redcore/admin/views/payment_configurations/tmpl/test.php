<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

// Check for request forgeries.
JPluginHelper::importPlugin('redpayment');
$app = JFactory::getApplication();
$input = $app->input;
$paymentName = $input->getString('payment_name');
$paymentConfigurationId = $input->getInt('payment_id', 0);
$randomId = rand(1, 10000);

$data = array(
	'payment_name' => $paymentName,
	'extension_name' => 'com_redcore',
	'owner_name' => '',
	'order_name' => 'Order ' . $randomId,
	'order_id' => 'test' . $randomId,
	'client_email' => 'test@test.com',
	'amount_original' => '10',
	'amount_order_tax' => '2',
	'order_tax_details' => '20% tax',
	'amount_shipping' => '1',
	'shipping_details' => '1 shipping',
	'customer_note' => 'Testing',
	'currency' => 'USD',
	'sandbox' => true,
	'url_cancel' => JUri::root() . 'administrator/index.php?option=com_redcore&view=payments',
	'url_accept' => JUri::root() . 'administrator/index.php?option=com_redcore&view=payments',
);

if (!empty($paymentConfigurationId))
{
	$model = RModelAdmin::getAdminInstance('Payment_Configuration', array(), 'com_redcore');

	if ($item = $model->getItem($paymentConfigurationId))
	{
		$data['extension_name'] = $item->extension_name;
		$data['owner_name'] = $item->owner_name;
	}
}

echo RApiPaymentHelper::displayPayment($paymentName, $data['extension_name'], $data['owner_name'], $data);
