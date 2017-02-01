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

$action = JRoute::_('index.php?option=com_redcore&view=payment_configurations');
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
				'searchField' => 'search_payment_configurations',
				'searchFieldSelector' => '#filter_search_payment_configurations',
				'limitFieldSelector' => '#list_payment_configurations_limit',
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
				<th>
					<?php echo JHtml::_('rgrid.sort', 'JSTATUS', 'state', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap">
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_PAYMENT_CONFIGURATION_PAYMENT_PLUGIN_NAME', 'payment_name', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_PAYMENT_CONFIGURATION_PAYMENT_EXTENSION_NAME', 'extension_name', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_PAYMENT_CONFIGURATION_PAYMENT_OWNER_NAME', 'owner_name', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JText::_('COM_REDCORE_PAYMENT_CONFIGURATION_TEST_CONFIGURATION'); ?>
				</th>
			</tr>
			</thead>
			<?php if ($this->items): ?>
				<tbody>
				<?php foreach ($this->items as $i => $item): ?>
					<?php
					$canChange = 1;
					$canEdit = 1;
					$canCheckin = 1;
					$isGlobal = is_null($item->id);
					$link = $isGlobal ? 'index.php?option=com_plugins&task=plugin.edit&extension_id=' . $item->extension_id . '&return=' . base64_encode($action)
						: 'index.php?option=com_redcore&task=payment_configuration.edit&id=' . $item->id;

					?>
					<tr>
						<td>
							<?php echo !$isGlobal ? JHtml::_('grid.id', $i, $item->id) : ''; ?>
						</td>
						<td>
							<?php echo !$isGlobal ? JHtml::_('rgrid.published', $item->state, $i, 'payment_configurations.', $canChange, 'cb') :
								JHtml::_('rgrid.published', $item->enabled, $i, 'plugins.', 0); ?>
						</td>
						<td>
							<?php if ($isGlobal) : ?>
								<?php echo $item->plugin_path_name; ?> (<a target="_blank" href="<?php echo JRoute::_($link); ?>"><?php echo JText::_('JEDIT'); ?></a>)
							<?php else: ?>
								<a href="<?php echo JRoute::_($link); ?>">
									<?php echo $item->plugin_path_name; ?>
								</a>
							<?php endif; ?>
						</td>
						<td style="word-break:break-all; word-wrap:break-word;">
							<?php echo !$isGlobal ? $item->extension_name : JText::_('COM_REDCORE_PAYMENT_CONFIGURATION_DEFAULT_CONFIGURATION_LABEL'); ?>
						</td>
						<td style="word-break:break-all; word-wrap:break-word;">
							<?php echo $item->owner_name; ?>
						</td>
						<td style="word-break:break-all; word-wrap:break-word;">
							<a class="btn btn-default" href="<?php echo $action; ?>&payment_name=<?php echo $item->element; ?>&payment_id=<?php echo $item->id; ?>&task=payment_configurations.test">
								<i class="icon-file-text"></i>
								<?php echo JText::_('COM_REDCORE_PAYMENT_CONFIGURATION_TEST_CONFIGURATION'); ?>
							</a>
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
