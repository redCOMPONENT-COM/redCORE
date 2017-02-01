<?php
/**
 * @package     Redcore.Translation
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$column = 0;
$return = !empty($displayData['return']) ? $displayData['return'] : '';
$components = !empty($displayData['components']) ? $displayData['components'] : array();
$configurationLink = !empty($displayData['configurationLink']) ? true : false;
?>
<div class="row">
	<?php if (empty($components)): ?>
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="pagination-centered">
				<h3><?php echo JText::_('COM_REDCORE_DASHBOARD_NO_EXTENSIONS') ?></h3>
			</div>
		</div>
	<?php else : ?>
		<?php foreach ($components as $component): ?>
			<div class="col-md-4">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4><strong><?php echo !empty($component->xml->name) ? RText::getTranslationIfExists($component->xml->name, '', '') : $component->option; ?></strong></h4>
					</div>
					<div class="panel-body">
						<table class="table table-striped adminlist">
							<tbody>
							<tr>
								<td>
									<strong><?php echo JText::_('JAUTHOR'); ?>:</strong>
								</td>
								<td>
									<strong><?php echo !empty($component->xml->author) ? $component->xml->author : ''; ?></strong>
								</td>
							</tr>
							<tr>
								<td>
									<strong><?php echo JText::_('JVERSION'); ?>:</strong>
								</td>
								<td>
									<strong><span class="label label-success"><?php echo !empty($component->xml->version) ? $component->xml->version : ''; ?></span></strong>
								</td>
							</tr>
							<tr>
								<td>
									<strong><?php echo JText::_('JGLOBAL_DESCRIPTION'); ?>:</strong>
								</td>
								<td>
									<strong><?php echo !empty($component->xml->description) ? RText::getTranslationIfExists($component->xml->description, '', '') : ''; ?></strong>
								</td>
							</tr>
							</tbody>
						</table>
						<?php if ($configurationLink): ?>
							<a
								class="btn btn-primary"
								href="<?php echo JRoute::_('index.php?option=com_redcore&view=config&layout=edit&component=' . $component->option . '&return=' . $return); ?>">
								<i class="icon-cogs"></i>
								<?php echo JText::_('COM_REDCORE_CONFIGURATION') ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php if ((++$column) % 3 == 0 ) : ?>
				</div>
				<div class="row">
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
