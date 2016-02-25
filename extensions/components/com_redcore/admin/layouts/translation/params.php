<?php
/**
 * @package     Redcore.Translation
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

$form = !empty($displayData['form']) ? $displayData['form'] : null;
$original = !empty($displayData['original']) ? $displayData['original'] : null;
$translation = !empty($displayData['translation']) ? $displayData['translation'] : null;
$name = !empty($displayData['name']) ? $displayData['name'] : '';
$column = !empty($displayData['column']) ? $displayData['column'] : null;
$translationForm = !empty($displayData['translationForm']) ? $displayData['translationForm'] : null;
$formType = $translationForm == true ? 'translationForm' : 'originalForm';
$selectedFieldSets = !empty($column['fieldsets']) ? explode(',', $column['fieldsets']) : null;
$selectedGroup = !empty($column['fieldsname']) ? $column['fieldsname'] : $name;
$fieldSetNameAddition = !empty($column['formname']) && $column['formname'] == 'plugin' ? 'COM_PLUGINS_' : '';

$fieldSets = !empty($form) ? $form->getFieldsets($selectedGroup) : array();
?>
<div class="tab-pane params-pane<?php echo $formType;?>">
	<?php if (!empty($form)) : ?>
		<div class="col-md-12">
			<ul class="nav nav-tabs params-tabs<?php echo $formType;?>">
				<?php foreach ($fieldSets as $name => $fieldSet) : ?>
					<?php
					if (!empty($selectedFieldSets) && !in_array($name, $selectedFieldSets)):
						continue;
					endif;
					$oneFieldFound = false;

					foreach ($form->getFieldset($name) as $field):
						if (!$field->hidden):
							$oneFieldFound = true;
							break;
						endif;
					endforeach;
					if (!$oneFieldFound):
						continue;
					endif;
					?>
					<?php $label = empty($fieldSet->label) ?  $fieldSetNameAddition . $name . '_FIELDSET_LABEL' : $fieldSet->label; ?>
					<li><a href="#params_<?php echo $formType;?>_<?php echo $name; ?>" data-toggle="tab"><?php echo RText::getTranslationIfExists($label, '', ''); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<div class="tab-content">
				<?php foreach ($fieldSets as $name => $fieldSet) : ?>
					<?php if (!empty($selectedFieldSets) && !in_array($name, $selectedFieldSets)) : ?>
						<?php continue; ?>
					<?php endif; ?>
					<div class="tab-pane" id="params_<?php echo $formType;?>_<?php echo $name; ?>">
						<?php if (isset($fieldSet->description) && !empty($fieldSet->description)) : ?>
							<p class="tab-description"><?php echo RText::getTranslationIfExists($fieldSet->description, '', ''); ?></p>
						<?php endif; ?>
						<?php foreach ($form->getFieldset($name) as $field): ?>
							<div class="form-group">
								<?php if (!$field->hidden && $name != "permissions") : ?>
									<div class="control-label">
										<?php echo $field->label; ?>
									</div>
								<?php endif; ?>
								<?php if ($name != "permissions") : ?>
									<div class="controls">
										<?php echo $field->input; ?>
									</div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<script type="text/javascript">
			jQuery('.params-tabs<?php echo $formType;?> a:first').tab('show');
			<?php if ($translationForm == false): ?>
			jQuery('.params-pane<?php echo $formType;?> .tab-content :input').unbind().attr("disabled", true);
			<?php endif; ?>
		</script>
	<?php endif; ?>
</div>
