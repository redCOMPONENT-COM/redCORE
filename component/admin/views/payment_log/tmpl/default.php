<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

$action = JRoute::_('index.php?option=com_redcore&view=payment_log');

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
?>
<form action="<?php echo $action; ?>" method="post" name="adminForm" id="adminForm"
      class="form-validate form-horizontal">
	<div class="container-fluid">
		<div class="col-md-12">
			<h3><label class="label label-primary"><?php echo JText::_('COM_REDCORE_PAYMENT_LOG_TITLE_TEXT'); ?></label></h3>
			<?php echo $this->item->message_text; ?>
			<br/><br/>
		</div>
		<div class="col-md-6">
			<table class="table table-condensed table-striped">
				<tr>
					<th style="width: 25%;">
						<?php echo JText::_('COM_REDCORE_PAYMENT_ID'); ?>
					</th>
					<td>
						<?php echo $this->item->payment_id; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_TRANSACTION_ID'); ?>
					</th>
					<td>
						<?php echo $this->item->transaction_id; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_LOG_AMOUNT'); ?>
					</th>
					<td>
						<?php echo RHelperCurrency::getFormattedPrice($this->item->amount, $this->item->currency); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_LOG_COUPON_CODE'); ?>
					</th>
					<td>
						<?php echo $this->item->coupon_code; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_STATUS'); ?>
					</th>
					<td>
						<?php echo RApiPaymentStatus::getStatusLabel($this->item->status); ?>
					</td>
				</tr>
			</table>
		</div>

		<div class="col-md-6">
			<table class="table table-condensed table-striped">
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_CREATED'); ?>
					</th>
					<td>
						<?php echo JHtml::_('date', $this->item->created_date, JText::_('DATE_FORMAT_LC2')); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_LOG_IP_ADDRESS'); ?>
					</th>
					<td>
						<?php echo $this->item->ip_address; ?>
					</td>
				</tr>
				<tr>
					<th style="width: 25%;word-wrap:break-word;">
						<?php echo JText::_('COM_REDCORE_PAYMENT_LOG_MESSAGE_URI'); ?>
					</th>
					<td>
						<?php echo $this->item->message_uri; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_CUSTOMER_NOTE'); ?>
					</th>
					<td>
						<?php echo $this->item->customer_note; ?>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div>
		<h3><label class="label label-primary"><?php echo JText::_('COM_REDCORE_PAYMENT_LOG_MESSAGE_TEXT'); ?></label></h3><br />
		<?php echo $this->item->message_post; ?>
	</div>

	<!-- hidden fields -->
	<input type="hidden" name="option" value="com_redcore">
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
	<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->get('return'); ?>">
	<input type="hidden" name="task" value="">
	<?php echo JHTML::_('form.token'); ?>
</form>
