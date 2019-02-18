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

if (!empty($payments)) :
?>
	<div class="controls">
<?php
	foreach ($payments as $key => $payment) :
?>
		<label for="<?php echo $payment->value ?>" id="<?php echo $payment->value ?>-lbl" class="radio">
			<input type="radio" name="<?php echo $name ?>" id="<?php echo $payment->value ?>"
				value="<?php echo $payment->value ?>" <?php echo trim($attr) . ($selected || $value == $payment->value ? ' checked="checked"' : '') ?> />
			<?php echo $payment->params->get('payment_title', $payment->text) ?>
			<?php if ($payment->params->get('payment_logo', '') != '') : ?>
				<br />
				<img src="<?php echo $payment->params->get('payment_logo', '') ?>" alt="<?php echo $payment->params->get('payment_title', $payment->text) ?>" />
			<?php endif; ?>
		</label>
<?php
	endforeach;
?>
	</div>
<?php
endif;
