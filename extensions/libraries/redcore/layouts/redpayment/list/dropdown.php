<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$data = $displayData;

$payments = $data['options']['payments'];
$extensionName = $data['options']['extensionName'];
$ownerName = $data['options']['ownerName'];
$name = !empty($data['options']['name']) ? $data['options']['name'] : 'redpayment_payment';
$value = !empty($data['options']['value']) ? $data['options']['value'] : '';
$id = !empty($data['options']['id']) ? $data['options']['id'] : 'redpayment_payment';
$attr = !empty($data['options']['attributes']) ? $data['options']['attributes'] : '';
$selectSingleOption = !empty($data['options']['selectSingleOption']);
$selected = $selectSingleOption && !empty($payments) && count($payments) <= 1;
$options = array();

foreach ($payments as $key => $payment)
{
	$options[] = JHTML::_('select.option', $payment->value, $payment->text);

	if ($selected)
	{
		$value = $payment->value;
	}
}

echo JHtml::_('select.genericlist', $options, $name, trim($attr), 'value', 'text', $value, $id);
