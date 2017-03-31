<?php
/**
 * @package     Redcore.Layouts
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Layout variables
 * ========================
 * @var  array  $displayData  List of available data.
 * @var  RForm  $form         Form data.
 */
extract($displayData);

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
?>

<div class="tab-pane" id="mainComponentConfiguration">
	<p class="lead"><?php echo JText::_('COM_REDCORE_CONFIG_MAIN_COMPONENT_CONFIGURATION_DESC'); ?></p>
	<?php if (!empty($form)) : ?>
		<?php $fieldSets = $form->getFieldsets(); ?>
		<div class="row-fluid">
			<div class="span3">
				<ul class="nav nav-stacked nav-pills" id="configTabs">
					<?php foreach ($fieldSets as $name => $fieldSet) : ?>
						<?php if ($name == 'translations') : ?>
							<?php continue; ?>
						<?php endif; ?>
						<?php $label = empty($fieldSet->label) ? 'COM_CONFIG_' . $name . '_FIELDSET_LABEL' : $fieldSet->label; ?>
						<li><a href="#<?php echo $name; ?>" data-toggle="tab"><?php echo JText::_($label); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="span9">
				<div class="tab-content">
					<?php foreach ($fieldSets as $name => $fieldSet) : ?>
						<?php if ($name == 'translations') : ?>
							<?php continue; ?>
						<?php endif; ?>
						<div class="tab-pane" id="<?php echo $name; ?>">
							<div class="form-horizontal">
								<?php if (isset($fieldSet->description) && !empty($fieldSet->description)) : ?>
									<p class="tab-description"><?php echo JText::_($fieldSet->description); ?></p>
								<?php endif; ?>
								<?php $count = 1; ?>
								<?php foreach ($form->getFieldset($name) as $field): ?>
									<div class="row-<?php echo ($count % 2) ? 'even' : 'odd' ?>">
										<?php echo $field->renderField(); ?>
									</div>
									<?php $count++; ?>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			jQuery('#configTabs a:first').tab('show'); // Select first tab
		</script>
	<?php endif; ?>
</div>
