<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

$action = JRoute::_('index.php?option=com_redcore&view=webservice');

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
?>
<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery('body').on('click', '.fields-add-new-row', function(e){
			e.preventDefault();
			var $button = jQuery(this);

			var getData = {};
			getData['operation'] = $button.find('[name="addNewRowOperation"]').val();
			getData['fieldList'] = $button.find('[name="addNewRowList"]').val();
			var rowType = $button.find('[name="addNewRowType"]').val();
			var optionType = $button.find('[name="addNewOptionType"]').val();

			if (typeof optionType == 'undefined')
			{
				optionType = rowType;
			}

			if (optionType == 'FieldFromDatabase' || optionType == 'ResourceFromDatabase')
			{
				getData['tableName'] = $button.parents('.form-inline:first').find('[name="jform[main][addFromDatabase]"]').val();
			}
			else if (optionType == 'ConnectWebservice')
			{
				getData['webserviceId'] = $button.parents('.form-inline:first').find('[name="jform[main][connectWebservice]"]').val();
			}

			jQuery.ajax({
				url: 'index.php?option=com_redcore&task=webservice.ajaxGet' + optionType,
				data: getData,
				dataType: 'text',
				beforeSend: function () {
					$button.parents('fieldset:first').addClass('opacity-40');
				}
			}).done(function (data) {
				$button.parents('fieldset:first').removeClass('opacity-40')
					.find('.ws-row-list').prepend(data)
					.find('.fields-edit-row:first').click();
				jQuery('select').chosen();
				jQuery('.hasTooltip').tooltip();
				rRadioGroupButtonsSet('.ws-' + rowType + '-' + getData['operation']);
				rRadioGroupButtonsEvent('.ws-' + rowType + '-' + getData['operation']);
			});
		}).on('click', '.fields-remove-row', function(e){
			e.preventDefault();
			jQuery(this).parents('.row-stripped').remove();
		}).on('click', '.ws-data-mode-switch input', function(e){
			var $radio = jQuery(this);
			var dataMode = $radio.val();
			var $currentTab = $radio.parents('.ws-params');
			$currentTab.find('.ws-dataMode').hide();
			$currentTab.find('.ws-dataMode-' + dataMode).show();
		}).on('click', '.ws-validate-data-switch input', function(e){
			var $radio = jQuery(this);
			var dataMode = $radio.val();
			var $currentTab = $radio.parents('.ws-params');
			$currentTab.find('.ws-validateData').hide();
			$currentTab.find('.ws-validateData-' + dataMode).show();
		}).on('click', '.ws-documentationSource-switch input', function(e){
			var $radio = jQuery(this);
			var dataMode = $radio.val();
			var $currentTab = $radio.parents('.ws-params');
			$currentTab.find('.ws-documentationSource').hide();
			$currentTab.find('.ws-documentationSource-' + dataMode).show();
		}).on('click', '.ws-use-forward-switch input', function(e){
			var $radio = jQuery(this);
			if ($radio.val() != '')
			{
				$radio.parents('.ws-params').find('.ws-use-operation-fieldset').hide();
			}
			else
			{
				$radio.parents('.ws-params').find('.ws-use-operation-fieldset').show();
			}
		}).on('click', '.fields-add-new-task', function(e){
			e.preventDefault();
			var $button = jQuery(this);

			var getData = {};
			getData['taskName'] = $button.parents('.fields-add-new-task-row').find('[name="newTask"]').val().replace(/[^\w]/g,'');

			if (getData['taskName'] != '')
			{
				jQuery.ajax({
					url: 'index.php?option=com_redcore&task=webservice.ajaxGetTask',
					data: getData,
					dataType: 'text',
					beforeSend: function () {
						$button.parents('#webserviceTabTask').addClass('opacity-40');
					}
				}).done(function (data) {
					$button.parents('#webserviceTabTask').removeClass('opacity-40')
						.find('.tab-content:first').prepend(data);

					jQuery('#taskTabs').prepend('<li><a href="#operationTabtask-'
					+ getData['taskName'] + '" id="operation-task-'
					+ getData['taskName'] + '-tab" data-toggle="tab">task-'
					+ getData['taskName'] + '</a></li>').find('li a:first').click();
					jQuery('select').chosen();
					jQuery('.hasTooltip').tooltip();
					rRadioGroupButtonsSet('#operationTabtask-' + getData['taskName']);
					rRadioGroupButtonsEvent('#operationTabtask-' + getData['taskName']);
					jQuery('#operationTabtask-' + getData['taskName'] + ' :input[checked="checked"]').click();
				});
			}
			else
			{
				alert('<?php echo JText::_('COM_REDCORE_WEBSERVICE_TASK_ADD_NEW_TASK_ERROR', true, true) ?>');
			}

		}).on('click', '.fields-edit-row', function(e){
			var $parent = jQuery(this).parents('.row-stripped');
			$parent.find('.ws-row-edit').show();
			$parent.find('.ws-row-display').hide();
		}).on('click', '.ws-isEnabled-trigger input', function(e){
			var $parent = jQuery(this).parents('.ws-params');
			$parent.find('.ws-operation-configuration')
				.prop('disabled', jQuery(this).val() == 0)
				.find('.chzn-done').prop('disabled', jQuery(this).val() == 0).trigger("liszt:updated");
		}).on('click', '.fields-apply-row', function(e){
			var $parent = jQuery(this).parents('.row-stripped');
			var rowValues = {};

			$parent.find('.ws-row-edit :input').each(function(){
				var $input = jQuery(this);
				var name = $input.attr('name');

				if ((!$input.is(':radio') || $input.prop('checked')) && typeof name !== typeof undefined && name !== false)
				{
					if ($input.is(':radio')){
						name = name.split('_');
						name = name[1];
					}

					$parent.find('.ws-row-display-cell-' + name).html($input.val()).parent().show();
					rowValues[name] = $input.val();
				}
			});

			$parent.find('.ws-row-original').val(JSON.stringify(rowValues));
			$parent.find('.ws-row-edit').hide();
			$parent.find('.ws-row-display').show();
		});

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
