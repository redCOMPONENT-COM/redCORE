<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
$option = JFactory::getApplication()->input->getString('component', '');
$view = RInflector::pluralize(JFactory::getApplication()->input->getString('view', ''));
$return = JFactory::getApplication()->input->getString('return', '');
$translationTableName = JFactory::getApplication()->input->getString('translationTableName', '');
$components = RedcoreHelpersView::getExtensionsRedcore();
$translationTables = RTranslationTable::getInstalledTranslationTables();
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

			<li class="<?php echo $translationTableName == str_replace('#__', '', $translationTable->table) ? 'active' : ''; ?>">
				<a href="<?php echo JRoute::_(
					'index.php?option=com_redcore&view=translations&component=' . $translationTable->option . '&translationTableName='
					. str_replace('#__', '', $translationTable->table)
					. '&filter[language]=' . $app->getUserStateFromRequest('com_redcore.translations.translations.filter.language', 'language', '', 'string')
					. '&return=' . $return
				); ?>">
					<i class="icon-globe"></i>
					<?php echo $translationTable->title; ?>
				</a>
			</li>
		<?php endforeach; ?>
		<li class="divider"></li>
	<?php else: ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=translation_tables') ?>">
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

	<?php if ($view === 'payments'): ?>
		<li class="nav-header"><?php echo JText::_('COM_REDCORE_PAYMENTS') ?></li>
		<li class="divider"></li>
	<?php else: ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=payment_configurations') ?>">
				<i class="icon-money"></i>
				<?php echo JText::_('COM_REDCORE_PAYMENTS') ?>
			</a>
		</li>
	<?php endif; ?>
	<li>
		<a href="http://redcomponent-com.github.io/redCORE/" target="_blank">
			<i class="icon-book"></i>
			<?php echo JText::_('COM_REDCORE_DOCUMENTATION_LINK_TITLE') ?>
		</a>
	</li>
</ul>
