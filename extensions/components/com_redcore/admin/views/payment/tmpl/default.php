<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

$action = JRoute::_('index.php?option=com_redcore&view=payment');

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
JHtml::_('rsearchtools.main');
?>
<script type="text/javascript">
	var loadedTabs = {};
	(function ($) {
		function ajaxTabSetup(tabName) {
			$('a[href="#' + tabName + '"]').on('shown.bs.tab', function (e) {
				// Tab already loaded
				if (loadedTabs[tabName] == true) {
					return true;
				}

				// Perform the ajax request
				$.ajax({
					url: 'index.php?option=com_redcore&task=payment.ajax' + tabName + '&view=payment&id=<?php echo $this->item->id ?>',
					beforeSend: function (xhr) {
						$('.' + tabName + '-content .spinner').show();
						$('#paymentTabs').addClass('opacity-40');
					}
				}).done(function (data) {
					$('.' + tabName + '-content .spinner').hide();
					$('#paymentTabs').removeClass('opacity-40');
					$('.' + tabName + '-content').html(data);
					$('select').chosen();
					$('.chzn-search').hide();
					$('.hasTooltip').tooltip({"animation": true, "html": true, "placement": "top",
						"selector": false, "title": "", "trigger": "hover focus", "delay": 0, "container": false});
					loadedTabs[tabName] = true;
				});
			})
		}

		$(document).ready(function () {
			ajaxTabSetup('logs');
			$('#paymentTabs a[href="#logs"]').tab('show');
		});

	})(jQuery);
</script>
<form action="<?php echo $action; ?>" method="post" name="adminForm" id="adminForm"
      class="form-validate form-horizontal">
	<div class="container-fluid">
		<div class="col-md-6">
			<table class="table table-condensed table-striped">
				<tr>
					<th style="width: 25%;">
						<?php echo JText::_('COM_REDCORE_PAYMENT_EXTENSION_NAME'); ?>
					</th>
					<td>
						<?php echo $this->item->extension_name; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_OWNER_NAME'); ?>
					</th>
					<td>
						<?php echo $this->item->owner_name; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_PAYMENT_NAME'); ?>
					</th>
					<td>
						<?php echo $this->item->payment_name; ?>
						<?php if ($this->item->sandbox): ?>
							<label class="label label-warning"><?php echo JText::_('COM_REDCORE_PAYMENT_SANDBOX'); ?></label>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_CLIENT_EMAIL'); ?>
					</th>
					<td>
						<?php echo $this->item->client_email; ?>
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
						<?php echo JText::_('COM_REDCORE_PAYMENT_CREATED'); ?>
					</th>
					<td>
						<?php echo JHtml::_('date', $this->item->created_date, JText::_('DATE_FORMAT_LC2')); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_MODIFIED'); ?>
					</th>
					<td>
						<?php echo JHtml::_('date', $this->item->modified_date, JText::_('DATE_FORMAT_LC2')); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_CONFIRMED_DATE'); ?>
					</th>
					<td>
						<?php echo JHtml::_('date', $this->item->confirmed_date, JText::_('DATE_FORMAT_LC2')); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_RETRY_COUNTER'); ?>
					</th>
					<td>
						<?php echo $this->item->retry_counter; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_STATUS'); ?>
					</th>
					<td>
						<label class="label label-<?php echo RApiPaymentStatus::getStatusLabelClass($this->item->status); ?>">
							<?php echo RApiPaymentStatus::getStatusLabel($this->item->status); ?>
						</label>
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

		<div class="col-md-6">
			<table class="table table-condensed table-striped">
				<tr>
					<th style="width: 25%;">
						<?php echo JText::_('COM_REDCORE_PAYMENT_TRANSACTION_ID'); ?>
					</th>
					<td>
						<?php echo $this->item->transaction_id; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_ORDER_NAME'); ?>
					</th>
					<td>
						<?php echo $this->item->order_name; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_ORDER_ID'); ?>
					</th>
					<td>
						<?php echo $this->item->order_id; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_ORIGINAL_AMOUNT'); ?>
					</th>
					<td>
						<?php echo RHelperCurrency::getFormattedPrice($this->item->amount_original, $this->item->currency); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_TAX_AMOUNT'); ?>
					</th>
					<td>
						<?php echo RHelperCurrency::getFormattedPrice($this->item->amount_order_tax, $this->item->currency); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_TAX_DETAILS'); ?>
					</th>
					<td>
						<?php echo $this->item->order_tax_details; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_SHIPPING_AMOUNT'); ?>
					</th>
					<td>
						<?php echo RHelperCurrency::getFormattedPrice($this->item->amount_shipping, $this->item->currency); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_SHIPPING_DETAILS'); ?>
					</th>
					<td>
						<?php echo $this->item->shipping_details; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_PAYMENT_FEE_AMOUNT'); ?>
					</th>
					<td>
						<?php echo RHelperCurrency::getFormattedPrice($this->item->amount_payment_fee, $this->item->currency); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_AMOUNT'); ?>
					</th>
					<td>
						<?php echo RHelperCurrency::getFormattedPrice($this->item->amount_total, $this->item->currency); ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_AMOUNT_PAID'); ?>
					</th>
					<td>
						<?php echo RHelperCurrency::getFormattedPrice($this->item->amount_paid, $this->item->currency); ?>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<!-- hidden fields -->
	<input type="hidden" name="option" value="com_redcore">
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
	<input type="hidden" name="task" value="">
	<?php echo JHTML::_('form.token'); ?>
</form>

<ul class="nav nav-tabs" id="paymentTabs">
	<li>
		<a href="#logs" data-toggle="tab">
			<?php echo JText::_('COM_REDCORE_PAYMENT_LOG'); ?>
		</a>
	</li>
</ul>
<div class="tab-content">
	<div class="tab-pane" id="logs">
		<div class="container-fluid">
			<div class="row-fluid logs-content">
				<div class="spinner pagination-centered">
					<?php echo JHtml::image('media/redcore/images/ajax-loader.gif', '') ?>
					dsadsa
				</div>
			</div>
		</div>
	</div>
</div>
