<?php
/**
 * @package    Redcore.Backend
 * @subpackage Templates
 *
 * @copyright  Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$template = $app->getTemplate();
$tab = $app->input->getString('tab');

if (empty($tab))
{
	$tab = 'mainComponentConfiguration';
}

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
?>
<form action="<?php echo JRoute::_('index.php?option=com_redcore'); ?>"
      id="adminForm" method="post" name="adminForm" autocomplete="off" class="form-validate form-horizontal" enctype="multipart/form-data">
	<ul class="nav nav-tabs" id="mainTabs">
		<li><a href="#mainComponentConfiguration" data-toggle="tab"><?php echo JText::_('COM_REDCORE_CONFIG_MAIN_COMPONENT_CONFIGURATION'); ?></a></li>
		<li><a href="#mainComponentTranslations" data-toggle="tab"><?php echo JText::_('COM_REDCORE_TRANSLATIONS'); ?></a></li>
		<li><a href="#mainComponentInfo" data-toggle="tab"><?php echo JText::_('COM_REDCORE_CONFIG_MAIN_COMPONENT_INFO'); ?></a></li>
	</ul>
	<div class="tab-content row-fluid">
		<?php echo $this->loadTemplate('configuration'); ?>
		<?php echo $this->loadTemplate('translations'); ?>
		<?php echo $this->loadTemplate('info'); ?>
	</div>
	<div>
		<input type="hidden" name="id" value="<?php echo $this->component->id; ?>" />
		<input type="hidden" name="component" value="<?php echo $this->component->option; ?>" />
		<input type="hidden" name="element" value="<?php echo $this->component->option; ?>" />
		<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
		<input type="hidden" name="contentElement" id="contentElement" value="" />
		<input type="hidden" name="tab" id="currentTab" value="<?php echo $tab; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery('#mainTabs a').on('click', function () {
			jQuery('#currentTab').val(jQuery(this).attr('href').substr(1))	;
		});

		jQuery('#mainTabs a[href="#<?php echo $tab ?>"]').tab('show');
	});
</script>
