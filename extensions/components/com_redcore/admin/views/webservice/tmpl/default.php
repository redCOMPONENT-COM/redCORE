<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

$action = JRoute::_('index.php?option=com_redcore&view=webservice');

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
RHelperAsset::load('redcore.min.js', 'redcore');

?>
<script type="text/javascript">
	jQuery(document).ready(function () {
		redCORE.ws.init();
		jQuery(':input[checked="checked"]').click();
	});
	Joomla.submitbutton = function(task)
	{
		jQuery('.fields-apply-row').click();
		jQuery('.ws-row-edit').remove();

		Joomla.submitform(task, document.getElementById('adminForm'));
	}
</script>
<form action="<?php echo $action; ?>" method="post" name="adminForm" id="adminForm"
      class="form-validate form-horizontal" role="form">
	<div role="tabpanel">
		<ul class="nav nav-tabs" id="mainTabs" role="tablist">
			<li role="presentation" class="active">
				<a href="#webserviceTabGeneral" id="general-tab" role="tab" data-toggle="tab">
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_TAB_GENERAL'); ?>
				</a>
			</li>
			<li role="presentation">
				<a href="#webserviceTabCreate" id="create-tab" role="tab" data-toggle="tab">
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_CREATE_LABEL'); ?>
				</a>
			</li>
			<li role="presentation">
				<a href="#webserviceTabRead" id="read-tab" role="tab" data-toggle="tab">
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_READ_LABEL'); ?>
				</a>
			</li>
			<li role="presentation">
				<a href="#webserviceTabUpdate" id="update-tab" role="tab" data-toggle="tab">
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_UPDATE_LABEL'); ?>
				</a>
			</li>
			<li role="presentation">
				<a href="#webserviceTabDelete" id="delete-tab" role="tab" data-toggle="tab">
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_DELETE_LABEL'); ?>
				</a>
			</li>
			<li role="presentation">
				<a href="#webserviceTabTask" id="task-tab" role="tab" data-toggle="tab">
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_TAB_TASK'); ?>
				</a>
			</li>
			<li role="presentation">
				<a href="#webserviceTabDocumentation" id="documentation-tab" role="tab" data-toggle="tab">
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_DOCUMENTATION_LABEL'); ?>
				</a>
			</li>
			<li role="presentation">
				<a href="#webserviceTabComplexTypes" id="complex-type-tab" role="tab" data-toggle="tab">
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_COMPLEX_TYPES_LABEL'); ?>
				</a>
			</li>
		</ul>
	</div>

	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active in" id="webserviceTabGeneral">
			<?php echo $this->loadTemplate('main'); ?>
		</div>

		<div role="tabpanel" class="tab-pane" id="webserviceTabCreate">
			<?php echo $this->loadTemplate('create'); ?>
		</div>

		<div role="tabpanel" class="tab-pane" id="webserviceTabRead">
			<?php echo $this->loadTemplate('read'); ?>
		</div>

		<div role="tabpanel" class="tab-pane" id="webserviceTabUpdate">
			<?php echo $this->loadTemplate('update'); ?>
		</div>

		<div role="tabpanel" class="tab-pane" id="webserviceTabDelete">
			<?php echo $this->loadTemplate('delete'); ?>
		</div>

		<div role="tabpanel" class="tab-pane" id="webserviceTabTask">
			<?php echo $this->loadTemplate('task'); ?>
		</div>

		<div role="tabpanel" class="tab-pane" id="webserviceTabDocumentation">
			<?php echo $this->loadTemplate('documentation'); ?>
		</div>

		<div role="tabpanel" class="tab-pane" id="webserviceTabComplexTypes">
			<?php echo $this->loadTemplate('complex_types'); ?>
		</div>
	</div>

	<!-- hidden fields -->
	<?php echo $this->form->getInput('id', 'main'); ?>
	<input type="hidden" name="option" value="com_redcore">
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
	<input type="hidden" name="task" value="">
	<?php echo JHTML::_('form.token'); ?>
</form>
