<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;
$firstTabActive = true;
$firstContentActive = true;
?>
<br />
<div class="row fields-add-new-task-row">
	<div class="col-lg-4">
		<div class="input-group">
			<input type="text" class="form-control" name="newTask" onblur="this.value = this.value.replace(/[^\w]/g,'')"
			       placeholder="<?php echo JText::_('COM_REDCORE_WEBSERVICE_TASK_ADD_NEW_TASK_PLACEHOLDER'); ?>" />
			<span class="input-group-btn">
				<button type="button" class="btn btn-default btn-success fields-add-new-task"
				        data-no-task-msg="<?php echo JText::_('COM_REDCORE_WEBSERVICE_TASK_ADD_NEW_TASK_ERROR', true, true);?>">
					<i class="icon-plus"></i>
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_TASK_ADD_NEW_TASK'); ?>
				</button>
			</span>
		</div>
	</div>
</div>
<br />
<div role="tabpanel">
	<ul class="nav nav-tabs" id="taskTabs" role="tablist">
		<?php foreach ($this->formData as $operation => $operationData): ?>
			<?php if (substr($operation, 0, strlen('task-')) === 'task-') : ?>
				<li role="presentation" <?php echo $firstTabActive ? ' class="active" ' : ''; ?>>
					<a href="#operationTab<?php echo $operation; ?>" id="operation-<?php echo $operation; ?>-tab" role="tab" data-toggle="tab">
						<?php echo $operation; ?>
					</a>
				</li>
				<?php $firstTabActive = false; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
</div>
<div class="tab-content">
	<?php foreach ($this->formData as $operation => $operationData): ?>
		<?php
		if (substr($operation, 0, strlen('task-')) === 'task-') :
			echo RLayoutHelper::render(
				'webservice.operation',
				array(
					'view' => $this,
					'options' => array(
						'operation' => $operation,
						'form'      => $this->form,
						'tabActive' => $firstContentActive ? ' active in ' : '',
						'fieldList' => array('defaultValue', 'isRequiredField', 'isPrimaryField'),
					)
				)
			);

			$firstContentActive = false;
		endif;
		?>
	<?php endforeach; ?>
</div>
