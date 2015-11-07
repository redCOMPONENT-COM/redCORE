<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$data = $displayData;
$xml = $data['xml'];
$requirements = $data['requirements'];
$modules = $data['modules'];
$plugins = $data['plugins'];
$message = $data['message'];

JHtml::_('rbootstrap.tooltip');
?>
<style>
	#j-main-container .adminform{
		width: 100%;
	}
</style>
<div class="row">
	<div class="col-md-12">
		<h3>
			<strong><?php echo JText::_('COM_REDCORE_CONFIG_NAME'); ?></strong>: <?php echo RText::getTranslationIfExists($xml->name, '', ''); ?>
		</h3>
		<h4>
			<?php echo JText::_('JVERSION'); ?> : <span class="label label-success"><?php echo $xml->version; ?></span>
		</h4>
		<p class="tab-description">
			<strong><?php echo JText::_('JGLOBAL_DESCRIPTION'); ?></strong>: <?php echo RText::getTranslationIfExists($xml->description, '', ''); ?>
		</p>
		<?php if (!empty($message)): ?>
			<div>
				<?php echo $message; ?>
			</div>
		<?php endif; ?>
		<table class="table table-striped">
			<thead>
			<tr>
				<th><?php echo JText::_('COM_REDCORE_CONFIG_NAME'); ?></th>
				<th><?php echo JText::_('JOPTION_REQUIRED'); ?></th>
				<th><?php echo JText::_('JCURRENT'); ?></th>
			</tr>
			</thead>

			<tbody>
			<?php foreach ($requirements['applications'] as $requirement): ?>
				<tr>
					<td>
						<strong><?php echo $requirement['name']; ?>:</strong>
					</td>
					<td>
						<span class="label label-success"><?php echo $requirement['required']; ?></span>
					</td>
					<td>
						<?php if ($requirement['status']) : ?>
						<span class="label label-success">
							<?php else : ?>
							<span class="label label-danger">
							<?php endif; ?>

							<?php echo $requirement['current']; ?>
							</span>
					</td>
				</tr>
			<?php endforeach; ?>
			<?php if (!empty($requirements['extensions'])) : ?>
				<?php foreach ($requirements['extensions'] as $extension): ?>
					<tr>
						<td>
							<strong><?php echo $extension['name']; ?> <?php echo JText::_('COM_REDCORE_CONFIG_EXTENSION_SUPPORTED'); ?>:</strong>
						</td>
						<td>
							<span class="label label-success"><?php echo JText::_('JYES'); ?></span>
						</td>
						<td>
							<?php if ($extension['status']) : ?>
								<span class="label label-success"><?php echo JText::_('JYES'); ?></span>
							<?php else : ?>
								<span class="label label-danger"><?php echo JText::_('JNO'); ?></span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php if (!empty($xml->media['folder'])) : ?>
				<tr>
					<td>
						<strong><?php echo $xml->media['folder']; ?></strong><br/>
					</td>
					<td>
						<span class="label label-success">
							<?php echo JText::_('JGLOBAL_CREATED') . ' (' . JText::_('JYES') . ')'; ?>
						</span> / <span class="label label-success">
							<?php echo JText::_('COM_REDCORE_CONFIG_WRITABLE') . ' (' . JText::_('JYES') . ')'; ?>
						</span>
					</td>
					<td>
						<?php if (@!is_dir(JPATH_SITE . '/' . $xml->media['folder'])) : ?>
							<span class="label label-danger"><?php echo JText::_('JGLOBAL_CREATED') . ' (' . JText::_('JNO') . ')'; ?></span>
						<?php else : ?>
							<span class="label label-success"><?php echo JText::_('JGLOBAL_CREATED') . ' (' . JText::_('JYES') . ')'; ?></span>
						<?php endif; ?>
						/
						<?php if (@!is_writeable(JPATH_SITE . '/' . $xml->media['folder'])) : ?>
							<span class="label label-danger"><?php echo JText::_('COM_REDCORE_CONFIG_WRITABLE') . ' (' . JText::_('JNO') . ')'; ?></span>
						<?php else : ?>
							<span class="label label-success"><?php echo JText::_('COM_REDCORE_CONFIG_WRITABLE') . ' (' . JText::_('JYES') . ')'; ?></span>
						<?php endif; ?>
					</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<div class="row" style="margin-top:20px;">
	<div class="col-md-6">
		<h4>
			<?php echo RText::getTranslationIfExists($xml->name, '', ''); ?> <?php echo JText::_('COM_REDCORE_CONFIG_MODULES'); ?>
		</h4>
		<table class="table table-striped adminlist">
			<thead>
				<tr>
					<th><?php echo JText::_('COM_REDCORE_CONFIG_NAME'); ?></th>
					<th><?php echo JText::_('COM_REDCORE_INSTALL_WITH_COMPONENT'); ?></th>
					<th><?php echo JText::_('JENABLED'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if (!empty($modules)): ?>
				<?php foreach ($modules as $module):
					$fromExtension = false;
					if (!empty($xml->modules)):
						foreach ($xml->modules->module as $xmlModule):
							if ($module->element == (string) $xmlModule['name']):
								$fromExtension = true;
								break;
							endif;
						endforeach;
					endif;
					?>
					<tr>
						<td>
							<strong><?php echo JText::_($module->name); ?></strong>
						</td>
						<td>
							<?php if ($fromExtension) : ?>
							<span class="label label-success"><?php echo JText::_('JYES'); ?>
								<?php else : ?>
								<span class="label label-warning"><?php echo JText::_('JNO'); ?>
									<?php endif; ?>
							</span>
						</td>
						<td>
							<?php if ($module->enabled == 1) : ?>
								<span class="label label-success"><?php echo JText::_('JENABLED'); ?></span>
							<?php else : ?>
								<span class="label label-danger"><?php echo JText::_('JDISABLED'); ?></span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="3">
						<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>

	<div class="col-md-6">
		<h4>
			<?php echo RText::getTranslationIfExists($xml->name, '', ''); ?> <?php echo JText::_('COM_REDCORE_CONFIG_PLUGINS'); ?>
		</h4>
		<table class="table table-striped adminlist">
			<thead>
				<tr>
					<th><?php echo JText::_('COM_REDCORE_CONFIG_NAME'); ?></th>
					<th><?php echo JText::_('COM_REDCORE_INSTALL_WITH_COMPONENT'); ?></th>
					<th><?php echo JText::_('JENABLED'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if (!empty($plugins)): ?>
				<?php foreach ($plugins as $plugin):
					$fromExtension = false;
					foreach ($xml->plugins->plugin as $xmlPlugin):
						if ($plugin->element == (string) $xmlPlugin['name']):
							$fromExtension = true;
							break;
						endif;
					endforeach;
					?>
					<tr>
						<td>
							<strong><?php echo JText::_($plugin->name); ?></strong>
						</td>
						<td>
							<?php if ($fromExtension) : ?>
							<span class="label label-success"><?php echo JText::_('JYES'); ?>
								<?php else : ?>
								<span class="label label-warning"><?php echo JText::_('JNO'); ?>
									<?php endif; ?>
							</span>
						</td>
						<td>
							<?php if ($plugin->enabled == 1) : ?>
								<span class="label label-success"><?php echo JText::_('JENABLED'); ?></span>
							<?php else : ?>
								<span class="label label-danger"><?php echo JText::_('JDISABLED'); ?></span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="3">
						<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</div>
</div>
<br />
<br />
