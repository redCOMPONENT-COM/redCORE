<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
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
$columns = array();
$fieldsXml = $this->contentElement->getTranslateFields();
$doNotTranslate = array();

// We are adding fields that do not require translation
foreach ($fieldsXml as $field)
{
	if ((string) $field['translate'] == '0' && (string) $field['type'] == 'titletext')
	{
		$doNotTranslate[] = (string) $field['name'];
	}
}

if (!empty($this->items)):
	$columns = (array) $this->translationTable->columns;
	$columns = array_merge($doNotTranslate, $columns);
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
	<?php if (empty($selectedLanguage)) : ?>
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="pagination-centered">
				<h3><?php echo JText::_('COM_REDCORE_TRANSLATIONS_SELECT_LANGUAGE') ?></h3>
			</div>
		</div>
	<?php elseif (empty($this->items)) : ?>
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
				<th class="nowrap hidden-xs">
					<?php echo JText::_('JSTATUS'); ?>
				</th>
				<th class="nowrap hidden-xs">
					<?php echo JText::_('JGLOBAL_FIELD_MODIFIED_LABEL'); ?>
				</th>
				<th class="nowrap hidden-xs">
					<?php echo JHtml::_('rsearchtools.sort', 'JGRID_HEADING_LANGUAGE', 't.rctranslations_language', $listDirn, $listOrder); ?>
				</th>
				<?php foreach ($columns as $column) : ?>
					<th style="width:20%" class="nowrap hidden-xs">
						<?php echo JHtml::_('rsearchtools.sort', $column, 't.' . $column, $listDirn, $listOrder); ?>
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
							. '&contentelement=' . $this->contentElementName
							. '&language=' . $selectedLanguage
							. '&id=' . (implode('###', $primaryId))
							. '&rctranslations_id=' . $item->rctranslations_id
						) . '">' : '';
					?>
					<tr>
						<td>
							<?php echo JHtml::_('grid.id', $i, $item->rctranslations_id); ?>
						</td>
						<td>
							<span class="<?php echo $item->translationStatus['badge']; ?>">
								<?php echo JText::_($item->translationStatus['status']); ?>
							</span>
						</td>
						<td>
							<?php echo !empty($item->rctranslations_modified) ? $this->escape($item->rctranslations_modified) : '--'; ?>
						</td>
						<td>
							<?php echo !empty($item->rctranslations_language) ? $this->escape($item->rctranslations_language) : '--'; ?>
						</td>
						<?php foreach ($columns as $column) : ?>
							<td>
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
		<input type="hidden" name="language" value="<?php echo $selectedLanguage; ?>">
		<input type="hidden" name="component" value="<?php echo $this->contentElement->extension; ?>">
		<input type="hidden" name="contentelement" value="<?php echo $this->contentElementName; ?>">
		<input type="hidden" name="boxchecked" value="0">
		<input type="hidden" name="layout" value="<?php echo JFactory::getApplication()->input->getString('layout'); ?>">
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
