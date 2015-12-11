<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;
$firstTabActive = true;
$firstContentActive = true;

?>
<br/>
<div class="row fields-add-new-complex-type-row">
	<div class="col-lg-4">
		<div class="input-group">
			<input type="text" class="form-control" name="newType" onblur="this.value = this.value.replace(/[^\w]/g,'')"
			       placeholder="<?php echo JText::_('COM_REDCORE_WEBSERVICE_COMPLEX_TYPES_ADD_NEW_COMPLEX_TYPE_PLACEHOLDER'); ?>" />
			<span class="input-group-btn">
				<button type="button" class="btn btn-default btn-success fields-add-new-task">
					<i class="icon-plus"></i>
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_COMPLEX_TYPES_ADD_NEW_COMPLEX_TYPE'); ?>
				</button>
			</span>
		</div>
	</div>
</div>
<br/>
<div role="tabpanel">
	<ul class="nav nav-tabs" id="taskTabs" role="tablist">
		<?php foreach ($this->formData as $operation => $operationData): ?>
			<?php if (substr($operation, 0, strlen('type-')) === 'type-') : ?>
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
		<?php $tabActive = $firstContentActive ? ' active in ' : '';?>
	<div role="tabpanel" class="tab-pane <?php echo $tabActive; ?>" id="operationTab<?php echo $operation; ?>">
		<?php
		if (substr($operation, 0, strlen('type-')) === 'type-') :
			echo RLayoutHelper::render(
				'webservice.fields',
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
	</div>
	<?php endforeach;?>
</div>
