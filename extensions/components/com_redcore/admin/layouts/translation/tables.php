<?php
/**
 * @package     Redcore.Translation
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

$contentElements = !empty($displayData['contentElements']) ? $displayData['contentElements'] : array();
$componentName = !empty($displayData['componentName']) ? $displayData['componentName'] : '';
$column = 0;
?>
<script type="text/javascript">
	function setContentElement(contentElement, componentName, task)
	{
		document.getElementById('contentElement').value = contentElement;
		document.getElementById('componentName').value = componentName;

		if (task != '')
		{
			if (task == 'translation_tables.delete')
			{
				if (confirm('<?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_CONTENT_ELEMENT_UNINSTALL_CONFIRM', true); ?>'))
					submitAction(task, document.getElementById('adminForm'));
			}
			else if (task == 'translation_tables.deleteXmlFile')
			{
				if (confirm('<?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_CONTENT_ELEMENT_DELETE_CONFIRM', true); ?>'))
					submitAction(task, document.getElementById('adminForm'));
			}
			else if (task == 'translation_tables.purgeTable')
			{
				if (confirm('<?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_CONTENT_ELEMENT_TRUNCATE_CONFIRM', true); ?>'))
					submitAction(task, document.getElementById('adminForm'));
			}
			else
			{
				submitAction(task, document.getElementById('adminForm'));
			}
		}
	}

	function submitAction(task, form)
	{
		if (typeof Joomla.submitform == 'function')
		{
			Joomla.submitform(task, form);
		}
		else
		{
			if (typeof(task) !== 'undefined' && task !== "") {
				form.task.value = task;
			}

			// Submit the form.
			if (typeof form.onsubmit == 'function') {
				form.onsubmit();
			}
			if (typeof form.fireEvent == "function") {
				form.fireEvent('submit');
			}
			form.submit();
		}
	}
</script>
<div class="tab-pane" id="mainComponentTranslations">
	<h4 class="tab-description"><?php echo JText::_('COM_REDCORE_TRANSLATIONS_DESC'); ?></h4>
	<div class="row-fluid">
		<div class="span6 well">
			<div class="form-group">
				<div class="control-label">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_CONTENT_ELEMENT_FILES_MASS_ACTIONS'); ?>
				</div>
				<div class="controls">
					<button
						class="btn btn-success"
						type="button"
						onclick="setContentElement('all', '<?php echo $componentName; ?>', 'translation_tables.installFromXml')">
						<i class="icon-cogs"></i>
						<?php echo JText::_('JTOOLBAR_INSTALL') . ' ' . JText::_('JALL'); ?>
					</button>
					<button
						class="btn btn-danger"
						type="button"
						onclick="setContentElement('all', '<?php echo $componentName; ?>', 'translation_tables.deleteXmlFile')">
						<i class="icon-remove"></i>
						<?php echo JText::_('JTOOLBAR_DELETE') . ' ' . JText::_('JALL'); ?>
					</button>
				</div>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<?php if (empty($contentElements)): ?>
			<div class="alert alert-info">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<div class="pagination-centered">
					<h3><?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_CONTENT_ELEMENT_NO_FILES_AVAILABLE') ?></h3>
				</div>
			</div>
		<?php else : ?>
		<?php foreach ($contentElements as $contentElement):
			$disabled = empty($contentElement->table) ? ' disabled="disabled" ' : '';
		?>
		<div class="span4 well">
			<h4>
				<?php echo !empty($contentElement->name) ? $contentElement->name : JText::_('JNONE'); ?>
				<br />
				<small><?php echo RTranslationContentElement::getPathWithoutBase($contentElement->contentElementXmlPath); ?></small>
			</h4>
			<table class="table table-striped adminlist">
				<tbody>
				<tr>
					<td>
						<strong><?php echo JText::_('JAUTHOR'); ?>:</strong>
					</td>
					<td>
						<strong><?php echo !empty($contentElement->xml->author) ? $contentElement->xml->author : ''; ?></strong>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php echo JText::_('JVERSION'); ?>:</strong>
					</td>
					<td>
						<strong><?php echo !empty($contentElement->xml->version) ? $contentElement->xml->version : ''; ?></strong>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php echo JText::_('JGLOBAL_DESCRIPTION'); ?>:</strong>
					</td>
					<td>
						<strong><?php echo !empty($contentElement->xml->description) ? $contentElement->xml->description : ''; ?></strong>
					</td>
				</tr>
				<?php if (isset($contentElement->mainTable)): ?>
					<tr>
						<td>
							<strong><?php echo JText::_('NOTICE'); ?>:</strong>
						</td>
						<td style="word-break:break-all;">
							<strong>
								<?php echo JText::sprintf(
									'COM_REDCORE_TRANSLATION_TABLE_ALREADY_INSTALLED',
									$contentElement->mainTable->name,
									$contentElement->mainTable->version,
									$contentElement->mainTable->xml_path
								); ?>
							</strong>
						</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>
			<button
				class="btn btn-sm btn-success"
				type="button"
				<?php echo $disabled; ?>
				onclick="setContentElement('<?php echo $contentElement->contentElementXml; ?>', '<?php echo $contentElement->extension; ?>', 'translation_tables.installFromXml')">
				<i class="icon-cogs"></i>
				<?php echo JText::_('JTOOLBAR_INSTALL') ?>
			</button>
			<button
				class="btn btn-sm btn-danger"
				type="button"
				onclick="setContentElement('<?php echo $contentElement->contentElementXml; ?>', '<?php echo $contentElement->extension; ?>', 'translation_tables.deleteXmlFile')">
				<i class="icon-remove"></i>
				<?php echo JText::_('JTOOLBAR_DELETE') ?>
			</button>
		</div>
		<?php if ((++$column) % 3 == 0 ) : ?>
			</div>
			<div class="row-fluid">
		<?php endif; ?>
		<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<input type="hidden" id="contentElement" name="contentElement" />
	<input type="hidden" id="componentName" name="component" />
</div>
