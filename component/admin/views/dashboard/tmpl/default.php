<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;
$return = base64_encode('index.php?option=com_redcore&view=dashboard');
$column = 0;
?>
<h2><?php echo JText::_('COM_REDCORE_DASHBOARD'); ?></h2>
<div class="row-fluid">
	<?php if (empty($this->components)): ?>
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="pagination-centered">
				<h3><?php echo JText::_('COM_REDCORE_DASHBOARD_NO_EXTENSIONS') ?></h3>
			</div>
		</div>
	<?php else : ?>
		<?php foreach ($this->components as $component): ?>
			<div class="span4 well">
				<h4>
					<?php echo !empty($component->xml->name) ? RText::getTranslationIfExists($component->xml->name, '', '') : $component->option; ?>
				</h4>
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
							<strong><span class="badge badge-success"><?php echo !empty($component->xml->version) ? $component->xml->version : ''; ?></span></strong>
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
				<a
					class="btn btn-primary"
					href="<?php echo JRoute::_('index.php?option=com_redcore&view=config&layout=edit&component=' . $component->option . '&return=' . $return); ?>">
					<i class="icon-cogs"></i>
					<?php echo JText::_('COM_REDCORE_CONFIGURATION') ?>
				</a>
				<a class="btn btn-primary"
				   href="<?php echo JRoute::_('index.php?option=com_redcore&view=translations&layout=manage&component=' . $component->option . '&return=' . $return); ?>">
					<i class="icon-globe"></i>
					<?php echo JText::_('COM_REDCORE_TRANSLATIONS') ?>
				</a>
			</div>
			<?php if ((++$column) % 3 == 0 ) : ?>
				</div>
				<div class="row-fluid">
			<?php endif; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
