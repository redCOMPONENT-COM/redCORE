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
<div role="tabpanel">
	<ul class="nav nav-tabs" id="readTabs" role="tablist">
		<?php foreach ($this->formData as $operation => $operationData): ?>
			<?php if (substr($operation, 0, strlen('read-')) === 'read-') : ?>
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
		if (substr($operation, 0, strlen('read-')) === 'read-') :
			$fieldList = array('defaultValue', 'isRequiredField', 'isPrimaryField');

			if ($operation == 'read-list')
			{
				$fieldList = array_merge($fieldList, array('isFilterField', 'isSearchableField'));
			}

			echo RLayoutHelper::render(
				'webservice.operation',
				array(
					'view' => $this,
					'options' => array(
						'operation' => $operation,
						'form'      => $this->form,
						'tabActive' => $firstContentActive ? ' active in ' : '',
						'fieldList' => $fieldList,
					)
				)
			);

			$firstContentActive = false;
		endif;
		?>
	<?php endforeach; ?>
</div>
