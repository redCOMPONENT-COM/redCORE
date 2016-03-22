<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$data = $displayData;

$state = $data['state'];
$items = $data['items'];
$pagination = $data['pagination'];
$formName = $data['formName'];
$showToolbar = isset($data['showToolbar']) ? $data['showToolbar'] : false;
$return = isset($data['return']) ? $data['return'] : null;
$action = RRoute::_('index.php?option=com_redcore&view=payment_logs');

// Allow to override the form action
if (isset($data['action']))
{
	$action = $data['action'];
}

$paymentId = JFactory::getApplication()->input->getInt('id');
$listOrder = $state->get('list.ordering');
$listDirn = $state->get('list.direction');
$saveOrder = $listOrder == 'ordering';

$searchToolsOptions = array(
	"searchFieldSelector" => "#filter_search_payment_logs",
	"orderFieldSelector" => "#list_fullordering",
	"searchField" => "search_payment_logs",
	"limitFieldSelector" => "#list_payment_log_limit",
	"activeOrder" => $listOrder,
	"activeDirection" => $listDirn,
	"formSelector" => ("#" . $formName),
	"filtersHidden" => (bool) empty($data['activeFilters'])
);
?>
<script type="text/javascript">
	(function ($) {
		$(document).ready(function () {
			$('#<?php echo $formName; ?>').searchtools(
				<?php echo json_encode($searchToolsOptions); ?>
			);
		});
	})(jQuery);
</script>
	<?php
	// Render the toolbar?
	if ($showToolbar)
	{
		echo RLayoutHelper::render('payment.logs_toolbar', $data);
	}
	?>
<form action="<?php echo $action; ?>" name="<?php echo $formName; ?>" class="adminForm" id="<?php echo $formName; ?>" method="post">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => (object) array(
					'filterForm' => $data['filterForm'],
					'activeFilters' => $data['activeFilters']
				),
			'options' => $searchToolsOptions
		)
	);
	?>

	<hr/>
	<?php if (empty($items)) : ?>
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="pagination-centered">
				<h3><?php echo JText::_('COM_REDCORE_NOTHING_TO_DISPLAY') ?></h3>
			</div>
		</div>
	<?php else : ?>
		<table class="table table-striped table-hover toggle-circle-filled" id="payment_logList">
			<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value=""
					       title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
				</th>
				<th width="20%" class="nowrap">
					<?php echo JHtml::_(
						'rsearchtools.sort', 'COM_REDCORE_PAYMENT_CREATED', 'l.created_date', $listDirn, $listOrder, null, 'asc', '', null, $formName
					); ?>
				</th>
				<th width="20%" class="nowrap">
					<?php echo JHtml::_(
						'rsearchtools.sort', 'COM_REDCORE_PAYMENT_LOG_AMOUNT', 'l.amount', $listDirn, $listOrder, null, 'asc', '', null, $formName
					); ?>
				</th>
				<th class="nowrap">
					<?php echo JHtml::_(
						'rsearchtools.sort', 'COM_REDCORE_PAYMENT_LOG_MESSAGE', 'l.message_text', $listDirn, $listOrder, null, 'asc', '', null, $formName
					); ?>
				</th>
				<th width="15%" class="nowrap">
					<?php echo JHtml::_(
						'rsearchtools.sort', 'COM_REDCORE_PAYMENT_LOG_STATUS', 'l.status', $listDirn, $listOrder, null, 'asc', '', null, $formName
					); ?>
				</th>
			</tr>
			</thead>
			<?php if ($items): ?>
				<tbody>
				<?php foreach ($items as $i => $item): ?>
					<?php
					$itemUrl = 'index.php?option=com_redcore&task=payment_log.edit&id=' . $item->id
						. '&jform[payment_id]=' . $item->payment_id . '&from_payment=1';

					if ($return)
					{
						$itemUrl .= '&return=' . $return;
					}
					?>
					<tr class="<?php echo RApiPaymentStatus::getStatusLabelClass($item->status); ?>">
						<td>
							<?php echo JHtml::_('rgrid.id', $i, $item->id, false, 'cid', $formName); ?>
						</td>
						<td>
							<a href="<?php echo RRoute::_($itemUrl); ?>">
								<?php echo JHtml::_('date', $item->created_date, JText::_('DATE_FORMAT_LC2')); ?>
							</a>
						</td>
						<td>
							<?php echo RHelperCurrency::getFormattedPrice($item->amount, $item->currency); ?>
						</td>
						<td>
							<?php echo $item->message_text ?>
						</td>
						<td>
							<?php echo RApiPaymentStatus::getStatusLabel($item->status); ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			<?php endif; ?>
		</table>

		<?php echo $pagination->getPaginationLinks('tab.pagination.links', array('showLimitBox' => false)); ?>
	<?php endif; ?>

	<div>
		<input type="hidden" name="task" value="payment_log.saveModelState">
		<?php if ($return) : ?>
			<input type="hidden" name="return" value="<?php echo $return ?>">
		<?php endif; ?>
		<input type="hidden" name="filter[payment_id]" value="<?php echo $paymentId ?>">
		<input type="hidden" name="from_payment" value="1">
		<input type="hidden" name="boxchecked" value="0">
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
