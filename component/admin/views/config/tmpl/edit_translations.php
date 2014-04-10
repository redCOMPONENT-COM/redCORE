<?php
/**
 * @package    Redcore.Backend
 * @subpackage Templates
 *
 * @copyright  Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
?>
<script>
	function setContentElement(contentElement, task)
	{

		document.getElementById('contentElement').value = contentElement;

		if (task != '')
		{
			if (task == 'config.uninstallContentElement')
			{
				if (confirm('<?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_UNINSTALL_CONFIRM', true); ?>'))
					Joomla.submitform(task);
			}
			else if (task == 'config.deleteContentElement')
			{
				if (confirm('<?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_DELETE_CONFIRM', true); ?>'))
					Joomla.submitform(task);
			}
			else if (task == 'config.purgeContentElement')
			{
				if (confirm('<?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_PURGE_CONFIRM', true); ?>'))
					Joomla.submitform(task);
			}
			else
			{
				Joomla.submitform(task);
			}
		}
	}
</script>
<div class="tab-pane" id="mainComponentTranslations">
	<p class="tab-description"><?php echo JText::_('COM_REDCORE_CONFIG_MAIN_COMPONENT_TRANSLATIONS_DESC'); ?></p>
	<div class="row-fluid">
		<div class="span6 well">
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('redcoreContentElement[]'); ?>
				</div>
				<div class="controls">
					<input type="file" name="redcoreContentElement[]" id="redcoreContentElement" accept="application/xml" class="inputbox" />
					<button
						class="btn btn-success"
						type="button"
						onclick="setContentElement('', 'config.uploadContentElement')">
						<i class="icon-upload"></i>
						<?php echo JText::_('JTOOLBAR_UPLOAD') ?>
					</button>
				</div>
			</div>
			<div class="control-group" style="margin-top:40px;margin-bottom: 0;">
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
	<div class="row-fluid">
		<?php if (empty($this->contentElements)): ?>
			<?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_NO_FILES_AVAILABLE'); ?>
		<?php else : ?>
			<?php foreach ($this->contentElements as $contentElement): ?>
				<?php $status = $contentElement->getStatus() ?>
				<div class="span4">
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
							class="btn btn-success"
							type="button"
							onclick="setContentElement('<?php echo $contentElement->contentElementXml; ?>', 'config.installContentElement')">
							<i class="icon-cogs"></i>
							<?php echo JText::_('JTOOLBAR_INSTALL') ?>
						</button>
						<?php $disabled = ' disabled="disabled" '; ?>
					<?php else: ?>
						<button
							class="btn btn-primary"
							type="button"
							onclick="setContentElement('<?php echo $contentElement->contentElementXml; ?>', 'config.installContentElement')">
							<i class="icon-cogs"></i>
							<?php echo JText::_('COM_REDCORE_UPDATE') ?>
						</button>
						<button
							class="btn btn-danger"
							type="button"
							onclick="setContentElement('<?php echo $contentElement->contentElementXml; ?>', 'config.uninstallContentElement')">
							<i class="icon-cogs"></i>
							<?php echo JText::_('JTOOLBAR_UNINSTALL') ?>
						</button>
						<?php $disabled = ''; ?>
					<?php endif; ?>
					<button
						class="btn btn-danger"
						type="button"
						onclick="setContentElement('<?php echo $contentElement->contentElementXml; ?>', 'config.deleteContentElement')">
						<i class="icon-remove"></i>
						<?php echo JText::_('JTOOLBAR_DELETE') ?>
					</button>
					<button
						class="btn btn-danger"
						type="button"
						onclick="setContentElement('<?php echo $contentElement->contentElementXml; ?>', 'config.purgeContentElement')" <?php echo $disabled; ?>>
						<i class="icon-trash"></i>
						<?php echo JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_PURGE_TRANSLATIONS') ?>
					</button>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
		<div class="clearfix"></div>
	</div>
</div>
