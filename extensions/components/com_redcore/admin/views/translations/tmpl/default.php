<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
$action = JRoute::_('index.php?option=com_redcore&view=translations');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$selectedLanguage = $this->state->get('filter.language', '');
$input = JFactory::getApplication()->input;
$columns = isset($this->translationTable) ? array_merge($this->translationTable->readonlyColumns, $this->translationTable->columns) : array();
$doNotTranslate = array();

if (!empty($this->items)):
	$this->translationTable->primaryKeys = (array) $this->translationTable->primaryKeys;

	foreach ($columns as $key => $column):
		// We will display only first 5 columns
		if ($column == 'params' || $key > 7):
			unset($columns[$key]);
		endif;
	endforeach;
endif;
?>
<form action="<?php echo $action; ?>" name="adminForm" class="adminForm" id="adminForm" method="post">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'filtersHidden' => false,
				'searchField' => 'search_translations',
				'searchFieldSelector' => '#filter_search_translations',
				'limitFieldSelector' => '#list_translations_limit',
				'activeOrder' => $listOrder,
				'activeDirection' => $listDirn
			)
		)
	);
	?>
	<hr/>
	<?php if (empty($this->items)) : ?>
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="pagination-centered">
				<h3><?php echo JText::_('COM_REDCORE_NOTHING_TO_DISPLAY') ?></h3>
			</div>
		</div>
	<?php else : ?>
		<table class="table table-striped table-hover" id="translationList">
			<thead>
			<tr>
				<th style="width:1%" class="hidden-xs">
					<input type="checkbox" name="checkall-toggle" value=""
					       title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
				</th>
				<th class="nowrap center">
					<?php echo JHtml::_('rsearchtools.sort', 'JSTATUS', 't.rctranslations_state', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap hidden-xs">
					<?php echo JText::_('JSTATUS'); ?>
				</th>
				<th class="nowrap hidden-xs">
					<?php echo JHtml::_('rsearchtools.sort', 'JGLOBAL_FIELD_MODIFIED_LABEL', 't.rctranslations_modified', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap hidden-xs">
					<?php echo JHtml::_('rsearchtools.sort', 'JGRID_HEADING_LANGUAGE', 't.rctranslations_language', $listDirn, $listOrder); ?>
				</th>
				<?php foreach ($columns as $column) : ?>
					<th style="width:20%" class="nowrap hidden-xs">
						<?php echo JHtml::_('rsearchtools.sort', $this->translationTable->allColumns[$column]['title'], 't.' . $column, $listDirn, $listOrder); ?>
					</th>
				<?php endforeach; ?>
			</tr>
			</thead>
			<?php if ($this->items): ?>
				<tbody>
				<?php foreach ($this->items as $i => $item): ?>
					<?php
					// @todo Implement ACL
					$canEdit = true;
					$canCheckin = $canEdit;
					$primaryId = array();

					foreach ($this->translationTable->primaryKeys as $primaryKey):
						$primaryId[] = $item->{$primaryKey};
					endforeach;

					$editLink = $canEdit ? '<a href="'
						. JRoute::_('index.php?option=com_redcore&task=translation.edit'
							. '&translationTableName=' . $this->translationTableName
							. '&language=' . $this->escape($item->rctranslations_language)
							. '&id=' . (implode('###', $primaryId))
							. '&rctranslations_id=' . $item->rctranslations_id
						) . '">' : '';
					?>
					<tr>
						<td>
							<?php echo JHtml::_('grid.id', $i, $item->rctranslations_id); ?>
						</td>
						<td class="center">
							<?php echo !empty($item->rctranslations_modified) ?
								JHtml::_('rgrid.published', $item->rctranslations_state, $i, 'translations.', $canChange = true, 'cb') : '--'; ?>
						</td>
						<td>
							<?php echo $editLink ?>
								<span class="<?php echo $item->translationStatus['badge']; ?>">
									<?php echo JText::_($item->translationStatus['status']); ?>
								</span>
							<?php if (!empty($editLink)) : ?>
								</a>
							<?php endif; ?>
						</td>
						<td class="small hidden-phone">
							<?php if (!empty($item->rctranslations_modified)) : ?>
								<span class="hasTooltip" title="" data-original-title="<strong><?php echo $this->escape($item->rctranslations_modified); ?></strong>">
									<?php echo $this->escape($item->rctranslations_modified_user); ?>
								</span>
							<?php else: ?>
								--
							<?php endif; ?>
						</td>
						<td>
							<?php echo !empty($item->rctranslations_language) ? $this->escape($item->rctranslations_language) : '--'; ?>
						</td>
						<?php foreach ($columns as $column) : ?>
							<td style="word-break:break-all; word-wrap:break-word;">
								<?php echo $editLink ?>
								<?php if (!empty($item->{'t_' . $column})) : ?>
									<?php echo strip_tags(substr($item->{'t_' . $column}, 0, 150)); ?>
								<?php else : ?>
									<?php echo strip_tags(substr($item->{$column}, 0, 150)); ?>
								<?php endif; ?>
								<?php if (!empty($editLink)) : ?>
									</a>
								<?php endif; ?>
							</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
				</tbody>
			<?php endif; ?>
		</table>
		<?php echo $this->pagination->getPaginationLinks(null, array('showLimitBox' => false)); ?>
	<?php endif; ?>
	<div>
		<input type="hidden" name="option" value="com_redcore">
		<input type="hidden" name="task" value="">
		<input type="hidden" name="translationTableName" value="<?php echo $this->translationTableName; ?>">
		<input type="hidden" name="boxchecked" value="0">
		<input type="hidden" name="layout" value="<?php echo JFactory::getApplication()->input->getString('layout'); ?>">
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
