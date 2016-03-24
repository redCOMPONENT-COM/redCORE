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

$action = JRoute::_('index.php?option=com_redcore&view=payment_dashboard');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$year = date('Y');
$month = date('m');
$day = date('d');
$yesterday = date('Y-m-d', strtotime('yesterday'));
$yesterday = explode('-', $yesterday);
$lastMonth = date('Y-m-d', strtotime('today - 1 month'));
$lastMonth = explode('-', $lastMonth);
$lastYear = date('Y', strtotime('today - 1 year'));

$statistics = isset($this->paymentData['overall']['amounts']['all']['sum']) ? $this->paymentData['overall']['amounts']['all']['sum'] : array();
?>
<form action="<?php echo $action; ?>" name="adminForm" class="adminForm" id="adminForm" method="post">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'filtersHidden' => false,
				'searchField' => 'search_payment_dashboard',
				'searchFieldSelector' => '#filter_search_payment_dashboard',
				'limitFieldSelector' => '#list_payment_dashboard_limit',
				'activeOrder' => $listOrder,
				'activeDirection' => $listDirn
			)
		)
	);
	?>
	<div class="container-fluid">
		<div class="col-lg-6 col-md-12">
			<h2><?php echo JText::_('COM_REDCORE_PAYMENT_DASHBOARD_CHART'); ?></h2>
			<?php echo RLayoutHelper::render(
				'chart.chart',
				array(
					'view' => $this,
					'options' => array(
						'chartOptions' => array(
							'legendTemplate' => RHtmlRchart::getDefaultLegendHtml()
						),
						'chartType' => $this->chartType,
						'chartData' => $this->chartData,
						'chartId' => 'mainPaymentChart',
					)
				)
			); ?>
		</div>
		<div class="col-lg-6 col-md-12">
			<h2><?php echo JText::_('COM_REDCORE_PAYMENT_DASHBOARD_STATISTICS'); ?></h2>
			<table class="table table-condensed table-striped">
				<tr>
					<th style="width: 40%;">
					</th>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENTS'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_AMOUNT'); ?>
					</th>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_DASHBOARD_TODAY'); ?>
					</th>
					<td>
						<?php if (isset($statistics[$year]['val'][$month]['val'][$day]['count'])) :
							echo $statistics[$year]['val'][$month]['val'][$day]['count'];
						else :
							echo 0;
						endif; ?>
					</td>
					<td>
						<?php if (isset($statistics[$year]['val'][$month]['val'][$day]['sum'])) :
							echo RHelperCurrency::getFormattedPrice($statistics[$year]['val'][$month]['val'][$day]['sum'], $this->paymentData['overall']['currency']);
						else :
							echo RHelperCurrency::getFormattedPrice(0, $this->paymentData['overall']['currency']);
						endif; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_DASHBOARD_YESTERDAY'); ?>
					</th>
					<td>
						<?php if (isset($statistics[$yesterday[0]]['val'][$yesterday[1]]['val'][$yesterday[2]]['count'])) :
							echo $statistics[$yesterday[0]]['val'][$yesterday[1]]['val'][$yesterday[2]]['count'];
						else :
							echo 0;
						endif; ?>
					</td>
					<td>
						<?php if (isset($statistics[$yesterday[0]]['val'][$yesterday[1]]['val'][$yesterday[2]]['sum'])) :
							echo RHelperCurrency::getFormattedPrice($statistics[$yesterday[0]]['val'][$yesterday[1]]['val'][$yesterday[2]]['sum'], $this->paymentData['overall']['currency']);
						else :
							echo RHelperCurrency::getFormattedPrice(0, $this->paymentData['overall']['currency']);
						endif; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_DASHBOARD_THIS_MONTH'); ?>
					</th>
					<td>
						<?php if (isset($statistics[$year]['val'][$month]['count'])) :
							echo $statistics[$year]['val'][$month]['count'];
						else :
							echo 0;
						endif; ?>
					</td>
					<td>
						<?php if (isset($statistics[$year]['val'][$month]['sum'])) :
							echo RHelperCurrency::getFormattedPrice($statistics[$year]['val'][$month]['sum'], $this->paymentData['overall']['currency']);
						else :
							echo RHelperCurrency::getFormattedPrice(0, $this->paymentData['overall']['currency']);
						endif; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_DASHBOARD_LAST_MONTH'); ?>
					</th>
					<td>
						<?php if (isset($statistics[$lastMonth[0]]['val'][$lastMonth[1]]['count'])) :
							echo $statistics[$lastMonth[0]]['val'][$lastMonth[1]]['count'];
						else :
							echo 0;
						endif; ?>
					</td>
					<td>
						<?php if (isset($statistics[$lastMonth[0]]['val'][$lastMonth[1]]['sum'])) :
							echo RHelperCurrency::getFormattedPrice($statistics[$lastMonth[0]]['val'][$lastMonth[1]]['sum'], $this->paymentData['overall']['currency']);
						else :
							echo RHelperCurrency::getFormattedPrice(0, $this->paymentData['overall']['currency']);
						endif; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_DASHBOARD_THIS_YEAR'); ?>
					</th>
					<td>
						<?php if (isset($statistics[$year]['count'])) :
							echo $statistics[$year]['count'];
						else :
							echo 0;
						endif; ?>
					</td>
					<td>
						<?php if (isset($statistics[$year]['sum'])) :
							echo RHelperCurrency::getFormattedPrice($statistics[$year]['sum'], $this->paymentData['overall']['currency']);
						else :
							echo RHelperCurrency::getFormattedPrice(0, $this->paymentData['overall']['currency']);
						endif; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_DASHBOARD_LAST_YEAR'); ?>
					</th>
					<td>
						<?php if (isset($statistics[$lastYear]['count'])) :
							echo $statistics[$lastYear]['count'];
						else :
							echo 0;
						endif; ?>
					</td>
					<td>
						<?php if (isset($statistics[$lastYear]['sum'])) :
							echo RHelperCurrency::getFormattedPrice($statistics[$lastYear]['sum'], $this->paymentData['overall']['currency']);
						else :
							echo RHelperCurrency::getFormattedPrice(0, $this->paymentData['overall']['currency']);
						endif; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_DASHBOARD_STATISTICS_MAX_IN_DAY'); ?>
					</th>
					<td>
						<?php if (isset($statistics['maxCount'])) :
							echo $statistics['maxCount'];
						else :
							echo 0;
						endif; ?>
					</td>
					<td>
						<?php if (isset($statistics['maxSum'])) :
							echo RHelperCurrency::getFormattedPrice($statistics['maxSum'], $this->paymentData['chart']['currency']);
						else :
							echo RHelperCurrency::getFormattedPrice(0, $this->paymentData['overall']['currency']);
						endif; ?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo JText::_('COM_REDCORE_PAYMENT_DASHBOARD_AVERAGE_DAY_YEAR'); ?>
					</th>
					<td>
						<?php if (isset($statistics[$year]['averageCount'])) :
							echo $statistics[$year]['averageCount'];
						else :
							echo 0;
						endif; ?>
					</td>
					<td>
						<?php if (isset($statistics['averageSum'])) :
							echo RHelperCurrency::getFormattedPrice($statistics[$year]['averageSum'], $this->paymentData['overall']['currency']);
						else :
							echo RHelperCurrency::getFormattedPrice(0, $this->paymentData['overall']['currency']);
						endif; ?>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="container-fluid">
		<h2><?php echo JText::_('COM_REDCORE_PAYMENT_DASHBOARD_STATISTICS_DETAILED'); ?></h2>
		<?php foreach ($this->paymentData['chart']['amounts'] as $itemName => $values) : ?>
			<div class="col-lg-3 col-md-6">
				<h3><?php echo $itemName; ?></h3>
				<table class="table table-condensed table-striped">
					<tr>
						<th style="width: 40%;">
						</th>
						<th>
							<?php echo JText::_('COM_REDCORE_PAYMENTS'); ?>
						</th>
						<th>
							<?php echo JText::_('COM_REDCORE_PAYMENT_AMOUNT'); ?>
						</th>
					</tr>
					<tr>
						<th>
							<?php echo JText::_('COM_REDCORE_PAYMENT_DASHBOARD_STATISTICS_SUM'); ?>
						</th>
						<td>
							<?php echo $values['sum']['count']; ?>
						</td>
						<td>
							<?php echo RHelperCurrency::getFormattedPrice($values['sum']['sum'], $this->paymentData['chart']['currency']); ?>
						</td>
					</tr>
					<tr>
						<th>
							<?php echo JText::_('COM_REDCORE_PAYMENT_DASHBOARD_STATISTICS_MAX_IN_DAY'); ?>
						</th>
						<td>
							<?php echo $values['sum']['maxCount']; ?>
						</td>
						<td>
							<?php echo RHelperCurrency::getFormattedPrice($values['sum']['maxSum'], $this->paymentData['chart']['currency']); ?>
						</td>
					</tr>
					<tr>
						<th>
							<?php echo JText::_('COM_REDCORE_PAYMENT_DASHBOARD_STATISTICS_AVERAGE'); ?>
						</th>
						<td>
							<?php echo $values['sum']['averageCount']; ?>
						</td>
						<td>
							<?php echo RHelperCurrency::getFormattedPrice($values['sum']['averageSum'], $this->paymentData['chart']['currency']); ?>
						</td>
					</tr>
				</table>
			</div>
		<?php endforeach; ?>
	</div>

	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
	<?php echo JHtml::_('form.token'); ?>
</form>
