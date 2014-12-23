<?php
/**
 * @package     Redcore.Translation
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

$contentElements = !empty($displayData['contentElements']) ? $displayData['contentElements'] : array();
$missingContentElements = !empty($displayData['missingContentElements']) ? $displayData['missingContentElements'] : array();
$column = 0;
?>
<script type="text/javascript">
	function setContentElement(contentElement, task)
	{
		document.getElementById('contentElement').value = contentElement;

		if (task != '')
		{
			if (task == 'config.uninstallContentElement')
			{
				if (confirm('<?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_UNINSTALL_CONFIRM', true); ?>'))
					submitAction(task, document.getElementById('adminForm'));
			}
			else if (task == 'config.deleteContentElement')
			{
				if (confirm('<?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_DELETE_CONFIRM', true); ?>'))
					submitAction(task, document.getElementById('adminForm'));
			}
			else if (task == 'config.purgeContentElement')
			{
				if (confirm('<?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_PURGE_CONFIRM', true); ?>'))
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
				document.getElementById('adminForm').task.value = task;
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
	<p class="tab-description"><?php echo JText::_('COM_REDCORE_TRANSLATIONS_DESC'); ?></p>
	<p class="tab-description">
		<?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_PLUGIN_LABEL'); ?>
		<?php if (RTranslationHelper::$pluginParams->get('enable_translations', 0) == 1) : ?>
			<span class="label label-success"><?php echo JText::_('JENABLED'); ?></span>
		<?php else : ?>
			<span class="label label-danger"><?php echo JText::_('JDISABLED'); ?></span>
		<?php endif; ?>

		<?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_LANGUAGE_FILTER'); ?>
		<?php if (JPluginHelper::isEnabled('system', 'languagefilter')) : ?>
			<span class="label label-success"><?php echo JText::_('JENABLED'); ?></span>
		<?php else : ?>
			<span class="label label-danger"><?php echo JText::_('JDISABLED'); ?></span>
		<?php endif; ?>
	</p>
	<div class="row">
		<div class="col-md-6 well">
			<div class="form-group">
				<div class="control-label">
					<?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_TITLE'); ?>
				</div>
				<div class="controls">
					<input type="file" multiple="multiple" name="redcoreContentElement[]" id="redcoreContentElement" accept="application/xml" class="inputbox" />
					<button
						class="btn btn-success"
						type="button"
						onclick="setContentElement('', 'config.uploadContentElement')">
						<i class="icon-upload"></i>
						<?php echo JText::_('JTOOLBAR_UPLOAD') ?>
					</button>
				</div>
			</div>
			<div class="form-group" style="margin-top:40px;margin-bottom: 0;">
				<div class="control-label">
					<?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_FILES_MASS_ACTIONS'); ?>
				</div>
				<div class="controls">
					<button
						class="btn btn-success"
						type="button"
						onclick="setContentElement('all', 'config.installContentElement')">
						<i class="icon-cogs"></i>
						<?php echo JText::_('JTOOLBAR_INSTALL') . ' / ' . JText::_('COM_REDCORE_UPDATE'); ?>
					</button>
					<button
						class="btn btn-danger"
						type="button"
						onclick="setContentElement('all', 'config.uninstallContentElement')">
						<i class="icon-cogs"></i>
						<?php echo JText::_('JTOOLBAR_UNINSTALL') ?>
					</button>
					<button
						class="btn btn-danger"
						type="button"
						onclick="setContentElement('all', 'config.deleteContentElement')">
						<i class="icon-remove"></i>
						<?php echo JText::_('JTOOLBAR_DELETE') ?>
					</button>
					<button
						class="btn btn-danger"
						type="button"
						onclick="setContentElement('all', 'config.purgeContentElement')">
						<i class="icon-trash"></i>
						<?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_PURGE_TRANSLATIONS') ?>
					</button>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<?php if (empty($contentElements)): ?>
			<div class="alert alert-info">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<div class="pagination-centered">
					<h3><?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_NO_FILES_AVAILABLE') ?></h3>
				</div>
			</div>
		<?php else : ?>
		<?php foreach ($contentElements as $contentElement): ?>
		<?php $status = $contentElement->getStatus() ?>
		<div class="col-md-4 well">
			<h4>
				<?php echo !empty($contentElement->name) ? $contentElement->name : $contentElement->contentElementXml; ?>
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
				<tr>
					<td>
						<strong><?php echo JText::_('JSTATUS'); ?>:</strong>
					</td>
					<td>
						<strong><?php echo $status; ?></strong>
					</td>
				</tr>
				</tbody>
			</table>
			<?php if ($status == JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_NOT_INSTALLED')): ?>
				<button
					class="btn btn-sm btn-success"
					type="button"
					onclick="setContentElement('<?php echo $contentElement->contentElementXml; ?>', 'config.installContentElement')">
					<i class="icon-cogs"></i>
					<?php echo JText::_('JTOOLBAR_INSTALL') ?>
				</button>
				<?php $disabled = ' disabled="disabled" '; ?>
			<?php else: ?>
				<button
					class="btn btn-sm btn-primary"
					type="button"
					onclick="setContentElement('<?php echo $contentElement->contentElementXml; ?>', 'config.installContentElement')">
					<i class="icon-cogs"></i>
					<?php echo JText::_('COM_REDCORE_UPDATE') ?>
				</button>
				<a class="btn btn-sm btn-primary"
				   href="<?php echo JRoute::_('index.php?option=com_redcore&view=translations&contentelement=' . str_replace('#__', '', $contentElement->table)); ?>">
					<i class="icon-globe"></i>
					<?php echo JText::_('COM_REDCORE_TRANSLATIONS') ?>
				</a>
				<button
					class="btn btn-sm btn-danger"
					type="button"
					onclick="setContentElement('<?php echo $contentElement->contentElementXml; ?>', 'config.uninstallContentElement')">
					<i class="icon-cogs"></i>
					<?php echo JText::_('JTOOLBAR_UNINSTALL') ?>
				</button>
				<?php $disabled = ''; ?>
			<?php endif; ?>
			<button
				class="btn btn-sm btn-danger"
				type="button"
				onclick="setContentElement('<?php echo $contentElement->contentElementXml; ?>', 'config.deleteContentElement')">
				<i class="icon-remove"></i>
				<?php echo JText::_('JTOOLBAR_DELETE') ?>
			</button>
			<button
				class="btn btn-sm btn-danger"
				type="button"
				onclick="setContentElement('<?php echo $contentElement->contentElementXml; ?>', 'config.purgeContentElement')" <?php echo $disabled; ?>>
				<i class="icon-trash"></i>
				<?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_PURGE_TRANSLATIONS') ?>
			</button>
		</div>
		<?php if ((++$column) % 3 == 0 ) : ?>
	</div>
	<div class="row">
		<?php endif; ?>
		<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<div class="row">
		<?php if (!empty($missingContentElements)): ?>
		<?php foreach ($missingContentElements as $missingContentElement): ?>
		<div class="col-md-4 well">
			<h4>
				<?php echo $missingContentElement->xml; ?>
			</h4>
			<table class="table table-striped adminlist">
				<tbody>
				<tr>
					<td>
						<strong><?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_TABLE'); ?>:</strong>
					</td>
					<td>
						<strong><?php echo !empty($missingContentElement->table) ? $missingContentElement->table : ''; ?></strong>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_COLUMNS'); ?>:</strong>
					</td>
					<td>
						<strong><?php echo !empty($missingContentElement->columns) ? implode(', ', $missingContentElement->columns) : ''; ?></strong>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php echo JText::_('JSTATUS'); ?>:</strong>
					</td>
					<td>
						<strong><?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_MISSING_XML_FILE'); ?></strong>
					</td>
				</tr>
				</tbody>
			</table>
			<button
				class="btn btn-sm btn-danger"
				type="button"
				onclick="setContentElement('<?php echo $missingContentElement->xml; ?>', 'config.uninstallContentElement')">
				<i class="icon-cogs"></i>
				<?php echo JText::_('JTOOLBAR_UNINSTALL') ?>
			</button>
		</div>
		<?php if ((++$column) % 3 == 0 ) : ?>
	</div>
	<div class="row">
		<?php endif; ?>
		<?php endforeach; ?>
		<?php endif; ?>
		<div class="clearfix"></div>
	</div>
</div>
