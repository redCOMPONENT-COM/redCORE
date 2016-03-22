<?php
/**
 * @package     Redcore.Webservice
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$operation = !empty($displayData['options']['operation']) ? $displayData['options']['operation'] : 'read';
$form = !empty($displayData['options']['form']) ? $displayData['options']['form'] : null;

?>
<h2>
	<?php echo JText::_('COM_REDCORE_WEBSERVICE_OPERATION_LABEL') ?>: <?php echo $operation ?>
</h2>
<div class="form-group">
	<?php echo $form->getLabel('isEnabled', $operation); ?>
	<div class="col-sm-10">
		<?php echo $form->getInput('isEnabled', $operation); ?>
	</div>
</div>
<fieldset class="ws-operation-configuration">
	<?php if (substr($operation, 0, strlen('task-')) === 'task-') : ?>
		<div class="form-group">
			<?php echo $form->getLabel('useOperation', $operation); ?>
			<div class="col-sm-10">
				<?php echo $form->getInput('useOperation', $operation); ?>
			</div>
		</div>
	<?php endif; ?>
	<fieldset class="ws-use-operation-fieldset">
		<div class="form-group">
			<?php echo $form->getLabel('authorizationNeeded', $operation); ?>
			<div class="col-sm-10">
				<?php echo $form->getInput('authorizationNeeded', $operation); ?>
			</div>
		</div>
		<div class="form-group">
			<?php echo $form->getLabel('authorization', $operation); ?>
			<div class="col-sm-10">
				<?php echo $form->getInput('authorization', $operation); ?>
			</div>
		</div>
		<div class="form-group">
			<?php echo $form->getLabel('strictFields', $operation); ?>
			<div class="col-sm-10">
				<?php echo $form->getInput('strictFields', $operation); ?>
			</div>
		</div>
		<div class="form-group">
			<?php echo $form->getLabel('dataMode', $operation); ?>
			<div class="col-sm-10">
				<?php echo $form->getInput('dataMode', $operation); ?>
			</div>
		</div>
		<div class="ws-dataMode ws-dataMode-model">
			<div class="form-group">
				<?php echo $form->getLabel('optionName', $operation); ?>
				<div class="col-sm-10">
					<?php echo $form->getInput('optionName', $operation); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->getLabel('modelClassName', $operation); ?>
				<div class="col-sm-10">
					<?php echo $form->getInput('modelClassName', $operation); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->getLabel('modelClassPath', $operation); ?>
				<div class="col-sm-10">
					<?php echo $form->getInput('modelClassPath', $operation); ?>
				</div>
			</div>
			<div class="form-group">
				<?php echo $form->getLabel('isAdminClass', $operation); ?>
				<div class="col-sm-10">
					<?php echo $form->getInput('isAdminClass', $operation); ?>
				</div>
			</div>
		</div>

		<div class="ws-dataMode ws-dataMode-table">
			<div class="form-group">
				<?php echo $form->getLabel('tableName', $operation); ?>
				<div class="col-sm-10">
					<?php echo $form->getInput('tableName', $operation); ?>
				</div>
			</div>
		</div>

		<div class="form-group">
			<?php echo $form->getLabel('functionName', $operation); ?>
			<div class="col-sm-10">
				<?php echo $form->getInput('functionName', $operation); ?>
			</div>
		</div>
		<?php if ($operation === 'read-list') : ?>
		<div class="form-group">
			<?php echo $form->getLabel('paginationFunction', $operation); ?>
			<div class="col-sm-10">
				<?php echo $form->getInput('paginationFunction', $operation); ?>
			</div>
		</div>
		<div class="form-group">
			<?php echo $form->getLabel('totalFunction', $operation); ?>
			<div class="col-sm-10">
				<?php echo $form->getInput('totalFunction', $operation); ?>
			</div>
		</div>
		<?php endif; ?>
		<div class="form-group">
			<?php echo $form->getLabel('functionArgs', $operation); ?>
			<div class="col-sm-10">
				<?php echo $form->getInput('functionArgs', $operation); ?>
			</div>
		</div>
		<div class="form-group">
			<?php echo $form->getLabel('validateData', $operation); ?>
			<div class="col-sm-10">
				<?php echo $form->getInput('validateData', $operation); ?>
			</div>
		</div>
		<div class="ws-validateData ws-validateData-function">
			<div class="form-group">
				<?php echo $form->getLabel('validateDataFunction', $operation); ?>
				<div class="col-sm-10">
					<?php echo $form->getInput('validateDataFunction', $operation); ?>
				</div>
			</div>
		</div>
		<div class="form-group">
			<?php echo $form->getLabel('description', $operation); ?>
			<div class="col-sm-10">
				<?php echo $form->getInput('description', $operation); ?>
			</div>
		</div>
	</fieldset>
</fieldset>
