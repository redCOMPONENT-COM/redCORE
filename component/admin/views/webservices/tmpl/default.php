<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$action = JRoute::_('index.php?option=com_redcore&view=webservices');

$searchToolsOptions = array(
	'filtersHidden' => true,
	"searchFieldSelector" => "#filter_search_webservices",
	"orderFieldSelector" => "#list_fullordering",
	"searchField" => "search_webservices",
	"limitFieldSelector" => "#list_webservices_limit",
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
	<ul class="nav nav-tabs" id="mainTabs">
		<li><a href="#mainComponentWebservices" data-toggle="tab"><?php echo JText::_('COM_REDCORE_WEBSERVICES'); ?></a></li>
	</ul>
	<div class="tab-content row-fluid">
		<?php echo RLayoutHelper::render(
			'webservice.webservices',
			array(
				'webservices' => $this->items,
				'missingWebservices' => $this->missingWebservices,
			)
		); ?>
	</div>
	<div>
		<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="webservice" id="webservice" value="" />
		<input type="hidden" name="version" id="version" value="" />
		<input type="hidden" name="folder" id="folder" value="" />
		<input type="hidden" name="boxchecked" value="0">
	</div>
	<?php echo JHtml::_('form.token'); ?>
</form>
<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery('#mainTabs a[href="#mainComponentWebservices"]').tab('show');
	});
</script>
