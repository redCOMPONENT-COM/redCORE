<?php
/**
 * @package     Redcore.Webservice
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$view = $displayData['view'];

$operation = !empty($displayData['options']['operation']) ? $displayData['options']['operation'] : 'read-list';
$fieldList = !empty($displayData['options']['fieldList']) ? $displayData['options']['fieldList'] : array();
$form = !empty($displayData['options']['form']) ? $displayData['options']['form'] : null;
$readListValues = $operation == 'read-list' ? ',isFilterField,isSearchableField' : '';

?>
<div class="ws-rows ws-Field-<?php echo $operation; ?>">
	<hr/>
	<fieldset>
		<legend><?php echo JText::_('COM_REDCORE_WEBSERVICE_FIELDS_LABEL'); ?></legend>
		<div class="form-group">
			<?php echo $form->getLabel('description', $operation . '.fields'); ?>
			<div class="col-sm-10">
				<?php echo $form->getInput('description', $operation . '.fields'); ?>
			</div>
		</div>
		<div class="form-inline">
			<button type="button" class="btn btn-default btn-primary fields-add-new-row">
				<input type="hidden" name="addNewRowType" value="Field" />
				<input type="hidden" name="addNewRowOperation" value="<?php echo $operation; ?>" />
				<input type="hidden" name="addNewRowList" value="defaultValue,isRequiredField,isPrimaryField<?php echo $readListValues; ?>" />
				<i class="icon-plus"></i>
				<?php echo JText::_('COM_REDCORE_WEBSERVICE_FIELD_ADD_NEW_LABEL'); ?>
			</button>
			<div class="input-group">
				<span class="input-group-btn">
					<button class="btn btn-primary fields-add-new-row" type="button"><i class="icon-plus"></i>
						<input type="hidden" name="addNewRowType" value="Field" />
						<input type="hidden" name="addNewOptionType" value="FieldFromDatabase" />
						<input type="hidden" name="addNewRowOperation" value="<?php echo $operation; ?>" />
						<input type="hidden" name="addNewRowList" value="defaultValue,isRequiredField,isPrimaryField<?php echo $readListValues; ?>" />
						<?php echo JText::_('COM_REDCORE_WEBSERVICE_FIELD_ADD_NEW_FROM_DATABASE_LABEL'); ?>
					</button>
				</span>
				<span class="input-group-addon">
					<?php echo $form->getInput('addFromDatabase', 'main'); ?>
				</span>
			</div>
		</div>

		<hr/>
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-2">
					<strong><?php echo JText::_('JOPTIONS'); ?></strong>
				</div>
				<div class="col-xs-2">
					<strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_NAME'); ?></strong>
				</div>
				<div class="col-xs-1">
					<strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_TRANSFORM'); ?></strong>
				</div>
				<?php if (in_array('defaultValue', $fieldList)) : ?>
					<div class="col-xs-1">
						<strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_DEFAULT_VALUE'); ?></strong>
					</div>
				<?php endif; ?>
				<?php if (in_array('isRequiredField', $fieldList)) : ?>
					<div class="col-xs-1">
						<strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_REQUIRED'); ?></strong>
					</div>
				<?php endif; ?>
				<?php if (in_array('isFilterField', $fieldList)) : ?>
					<div class="col-xs-1">
						<strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_FILTER'); ?></strong>
					</div>
				<?php endif; ?>
				<?php if (in_array('isSearchableField', $fieldList)) : ?>
					<div class="col-xs-1">
						<strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_SEARCHABLE'); ?></strong>
					</div>
				<?php endif; ?>
				<?php if (in_array('isPrimaryField', $fieldList)) : ?>
					<div class="col-xs-1">
						<strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_PRIMARY'); ?></strong>
					</div>
				<?php endif; ?>
				<div class="col-xs-2">
					<strong><?php echo JText::_('JGLOBAL_DESCRIPTION'); ?></strong>
				</div>
			</div>

			<div class="ws-row-list">
				<?php
				if (!empty($view->fields[$operation])) :
					foreach ($view->fields[$operation] as $field) :
						$displayData['options']['form'] = $field;
						echo $this->sublayout('field', $displayData);
					endforeach;
				endif;
				?>
			</div>
		</div>
	</fieldset>
	<hr/>
</div>
