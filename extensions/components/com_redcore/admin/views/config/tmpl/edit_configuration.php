<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
?>
<div class="tab-pane" id="mainComponentConfiguration">
	<p class="tab-description"><?php echo JText::_('COM_REDCORE_CONFIG_MAIN_COMPONENT_CONFIGURATION_DESC'); ?></p>
	<?php if (!empty($this->form)) : ?>
		<div class="col-md-12">
			<ul class="nav nav-tabs" id="configTabs">
				<?php $fieldSets = $this->form->getFieldsets(); ?>
				<?php foreach ($fieldSets as $name => $fieldSet) : ?>
					<?php if ($name == 'translations') : ?>
						<?php continue; ?>
					<?php endif; ?>
					<?php $label = empty($fieldSet->label) ? 'COM_CONFIG_' . $name . '_FIELDSET_LABEL' : $fieldSet->label; ?>
					<li><a href="#<?php echo $name; ?>" data-toggle="tab"><?php echo JText::_($label); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<div class="tab-content">
				<?php $fieldSets = $this->form->getFieldsets(); ?>
				<?php foreach ($fieldSets as $name => $fieldSet) : ?>
					<?php if ($name == 'translations') : ?>
						<?php continue; ?>
					<?php endif; ?>
					<div class="tab-pane" id="<?php echo $name; ?>">
						<?php if (isset($fieldSet->description) && !empty($fieldSet->description)) : ?>
							<p class="tab-description"><?php echo JText::_($fieldSet->description); ?></p>
						<?php endif; ?>
						<?php foreach ($this->form->getFieldset($name) as $field): ?>
							<div class="form-group">
								<?php echo $field->renderField(); ?>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<script type="text/javascript">
			jQuery('#configTabs a:first').tab('show'); // Select first tab
		</script>
	<?php endif; ?>
</div>

