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
<div class="tab-pane" id="mainComponentInfo">
	<p class="tab-description"><?php echo RText::getTranslationIfExists($this->component->xml->description, '', ''); ?></p>

	<div class="span12">
		<div class="span10">
			<h4>
				<?php echo JText::_('JVERSION'); ?> : <span class="badge badge-success"><?php echo $this->component->xml->version; ?></span>
			</h4>
			<?php if (!empty($this->component->xml->requirements)): ?>
				<table class="table table-striped">
					<thead>
						<tr>
							<th style="text-align: left;"><?php echo JText::_('COM_REDCORE_CONFIG_NAME'); ?></th>
							<th style="text-align: left;"><?php echo JText::_('JOPTION_REQUIRED'); ?></th>
							<th style="text-align: left;"><?php echo JText::_('JCURRENT'); ?></th>
						</tr>
					</thead>

					<tbody>
						<?php if (!empty($this->component->xml->requirements->php)) : ?>
							<tr>
								<td>
									<strong><?php echo JText::_('COM_REDCORE_CONFIG_PHP_VERSION'); ?>:</strong>
								</td>
								<td>
									<span class="badge badge-success"><?php echo $this->component->xml->requirements->php; ?></span>
								</td>
								<td>
									<?php $phpVersion = phpversion(); ?>
									<?php if (version_compare($this->component->xml->requirements->php,  $phpVersion, '>=')) : ?>
										<span class="badge badge-important">
									<?php else : ?>
										<span class="badge badge-success">
									<?php endif; ?>

										<?php echo $phpVersion; ?>
									</span>
								</td>
							</tr>
						<?php endif; ?>
						<?php if (!empty($this->component->xml->requirements->mysql)) : ?>
							<tr>
								<td>
									<strong><?php echo JText::_('COM_REDCORE_CONFIG_MYSQL_VERSION'); ?>:</strong>
								</td>
								<td>
									<span class="badge badge-success"><?php echo $this->component->xml->requirements->mysql; ?></span>
								</td>
								<td>
									<?php $dbVersion  = JFactory::getDbo()->getVersion(); ?>
									<?php if (version_compare($this->component->xml->requirements->mysql,  $dbVersion, '>=')) : ?>
										<span class="badge badge-important">
									<?php else : ?>
										<span class="badge badge-success">
									<?php endif; ?>

										<?php echo $dbVersion; ?>
									</span>
								</td>
							</tr>
						<?php endif; ?>

						<?php if (!empty($this->component->xml->requirements->extensions->extension)) : ?>
							<?php foreach ($this->component->xml->requirements->extensions->extension as $extension) : ?>
								<tr>
									<td>
										<strong><?php echo $extension; ?> <?php echo JText::_('COM_REDCORE_CONFIG_EXTENSION_SUPPORTED'); ?>:</strong>
									</td>
									<td>
										<span class="badge badge-success"><?php echo JText::_('JYES'); ?></span>
									</td>
									<td>
										<?php if (!extension_loaded($extension)) : ?>
											<span class="badge badge-important"><?php echo JText::_('JNO'); ?></span>
										<?php else : ?>
											<span class="badge badge-success"><?php echo JText::_('JYES'); ?></span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php if (!empty($this->component->xml->media['folder'])) : ?>
							<tr>
								<td>
									<strong><?php echo $this->component->xml->media['folder']; ?></strong><br/>
								</td>
								<td>
									<span class="badge badge-success"><?php echo JText::_('JGLOBAL_CREATED') . ' (' . JText::_('JYES') . ')'; ?></span> / <span class="badge badge-success"><?php echo JText::_('COM_REDCORE_CONFIG_WRITABLE') . ' (' . JText::_('JYES') . ')'; ?></span>
								</td>
								<td>
									<?php if (@!is_dir(JPATH_SITE . '/' . $this->component->xml->media['folder'])) : ?>
										<span class="badge badge-important"><?php echo JText::_('JGLOBAL_CREATED') . ' (' . JText::_('JNO') . ')'; ?></span>
									<?php else : ?>
										<span class="badge badge-success"><?php echo JText::_('JGLOBAL_CREATED') . ' (' . JText::_('JYES') . ')'; ?></span>
									<?php endif; ?>
									 /
									<?php if (@!is_writeable(JPATH_SITE . '/' . $this->component->xml->media['folder'])) : ?>
										<span class="badge badge-important"><?php echo JText::_('COM_REDCORE_CONFIG_WRITABLE') . ' (' . JText::_('JNO') . ')'; ?></span>
									<?php else : ?>
										<span class="badge badge-success"><?php echo JText::_('COM_REDCORE_CONFIG_WRITABLE') . ' (' . JText::_('JYES') . ')'; ?></span>
									<?php endif; ?>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>

	<div class="span12" style="margin-top:20px;">
		<div class="span5">
			<h4>
				<?php echo RText::getTranslationIfExists($this->component->xml->name, '', ''); ?> <?php echo JText::_('COM_REDCORE_CONFIG_MODULES'); ?>
			</h4>
			<table class="table table-striped adminlist">
				<thead>
				<tr>
					<th><?php echo JText::_('COM_REDCORE_CONFIG_NAME'); ?></th>
					<th><?php echo JText::_('JENABLED'); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if (!empty($this->modules)): ?>
					<?php foreach ($this->modules as $module): ?>
						<tr>
							<td>
								<strong><?php echo $module->name; ?></strong>
							</td>
							<td>
								<?php if ($module->enabled == 1) : ?>
									<span class="badge badge-success"><?php echo JText::_('JENABLED'); ?></span>
								<?php else : ?>
									<span class="badge badge-important"><?php echo JText::_('JDISABLED'); ?></span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="2">
							<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
						</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>
		</div>

		<div class="span5">
			<h4>
				<?php echo RText::getTranslationIfExists($this->component->xml->name, '', ''); ?> <?php echo JText::_('COM_REDCORE_CONFIG_PLUGINS'); ?>
			</h4>
			<table class="table table-striped adminlist">
				<thead>
				<tr>
					<th><?php echo JText::_('COM_REDCORE_CONFIG_NAME'); ?></th>
					<th><?php echo JText::_('JENABLED'); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if (!empty($this->plugins)): ?>
					<?php foreach ($this->plugins as $plugin): ?>
						<tr>
							<td>
								<strong><?php echo $plugin->name; ?></strong>
							</td>
							<td>
								<?php if ($plugin->enabled == 1) : ?>
									<span class="badge badge-success"><?php echo JText::_('JENABLED'); ?></span>
								<?php else : ?>
									<span class="badge badge-important"><?php echo JText::_('JDISABLED'); ?></span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="2">
							<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
						</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
