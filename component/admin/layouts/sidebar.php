<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
$option = JFactory::getApplication()->input->getString('component', '');
$view = RInflector::pluralize(JFactory::getApplication()->input->getString('view', ''));
$return = JFactory::getApplication()->input->getString('return', '');
$contentElement = JFactory::getApplication()->input->getString('contentelement', '');
$components = RedcoreHelpersView::getExtensionsRedcore();
$translationTables = RTranslationHelper::getInstalledTranslationTables();

if (empty($return))
{
	$return = base64_encode('index.php?option=com_redcore&view=dashboard');
}
?>
<ul class="nav nav-list">
	<?php if ($view === 'dashboards'): ?>
		<li class="nav-header"><?php echo JText::_('COM_REDCORE_DASHBOARD') ?></li>
	<?php else: ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=dashboard') ?>">
				<i class="icon-dashboard"></i>
				<?php echo JText::_('COM_REDCORE_DASHBOARD') ?>
			</a>
		</li>
	<?php endif; ?>

	<?php if ($view === 'configs'): ?>
		<li class="nav-header"><?php echo JText::_('COM_REDCORE_CONFIGURATION') ?></li>
		<?php foreach ($components as $component) : ?>
			<li class="<?php echo $option == $component->option ? 'active' : ''; ?>">
				<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=config&layout=edit&component=' . $component->option . '&return=' . $return) ?>">
					<i class="icon-cogs"></i>
					<?php echo JText::_($component->xml->name); ?>
				</a>
			</li>
		<?php endforeach; ?>
		<li class="divider"></li>
	<?php else: ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=configs') ?>">
				<i class="icon-cogs"></i>
				<?php echo JText::_('COM_REDCORE_CONFIGURATION'); ?>
			</a>
		</li>
	<?php endif; ?>

	<?php if ($view === 'translations'): ?>
		<li class="nav-header"><?php echo JText::_('COM_REDCORE_TRANSLATIONS') ?></li>
		<?php foreach ($translationTables as $translationTable) : ?>

			<li class="<?php echo $contentElement == str_replace('#__', '', $translationTable->table) ? 'active' : ''; ?>">
				<a href="<?php echo JRoute::_(
					'index.php?option=com_redcore&view=translations&component=' . $translationTable->option . '&contentelement='
					. str_replace('#__', '', $translationTable->table)
					. '&return=' . $return
				); ?>">
					<i class="icon-globe"></i>
					<?php echo $translationTable->name; ?>
				</a>
			</li>
		<?php endforeach; ?>
		<li class="divider"></li>
	<?php else: ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=translations&contentelement=&layout=manage&return=' . $return) ?>">
				<i class="icon-globe"></i>
				<?php echo JText::_('COM_REDCORE_TRANSLATIONS') ?>
			</a>
		</li>
	<?php endif; ?>

	<?php if ($view === 'webservices'): ?>
		<li class="nav-header"><?php echo JText::_('COM_REDCORE_WEBSERVICES') ?></li>
		<li class="divider"></li>
	<?php else: ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=webservices') ?>">
				<i class="icon-globe"></i>
				<?php echo JText::_('COM_REDCORE_WEBSERVICES') ?>
			</a>
		</li>
	<?php endif; ?>

	<?php if ($view === 'oauth_clients'): ?>
		<li class="nav-header"><?php echo JText::_('COM_REDCORE_OAUTH_CLIENTS') ?></li>
		<li class="divider"></li>
	<?php else: ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=oauth_clients') ?>">
				<i class="icon-globe"></i>
				<?php echo JText::_('COM_REDCORE_OAUTH_CLIENTS') ?>
			</a>
		</li>
	<?php endif; ?>
</ul>
