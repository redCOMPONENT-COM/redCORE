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
$form = !empty($displayData['options']['form']) ? $displayData['options']['form'] : null;
$heading = !empty($displayData['options']['heading']) ?
	$displayData['options']['heading'] : JText::_('COM_REDCORE_WEBSERVICE_RESOURCES_LABEL');
$headingDescription = !empty($displayData['options']['headingDescription']) ?
	$displayData['options']['headingDescription'] : JText::_('COM_REDCORE_WEBSERVICE_RESOURCES_DESCRIPTION');

?>
<div class="ws-rows ws-Resource-<?php echo $operation; ?>">
	<hr/>
	<fieldset>
		<legend>
			<span class="hasTooltip" title="<?php echo $headingDescription; ?>">
				<?php echo $heading; ?>
			</span>
		</legend>
		<div class="form-group">
			<?php echo $form->getLabel('description', $operation . '.resources'); ?>
			<div class="col-sm-10">
				<?php echo $form->getInput('description', $operation . '.resources'); ?>
			</div>
		</div>
		<div class="form-inline">
			<button type="button" class="btn btn-default btn-primary fields-add-new-row">
				<input type="hidden" name="addNewRowType" value="Resource" />
				<input type="hidden" name="addNewRowOperation" value="<?php echo $operation; ?>" />
				<input type="hidden" name="addNewRowList" value="" />
				<i class="icon-plus"></i>
				<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_ADD_NEW_LABEL'); ?>
			</button>
			<button type="button" class="btn btn-default btn-primary fields-add-new-row">
				<input type="hidden" name="addNewRowType" value="Resource" />
				<input type="hidden" name="addNewRowOperation" value="<?php echo $operation; ?>" />
				<input type="hidden" name="addNewRowList" value="link" />
				<i class="icon-plus"></i>
				<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_ADD_NEW_LINK_LABEL'); ?>
			</button>
			<div class="input-group">
				<span class="input-group-btn">
					<button class="btn btn-primary fields-add-new-row" type="button"><i class="icon-plus"></i>
						<input type="hidden" name="addNewRowType" value="Resource" />
						<input type="hidden" name="addNewOptionType" value="ResourceFromDatabase" />
						<input type="hidden" name="addNewRowOperation" value="<?php echo $operation; ?>" />
						<input type="hidden" name="addNewRowList" value="" />
						<?php echo JText::_('COM_REDCORE_WEBSERVICE_FIELD_ADD_NEW_FROM_DATABASE_LABEL'); ?>
					</button>
				</span>
				<span class="input-group-addon">
					<?php echo $form->getInput('addFromDatabase', 'main'); ?>
				</span>
			</div>
			<div class="input-group">
				<span class="input-group-btn">
					<button class="btn btn-primary fields-add-new-row hasTooltip" type="button"
					        data-original-title="<?php echo JText::_('COM_REDCORE_WEBSERVICE_LIST_DESCRIPTION'); ?>">
						<i class="icon-plus"></i>
						<input type="hidden" name="addNewRowType" value="Resource" />
						<input type="hidden" name="addNewOptionType" value="ConnectWebservice" />
						<input type="hidden" name="addNewRowOperation" value="<?php echo $operation; ?>" />
						<input type="hidden" name="addNewRowList" value="" />
						<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_ADD_CONNECTION_LABEL'); ?>
					</button>
				</span>
				<span class="input-group-addon">
					<?php echo $form->getInput('connectWebservice', 'main'); ?>
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
					<strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_RESOURCE_NAME'); ?></strong>
				</div>
				<div class="col-xs-1">
					<strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_RESOURCE_GROUP'); ?></strong>
				</div>
				<div class="col-xs-4">
					<strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_RESOURCE_FORMAT'); ?></strong>
				</div>
				<div class="col-xs-2">
					<strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_RESOURCE_PARAMETERS'); ?></strong>
				</div>
				<div class="col-xs-1">
					<strong><?php echo JText::_('JGLOBAL_DESCRIPTION'); ?></strong>
				</div>
			</div>
			<div class="ws-row-list">
				<?php
					if (!empty($view->resources[$operation])) :
						foreach ($view->resources[$operation] as $resourceSpecific) :
							foreach ($resourceSpecific as $resource) :
								$displayData['options']['form'] = $resource;
								echo $this->sublayout('resource', $displayData);
							endforeach;
						endforeach;
					endif;
				?>
			</div>
		</div>
	</fieldset>
	<hr/>
</div>
