<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

$action = JRoute::_('index.php?option=com_redcore&view=translation_table');

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
$this->form->setValue('tableName', '', str_replace('#__', '', $this->item->name));
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if ((task == 'translation_table.apply' || task == 'translation_table.save') && parseInt('<?php echo $this->item->id; ?>') > 0 )
		{
			if (confirm('<?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_SAVE_TABLE_CHANGES', true); ?>'))
				Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>
<form action="<?php echo $action; ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<div class="container-fluid">
		<div class="row">
			<div id="main-params" class="col-md-12">
				<div class="form-group">
					<div class="col-md-2 col-sm-3">
						<?php echo $this->form->getLabel('name') ?>
					</div>
					<div class="col-md-10 col-sm-9">
						<?php echo $this->form->getInput('name') ?><?php echo $this->form->getInput('tableName'); ?>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-2 col-sm-3">
						<?php echo $this->form->getLabel('extension_name') ?>
					</div>
					<div class="col-md-10 col-sm-9">
						<?php echo $this->form->getInput('extension_name') ?><?php echo $this->form->getInput('optionName'); ?>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-2 col-sm-3">
						<?php echo $this->form->getLabel('title') ?>
					</div>
					<div class="col-md-10 col-sm-9">
						<?php echo $this->form->getInput('title') ?>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-2 col-sm-3">
						<?php echo $this->form->getLabel('filter_query') ?>
					</div>
					<div class="col-md-10 col-sm-9">
						<?php echo $this->form->getInput('filter_query') ?>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-2 col-sm-3">
						<?php echo $this->form->getLabel('xml_path') ?>
					</div>
					<div class="col-md-10 col-sm-9">
						<?php echo RTranslationContentElement::getPathWithoutBase($this->item->xml_path); ?>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-2 col-sm-3">
						<?php echo $this->form->getLabel('state') ?>
					</div>
					<div class="col-md-10 col-sm-9">
						<?php echo $this->form->getInput('state') ?>
					</div>
				</div>
			</div>
		</div>
		<br/>
		<ul class="nav nav-tabs" id="mainTabs">
			<li role="presentation" class="active">
				<a href="#mainComponentColumns" data-toggle="tab"><?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_TABLE_COLUMNS'); ?></a>
			</li>
			<li role="presentation">
				<a href="#mainComponentEditForm" data-toggle="tab" class="translateTable-editForm">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_TABLE_EDIT_FORM'); ?>
				</a>
			</li>
			<li role="presentation">
				<a href="#mainComponentInfo" data-toggle="tab" class="translateTable-infoForm">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_TABLE_INFO'); ?>
				</a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active in" id="mainComponentColumns">
				<h4><?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_MODIFY_WARNING'); ?></h4>
				<hr/>
				<div class="form-inline">
					<button type="button" class="btn btn-default btn-primary columns-add-new-row" onclick="javascript:addNewColumnToList(false)">
						<i class="icon-plus"></i>
						<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_ADD_NEW_COLUMN'); ?>
					</button>
					<div class="input-group">
						<span class="input-group-btn">
							<button class="btn btn-primary columns-add-new-row" type="button" onclick="javascript:addNewColumnToList(true)"><i class="icon-plus"></i>
								<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_ADD_NEW_COLUMN_FROM_DATABASE'); ?>
							</button>
						</span>
						<span class="input-group-addon table-columns-selection">
						</span>
					</div>
				</div>
				<hr/>
				<div class="columns-row-list container-fluid">
					<?php
					if (!empty($this->item->columns)) :
						foreach ($this->item->columns as $column) :
							echo RLayoutHelper::render(
								'translation.table.column',
								array(
									'view' => $this,
									'options' => array(
										'column' => $column,
										'form'   => $this->form,
									)
								)
							);
						endforeach;
					endif;
					?>
				</div>
			</div>
			<div class="tab-pane" id="mainComponentEditForm">
				<h4><?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_EDIT_FORM_DESC'); ?></h4>
				<hr/>
				<div class="form-inline">
					<button type="button" class="btn btn-default btn-primary editform-add-new-row" onclick="javascript:addNewEditFormToList(false)">
						<i class="icon-plus"></i>
						<?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_ADD_NEW_EDIT_FORM'); ?>
					</button>
				</div>
				<hr/>
				<div class="editform-row-list container-fluid">
					<?php
					if (!empty($this->item->editForms)) :
						foreach ($this->item->editForms as $editForm) :
							echo RLayoutHelper::render(
								'translation.table.editform',
								array(
									'view' => $this,
									'options' => array(
										'editForm' => $editForm,
										'form'   => $this->form,
									)
								)
							);
						endforeach;
					endif;
					?>
				</div>
			</div>
			<div class="tab-pane" id="mainComponentInfo">
				<h4><?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_INFO_DESC'); ?></h4>
				<hr/>
				<div id="info-params">
					<div class="form-group">
						<div class="col-md-2 col-sm-3">
							<?php echo $this->form->getLabel('version') ?>
						</div>
						<div class="col-md-10 col-sm-9">
							<?php echo $this->form->getInput('version') ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-2 col-sm-3">
							<?php echo $this->form->getLabel('author') ?>
						</div>
						<div class="col-md-10 col-sm-9">
							<?php echo $this->form->getInput('author') ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-2 col-sm-3">
							<?php echo $this->form->getLabel('copyright') ?>
						</div>
						<div class="col-md-10 col-sm-9">
							<?php echo $this->form->getInput('copyright') ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-2 col-sm-3">
							<?php echo $this->form->getLabel('description') ?>
						</div>
						<div class="col-md-10 col-sm-9">
							<?php echo $this->form->getInput('description') ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- hidden fields -->
	<input type="hidden" name="option" value="com_redcore">
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
	<input type="hidden" name="fromEditForm" value="1">
	<input type="hidden" name="task" value="">
	<?php echo JHTML::_('form.token'); ?>
</form>
<script type="text/javascript">
	function changeOptionName(obj){
		jQuery('#jform_extension_name').val(obj.value);
	}
	function changeTableName(obj){
		jQuery('#jform_name').val('#__' + obj.value);
		fillColumnsDropdown();
	}

	function fillColumnsDropdown(){
		var getData = {};
		getData.tableName = jQuery('#jform_name').val();

		jQuery.ajax({
			url: 'index.php?option=com_redcore&task=translation_table.ajaxGetColumns',
			data: getData,
			dataType: 'text',
			beforeSend: function ()
			{
				jQuery('.table-columns-selection').addClass('opacity-40');
			}
		}).done(function (data){
			jQuery('.table-columns-selection').removeClass('opacity-40').html(data);
			jQuery('select').chosen();
			jQuery('.hasTooltip').tooltip();
		});
	}

	function addNewColumnToList(fromList){
		var getData = {};
		if (fromList)
		{
			getData.columnName = jQuery('#tableColumnList').val();
			getData.tableName = jQuery('#jform_name').val();
		}

		jQuery.ajax({
			url: 'index.php?option=com_redcore&task=translation_table.ajaxGetColumn',
			data: getData,
			dataType: 'text',
			beforeSend: function ()
			{
				jQuery('.columns-row-list').addClass('opacity-40');
			}
		}).done(function (data){
			jQuery('.columns-row-list').removeClass('opacity-40').prepend(data);
			jQuery('select').chosen();
			jQuery('.hasTooltip').tooltip();
			rRadioGroupButtonsSet('.columns-row-list');
			rRadioGroupButtonsEvent('.columns-row-list');
			jQuery('.columns-row-list :input[checked="checked"]').click();
		});
	}

	function addNewEditFormToList(fromList){
		var getData = {};
		getData.extensionName = jQuery('#jform_extension_name').val();

		jQuery.ajax({
			url: 'index.php?option=com_redcore&task=translation_table.ajaxGetEditForm',
			data: getData,
			dataType: 'text',
			beforeSend: function ()
			{
				jQuery('.editform-row-list').addClass('opacity-40');
			}
		}).done(function (data){
			jQuery('.editform-row-list').removeClass('opacity-40').prepend(data);
			jQuery('select').chosen();
			jQuery('.hasTooltip').tooltip();
			rRadioGroupButtonsSet('.editform-row-list');
			rRadioGroupButtonsEvent('.editform-row-list');
			jQuery('.editform-row-list :input[checked="checked"]').click();
		});
	}

	jQuery(document).ready(function () {
		fillColumnsDropdown();

		jQuery('body').on('click', '.columns-remove-row, .editform-remove-row, .parameter-remove-row', function(){
			jQuery(this).parents('.row-stripped:first').remove();
		})
		.on('click', '.add-new-parameter', function(){
		jQuery(this).parent().find('.column-parameter-list').prepend(jQuery(this).parent().find('.column-parameter-empty').html());
		});
	});
</script>

