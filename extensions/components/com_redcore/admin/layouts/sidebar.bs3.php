<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;
$app = JFactory::getApplication();
$option = $app->input->getString('component', '');
$view = RInflector::pluralize($app->input->getString('view', ''));
$return = $app->input->getString('return', '');
$translationTableName = $app->input->getString('translationTableName', '');
$components = RedcoreHelpersView::getExtensionsRedcore(true);
$translationTables = RTranslationTable::getInstalledTranslationTables(false, true);
?>
<div class="panel-group" id="rc-sidebar-accordion">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=dashboard') ?>">
					<i class="icon-dashboard"></i>
					<?php echo JText::_('COM_REDCORE_DASHBOARD') ?></a>
			</h4>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#rc-sidebar-accordion" href="#rc-sidebar-accordion-configuration">
					<i class="icon-cogs"></i>
					<?php echo JText::_('COM_REDCORE_CONFIGURATION') ?></a>
			</h4>
		</div>
		<div id="rc-sidebar-accordion-configuration" class="panel-collapse collapse<?php echo $view === 'configs' ? ' in' : '';?>">
			<ul class="list-group">
				<?php foreach ($components as $component) : ?>
					<li class="list-group-item <?php echo $option == $component->option ? 'list-group-item-info' : ''; ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=config&layout=edit&component=' . $component->option . '&return=' . $return) ?>">
							<i class="icon-cogs"></i>
							<?php echo JText::_($component->xml->name); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a
					data-toggle="collapse"
					data-parent="#rc-sidebar-accordion"
					href="#rc-sidebar-accordion-translations">
					<i class="icon-cogs"></i>
					<?php echo JText::_('COM_REDCORE_TRANSLATIONS') ?></a>
			</h4>
		</div>
		<div id="rc-sidebar-accordion-translations" class="panel-collapse collapse<?php echo $view === 'translations' ? ' in' : '';?>">
			<ul class="list-group">
				<li class="list-group-item">
					<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=translation_tables') ?>">
						<i class="icon-globe"></i>
						<?php echo JText::_('COM_REDCORE_TRANSLATIONS_MANAGE_CONTENT_ELEMENTS') ?>
					</a>
				</li>
				<?php foreach ($translationTables as $translationTable) : ?>
					<li class="list-group-item <?php echo $translationTableName == str_replace('#__', '', $translationTable->table) ? 'list-group-item-info' : ''; ?>">
						<a href="<?php echo JRoute::_(
							'index.php?option=com_redcore&view=translations&filter[translationTableName]='
							. str_replace('#__', '', $translationTable->table)
							. '&filter[language]=' . $app->getUserStateFromRequest('com_redcore.translations.translations.filter.language', 'language', '', 'string')
							. '&return=' . $return
						); ?>">
							<i class="icon-globe"></i>
							<?php echo $translationTable->title; ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=webservices') ?>">
					<i class="icon-globe"></i>
					<?php echo JText::_('COM_REDCORE_WEBSERVICES') ?>
				</a>
			</h4>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=oauth_clients') ?>">
					<i class="icon-globe"></i>
					<?php echo JText::_('COM_REDCORE_OAUTH_CLIENTS') ?>
				</a>
			</h4>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a
					data-toggle="collapse"
					data-parent="#rc-sidebar-accordion"
					href="#rc-sidebar-accordion-payments">
					<i class="icon-money"></i>
					<?php echo JText::_('COM_REDCORE_PAYMENTS') ?></a>
			</h4>
		</div>
		<div id="rc-sidebar-accordion-payments"
		     class="panel-collapse collapse<?php echo in_array($view, array('payments', 'payment_configurations', 'payment_dashboards', 'payment_logs')) ?
			     ' in' : '';?>">
			<ul class="list-group">
				<li class="list-group-item">
					<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=payment_dashboard') ?>">
						<i class="icon-dashboard"></i>
						<?php echo JText::_('COM_REDCORE_PAYMENT_DASHBOARD') ?>
					</a>
				</li>
				<li class="list-group-item">
					<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=payment_configurations') ?>">
						<i class="icon-cogs"></i>
						<?php echo JText::_('COM_REDCORE_PAYMENT_CONFIGURATION_LIST_TITLE') ?>
					</a>
				</li>
				<li class="list-group-item">
					<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=payments') ?>">
						<i class="icon-money"></i>
						<?php echo JText::_('COM_REDCORE_PAYMENTS') ?>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a href="http://redcomponent-com.github.io/redCORE/" target="_blank">
					<i class="icon-book"></i>
					<?php echo JText::_('COM_REDCORE_DOCUMENTATION_LINK_TITLE') ?>
				</a>
			</h4>
		</div>
	</div>
</div>
