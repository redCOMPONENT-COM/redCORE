<?php
/**
 * @package    Redcore.Backend
 * @subpackage Templates
 *
 * @copyright  Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$template = $app->getTemplate();

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
?>
<form action="<?php echo JRoute::_('index.php?option=com_redcore'); ?>"
      id="adminForm" method="post" name="adminForm" autocomplete="off" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span10">
			<ul class="nav nav-tabs" id="configTabs">
				<?php $fieldSets = $this->form->getFieldsets(); ?>
				<?php foreach ($fieldSets as $name => $fieldSet) : ?>
					<?php $label = empty($fieldSet->label) ? 'COM_CONFIG_' . $name . '_FIELDSET_LABEL' : $fieldSet->label; ?>
					<li><a href="#<?php echo $name; ?>" data-toggle="tab"><?php echo JText::_($label); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<div class="tab-content">
				<?php $fieldSets = $this->form->getFieldsets(); ?>
				<?php foreach ($fieldSets as $name => $fieldSet) : ?>
					<div class="tab-pane" id="<?php echo $name; ?>">
						<?php if (isset($fieldSet->description) && !empty($fieldSet->description)) : ?>
							<p class="tab-description"><?php echo JText::_($fieldSet->description); ?></p>
						<?php endif; ?>
						<?php foreach ($this->form->getFieldset($name) as $field): ?>
							<div class="control-group">
								<?php if (!$field->hidden && $name != "permissions") : ?>
									<div class="control-label">
										<?php echo $field->label; ?>
									</div>
								<?php endif; ?>
								<div class="<?php if ($name != "permissions") : ?>controls<?php endif; ?>">
									<?php echo $field->input; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<div>
		<input type="hidden" name="id" value="<?php echo $this->component->id; ?>" />
		<input type="hidden" name="component" value="<?php echo $this->component->option; ?>" />
		<input type="hidden" name="element" value="<?php echo $this->component->option; ?>" />
		<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<script type="text/javascript">
	jQuery('#configTabs a:first').tab('show'); // Select first tab
</script>
