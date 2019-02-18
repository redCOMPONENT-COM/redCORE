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

$action = JRoute::_('index.php?option=com_redcore&view=payments');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
?>
<form action="<?php echo $action; ?>" name="adminForm" class="adminForm" id="adminForm" method="post">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'filtersHidden' => false,
				'searchField' => 'search_payments',
				'searchFieldSelector' => '#filter_search_payments',
				'limitFieldSelector' => '#list_payments_limit',
				'activeOrder' => $listOrder,
				'activeDirection' => $listDirn
			)
		)
	);
	?>
	<hr/>
	<div class="row-fluid">
		<table class="table table-striped table-hover" id="oauthClientsList">
			<thead>
			<tr>
				<th class="hidden-xs">
					<input type="checkbox" name="checkall-toggle" value=""
					       title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
				</th>
				<th class="nowrap">
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_PAYMENT_ORDER_NAME', 'p.order_name', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap">
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_PAYMENT_PAYMENT_NAME', 'p.payment_name', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_PAYMENT_EXTENSION_NAME', 'p.extension_name', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_PAYMENT_OWNER_NAME', 'p.owner_name', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_PAYMENT_AMOUNT', 'p.amount_total', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_PAYMENT_AMOUNT_PAID', 'p.amount_paid', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_PAYMENT_STATUS', 'p.status', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap hidden-xs">
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_PAYMENT_MODIFIED', 'p.modified_date', $listDirn, $listOrder); ?>
				</th>
			</tr>
			</thead>
			<?php if ($this->items): ?>
				<tbody>
				<?php foreach ($this->items as $i => $item): ?>
					<tr class="<?php echo RApiPaymentStatus::getStatusLabelClass($item->status); ?>">
						<td>
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td>
							<a href="<?php echo JRoute::_('index.php?option=com_redcore&task=payment.edit&id=' . $item->id); ?>">
							<?php echo $item->order_name; ?>
							</a>
							<?php if ($item->sandbox): ?>
								<label class="label label-warning"><?php echo JText::_('COM_REDCORE_PAYMENT_SANDBOX'); ?></label>
							<?php endif; ?>
						</td>
						<td>
							<?php echo $item->payment_name; ?>
						</td>
						<td>
							<?php echo $item->extension_name; ?>
						</td>
						<td>
							<?php echo $item->owner_name; ?>
						</td>
						<td>
							<?php echo RHelperCurrency::getFormattedPrice($item->amount_total, $item->currency); ?>
						</td>
						<td>
							<?php echo RHelperCurrency::getFormattedPrice($item->amount_paid, $item->currency); ?>
						</td>
						<td>
							<?php echo RApiPaymentStatus::getStatusLabel($item->status); ?>
						</td>
						<td>
							<?php echo JHtml::_('date', $item->modified_date, JText::_('DATE_FORMAT_LC2')); ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			<?php endif; ?>
		</table>
		<?php echo $this->pagination->getListFooter(); ?>
	</div>

	<div>
		<input type="hidden" name="task" value="">
		<input type="hidden" name="boxchecked" value="0">
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
