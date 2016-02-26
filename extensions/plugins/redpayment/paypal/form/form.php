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

$params = $data['options']['params'];
$paymentData = $data['options']['paymentData'];
$fields = !empty($paymentData['hiddenFields']) ? $paymentData['hiddenFields'] : array();
$extensionName = $data['options']['extensionName'];
$ownerName = $data['options']['ownerName'];
$paymentName = $data['options']['paymentName'];
$paymentTitle = $data['options']['paymentTitle'];
$paymentLogo = $params->get('payment_logo');
$payment = $data['options']['payment'];
$formName = $data['options']['formName'];
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
	<?php else :
		$text = JText::sprintf('LIB_REDCORE_PAYMENT_SUBMIT_TO_PAYMENT', $paymentTitle);
	?>
		<input
			type="submit"
			name="<?php echo $formName ?>Submit"
			id="<?php echo $formName ?>Submit"
			class="btn btn-primary submitButton"
			value="<?php echo $text; ?>" />
		<?php if ($paymentLogo) : ?>
		<br />
		<img
			src="<?php echo $paymentLogo; ?>"
			alt="<?php echo $text; ?>" />
		<?php endif;
	endif; ?>
</form>
