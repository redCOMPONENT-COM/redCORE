<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

$action = JRoute::_('index.php?option=com_redcore&view=payment_configuration');

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
?>
<script type="text/javascript">
jQuery(document).ready(function () {
	jQuery('body').on('change', '#jform_extension_name_dropdown', function(e){
		jQuery('#jform_extension_name').val(jQuery(this).val());
	}).on('change', '#jform_payment_name', function(e){
		if (jQuery(this).val() != '')
		{
			Joomla.submitform('payment_configuration.edit', document.getElementById('adminForm'));
		}
	})
});
</script>
<form action="<?php echo $action; ?>" method="post" name="adminForm" id="adminForm"
      class="form-validate form-horizontal">
	<div class="container-fluid">
		<div id="main-params">
			<div class="form-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('payment_name'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('payment_name'); ?>
				</div>
			</div>
			<div class="form-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('extension_name'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('extension_name'); ?>
					<div class="pull-left">
						<?php echo $this->form->getInput('extension_name_dropdown'); ?>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('owner_name'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('owner_name'); ?>
				</div>
			</div>
			<div class="form-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('state'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('state'); ?>
				</div>
			</div>

			<div class="form-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('params'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('params'); ?>
				</div>
			</div>
			<?php
			$element = new RTranslationContentElement('com_plugins', '');
			$element->name = 'plugins';
			$element->extension_name = 'com_plugins';
			$column = array('name' => 'params', 'formname' => 'plugin');
			echo RLayoutHelper::render(
				'translation.params',
				array(
					'form' => RTranslationHelper::loadParamsForm($column, $element, $this->item, 'plugin'),
					'column' => $column,
					'translationForm' => true,
				)
			);
			?>
		</div>
	</div>

	<!-- hidden fields -->
	<input type="hidden" name="option" value="com_redcore">
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
	<input type="hidden" name="task" value="">
	<?php echo JHTML::_('form.token'); ?>
</form>
