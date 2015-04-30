<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$data = $displayData;

$params = $data['options']['params'];
$paymentData = $data['options']['paymentData'];
$fields = !empty($paymentData['hiddenFields']) ? $paymentData['hiddenFields'] : array();
$extensionName = $data['options']['extensionName'];
$ownerName = $data['options']['ownerName'];
$paymentName = $data['options']['paymentName'];
$paymentTitle = $data['options']['paymentTitle'];
$paymentLogo = $params->get('payment_logo');
$formName = 'redpaymentForm' . $data['options']['paymentName'];
$action = $data['options']['action'];
$autoSubmit = !empty($data['options']['autoSubmit']);
?>
<form action="<?php echo $action ?>" class="adminForm" method="post" name="<?php echo $formName ?>" id="<?php echo $formName ?>">
	<?php if(!empty($fields)) :
		foreach ($fields as $fieldName => $fieldValue) : ?>
			<input type="hidden" name="<?php echo $fieldName; ?>" value="<?php echo $fieldValue; ?>" />
		<?php endforeach;
	endif; ?>

	<?php if ($autoSubmit) : ?>
		<h3><?php echo JText::sprintf('LIB_REDCORE_PAYMENT_SUBMIT_TO_PAYMENT_WAIT', $paymentTitle); ?></h3>
		<script type="text/javascript">
			window.onload = function(){
				document.getElementById("<?php echo $formName ?>").submit();
			}
		</script>
	<?php else : ?>
		<?php if ($paymentLogo) : ?>
			<input type="image" src="<?php echo $paymentLogo; ?>" alt="<?php echo JText::sprintf('LIB_REDCORE_PAYMENT_SUBMIT_TO_PAYMENT', $paymentName); ?>" />
		<?php else : ?>
			<input type="submit" value="<?php echo JText::sprintf('LIB_REDCORE_PAYMENT_SUBMIT_TO_PAYMENT', $paymentName); ?>" />
		<?php endif; ?>
	<?php endif; ?>
</form>
