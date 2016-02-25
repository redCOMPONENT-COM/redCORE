<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;
JText::script('JGLOBAL_SELECT_SOME_OPTIONS');
JText::script('JGLOBAL_SELECT_AN_OPTION');
JText::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$selectedLanguage = $this->state->get('filter.language', '');
$action = JRoute::_('index.php?option=com_redcore&view=translations');

// Company filter will not enable search tools
if (isset($data['activeFilters']['company']))
{
	unset($data['activeFilters']['company']);
}

$searchToolsOptions = array(
	'filtersHidden' => false,
	"searchFieldSelector" => "#filter_search_translations",
	"orderFieldSelector" => "#list_fullordering",
	"searchField" => "search_translations",
	"limitFieldSelector" => "#list_translations_limit",
	"activeOrder" => $listOrder,
	"activeDirection" => $listDirn,
	"formSelector" => ("#adminForm"),
);
?>
<form action="<?php echo $action; ?>" id="adminForm" method="post" name="adminForm" autocomplete="off" class="adminForm form-validate form-horizontal" enctype="multipart/form-data">
	<?php echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view' => $this,
			'options' => $searchToolsOptions
		)
	);
	?>
	<hr/>
	<?php if (empty($this->componentName)) : ?>
		<div class="alert alert-info">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<div class="pagination-centered">
				<h3><?php echo JText::_('COM_REDCORE_TRANSLATIONS_SELECT_COMPONENT') ?></h3>
			</div>
		</div>
	<?php else : ?>
	<ul class="nav nav-tabs" id="mainTabs">
		<li><a href="#mainComponentTranslations" data-toggle="tab"><?php echo JText::_('COM_REDCORE_TRANSLATIONS'); ?></a></li>
	</ul>
	<div class="tab-content">
		<?php echo RLayoutHelper::render(
			'translation.tables',
			array(
				'contentElements' => $this->contentElements,
				'missingContentElements' => $this->missingContentElements,
			)
		); ?>
	</div>
	<div>

		<input type="hidden" name="id" value="<?php echo $this->component->id; ?>" />
		<input type="hidden" name="component" value="<?php echo $this->componentName; ?>">
		<input type="hidden" name="language" value="<?php echo $selectedLanguage; ?>">
		<input type="hidden" name="contentElement" id="contentElement" value="" />
		<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0">
		<input type="hidden" name="layout" value="<?php echo JFactory::getApplication()->input->getString('layout'); ?>">
	</div>
	<?php endif; ?>
	<?php echo JHtml::_('form.token'); ?>
</form>
<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery('#mainTabs a[href="#mainComponentTranslations"]').tab('show');
	});
</script>
