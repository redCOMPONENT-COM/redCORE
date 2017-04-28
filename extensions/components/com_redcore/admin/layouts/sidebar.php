<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$option               = JFactory::getApplication()->input->getString('component', '');
$view                 = RInflector::pluralize(JFactory::getApplication()->input->getString('view', ''));
$return               = JFactory::getApplication()->input->getString('return', '');
$translationTableName = JFactory::getApplication()->input->getString('translationTableName', '');
$components           = RedcoreHelpersView::getExtensionsRedcore(true);
$translationTables    = RTranslationTable::getInstalledTranslationTables();
$app                  = JFactory::getApplication();
?>
<div class="accordion" id="sidebarAccordion">
	<div class="accordion-group">
		<div class="accordion-heading">
			<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=dashboard') ?>" class="accordion-toggle text-error" data-parent="sidebarAccordion">
				<?php if ($view === 'dashboards'): ?>
						<i class="icon-dashboard"></i><?php echo JText::_('COM_REDCORE_DASHBOARD') ?>
				<?php else: ?>
					<h5>
						<i class="icon-dashboard"></i><?php echo JText::_('COM_REDCORE_DASHBOARD') ?>
					</h5>
				<?php endif; ?>
			</a>
		</div>
	</div>
	<div class="accordion-group">
		<div class="accordion-heading">
			<h5 class="accordion-toggle" href="#collapseConfig" data-toggle="collapse" data-parent="sidebarAccordion">
				<a class="text-error"><i class="icon-dashboard"></i><?php echo JText::_('COM_REDCORE_CONFIGURATION') ?></a>
			</h5>
		</div>
		<div class="accordion-body collapse <?php echo ($view === 'configs') ? 'in' : '' ?>" id="collapseConfig">
			<ul class="nav nav-list">
				<?php foreach ($components as $component) : ?>
					<li class="<?php echo $option == $component->option ? 'active' : ''; ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=config&layout=edit&component=' . $component->option . '&return=' . $return) ?>">
							<i class="icon-cogs"></i>
							<?php echo JText::_($component->xml->name); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<div class="accordion-group">
		<div class="accordion-heading">
			<h5 class="accordion-toggle" href="#collapseTranslations" data-toggle="collapse" data-parent="sidebarAccordion">
				<a class="text-error"><i class="icon-dashboard"></i><?php echo JText::_('COM_REDCORE_TRANSLATIONS') ?></a>
			</h5>
		</div>
		<div class="accordion-body collapse <?php echo ($view === 'translations') ? 'in' : '' ?>" id="collapseTranslations">
			<div class="">
				<ul class="nav nav-list">
					<li>
						<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=translation_tables') ?>">
							<i class="icon-globe"></i><?php echo JText::_('COM_REDCORE_TRANSLATIONS_MANAGE_CONTENT_ELEMENTS') ?>
						</a>
					</li>
					<?php foreach ($translationTables as $translationTable) : ?>
						<li class="<?php echo $translationTableName == str_replace('#__', '', $translationTable->table) ? 'active' : ''; ?>">
							<a href="<?php echo JRoute::_(
								'index.php?option=com_redcore&view=translations&component=' . $translationTable->option . '&translationTableName='
								. str_replace('#__', '', $translationTable->table)
								. '&filter[language]=' . $app->getUserStateFromRequest('com_redcore.translations.translations.filter.language', 'language', '', 'string')
								. '&return=' . $return
							); ?>">
								<i class="icon-globe"></i><?php echo $translationTable->title; ?>
							</a>
						</li>
					<?php endforeach; ?>
			</ul>
			</div>
		</div>
	</div>
	<div class="accordion-group">
		<div class="accordion-heading">
			<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=webservices') ?>" class="accordion-toggle text-error">
				<h5>
					<i class="icon-dashboard"></i><?php echo JText::_('COM_REDCORE_WEBSERVICES') ?>
				</h5>
			</a>
		</div>
	</div>
	<div class="accordion-group">
		<div class="accordion-heading">
			<a href="<?php echo JRoute::_('index.php?option=com_redcore&view=oauth_clients') ?>" class="accordion-toggle text-error">
				<h5>
					<i class="icon-dashboard"></i><?php echo JText::_('COM_REDCORE_OAUTH_CLIENTS') ?>
				</h5>
			</a>
		</div>
	</div>
	<div class="accordion-group">
		<div class="accordion-heading">
			<h5 href="#collapsePayments" data-toggle="collapse" class="accordion-toggle"  data-parent="sidebarAccordion">
				<a class="text-error"><i class="icon-money"></i><?php echo JText::_('COM_REDCORE_PAYMENTS') ?></a>
			</h5>
		</div>
		<div class="accordion-body collapse <?php echo in_array($view, array('payments', 'payment_configurations', 'payment_dashboards', 'payment_logs')) ?
			' in' : '';?>" id="collapsePayments">
			<ul class="nav nav-list">
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
	<div class="accordion-group">
		<div class="accordion-heading">
			<a href="http://redcomponent-com.github.io/redCORE/" target="_blank" class="accordion-toggle text-error">
				<h5>
					<i class="icon-book"></i><?php echo JText::_('COM_REDCORE_DOCUMENTATION_LINK_TITLE'); ?>
				</h5>
			</a>
		</div>
	</div>
</div>
