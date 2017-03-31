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

$action = JRoute::_('index.php?option=com_redcore&view=translation_tables');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$db = JFactory::getDbo();
?>
<form action="<?php echo $action; ?>" name="adminForm" class="adminForm" id="adminForm" method="post">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => array(
				'filtersHidden' => false,
				'searchField' => 'search_translation_tables',
				'searchFieldSelector' => '#filter_search_translation_tables',
				'limitFieldSelector' => '#list_translation_tables_limit',
				'activeOrder' => $listOrder,
				'activeDirection' => $listDirn
			)
		)
	);
	?>
	<hr/>
	<ul class="nav nav-tabs" id="mainTabs">
		<li role="presentation" class="active">
			<a href="#mainComponentTranslations" data-toggle="tab"><?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_INSTALLED_TABLES'); ?></a>
		</li>
		<li role="presentation">
			<a href="#mainComponentTranslationsXmls" data-toggle="tab" class="lc-not_installed_translations">
				<?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_AVAILABLE_TABLES'); ?> <span class="badge"><?php echo $this->xmlFilesAvailable; ?></span>
			</a>
		</li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active in" id="mainComponentTranslations">
			<div class="row-fluid">
				<table class="table table-striped table-hover" id="translationTablesList">
					<thead>
					<tr>
						<th class="hidden-xs">
							<input type="checkbox" name="checkall-toggle" value=""
							       title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
						</th>
						<th class="nowrap center">
							<?php echo JHtml::_('rsearchtools.sort', 'JSTATUS', 'w.state', $listDirn, $listOrder); ?>
						</th>
						<th class="nowrap">
							<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_TRANSLATION_TABLE_TITLE', 'tt.title', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_TRANSLATION_TABLE_EXTENSION_NAME', 'tt.extension_name', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_TRANSLATION_TABLE_XML_FILE', 'tt.xml_path', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_TRANSLATION_TABLE_ORIGINAL_TABLE_ROWS', 'original_rows', $listDirn, $listOrder); ?>
						</th>
						<th style="white-space:nowrap;">
							<?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_TRANSLATED_TABLE_ROWS'); ?>
							<br />
							<?php foreach ($this->languages as $languageKey => $language): ?>
								<span class="label label-primary"><?php echo $languageKey; ?></span>
							<?php endforeach; ?>
						</th>
						<th class="nowrap hidden-xs">
							<?php echo JHtml::_('rgrid.sort', 'COM_REDCORE_TRANSLATION_TABLE_TRANSLATE_COLUMNS', 'translate_columns', $listDirn, $listOrder); ?>
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
							$columns = explode(',', $item->translate_columns);
							$title = !empty($item->title) ? $item->title : $item->name;
							$xmlModified = false;
							$xmlDoc = RTranslationContentElement::getContentElement('', JPATH_SITE . $item->xml_path, $fullPath = true);
							if (!empty($item->xml_path)) :
								if (isset($xmlDoc->xml_hashed)) :
									$xmlModified = $xmlDoc->xml_hashed != $item->xml_hashed;
								endif;
							endif;
							?>
							<tr>
								<td>
									<?php echo JHtml::_('grid.id', $i, $item->id); ?>
								</td>
								<td class="center">
									<?php echo JHtml::_('rgrid.published', $item->state, $i, 'translation_tables.', $canChange = true, 'cb'); ?>
								</td>
								<td>
									<a class="hasTooltip"
									   data-original-title="<span style='word-break:break-all;'><?php echo str_replace('#__', $db->getPrefix(), $item->name); ?> - <?php echo $item->version; ?></span>"
									   href="<?php echo JRoute::_('index.php?option=com_redcore&task=translation_table.edit&id=' . $item->id); ?>">
									<?php echo $title; ?>
									</a>
								</td>
								<td>
									<?php echo $item->extension_name; ?>
								</td>
								<td style="word-break:break-all; word-wrap:break-word;">
									<?php if (!empty($item->xml_path)) : ?>
										<?php $badgeTitle = RTranslationContentElement::getPathWithoutBase($item->xml_path); ?>
										<?php if (empty($xmlDoc)) : ?>
											<span class="label label-danger hasTooltip" data-original-title="<span style='word-break:break-all;'><?php echo $badgeTitle; ?></span>">
												<?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_XML_MISSING'); ?>
											</span>
										<?php elseif ($xmlModified) : ?>
											<span class="label label-warning hasTooltip" data-original-title="<span style='word-break:break-all;'><?php echo $badgeTitle; ?></span>">
												<?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_XML_CHANGED'); ?>
											</span>
											<div><?php echo $xmlDoc->getFieldDifference(); ?></div>
										<?php else : ?>
											<span class="label label-success hasTooltip" data-original-title="<span style='word-break:break-all;'><?php echo $badgeTitle; ?></span>">
												<?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_XML_VALID'); ?>
											</span>
										<?php endif; ?>
									<?php else: ?>
										<span class="label label-success"><?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_NOT_USING_XML'); ?></span>
									<?php endif; ?>
								</td>
								<td>
									<span class="label label-primary"><?php echo $item->original_rows; ?></span>
								</td>
								<td>
									<?php foreach ($this->languages as $languageKey => $language): ?>
										<span style="display: inline-block;min-width:43px;" class="label label-primary hasTooltip" data-original-title="<?php echo $languageKey; ?>"><?php echo isset($item->translation_rows[$languageKey]) ? $item->translation_rows[$languageKey]->translation_rows : 0; ?></span>
									<?php endforeach; ?>
								</td>
								<td>
									<?php foreach ($columns as $column): ?>
										<span class="badge"><?php echo $column; ?></span>&nbsp;
									<?php endforeach; ?>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					<?php endif; ?>
				</table>
				<?php echo $this->pagination->getListFooter(); ?>
			</div>
		</div>
		<div class="tab-pane" id="mainComponentTranslationsXmls">
			<?php if (empty($this->xmlFiles)): ?>
				<br />
				<div class="alert alert-info">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<div class="pagination-centered">
						<h3><?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_NO_FILES_AVAILABLE') ?></h3>
					</div>
				</div>
			<?php else : ?>
				<div class="row-fluid">
				<?php echo RLayoutHelper::render(
					'translation.tables',
					array(
						'contentElements' => $this->xmlFiles,
					)
				); ?>
				</div>
			<?php endif; ?>
			<div class="clearfix"></div>
		</div>
	</div>

	<div>
		<input type="hidden" name="task" value="">
		<input type="hidden" name="boxchecked" value="0">
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
