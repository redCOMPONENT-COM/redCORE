<?php
/**
 * @package     Redcore.Webservice
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

extract($displayData);

$editForm = (object) $options['editForm'];

$editForm->identifier = !isset($editForm->identifier) ? 'id' : $editForm->identifier;
$editForm->admin = !isset($editForm->admin) ? 'false' : $editForm->admin;
$editForm->layout = empty($editForm->layout) ? 'edit' : $editForm->layout;
$editForm->showbutton = empty($editForm->showbutton) ? 'true' : $editForm->showbutton;
$editForm->htmlposition = empty($editForm->htmlposition) ? '.btn-toolbar:first' : $editForm->htmlposition;
$editForm->option = empty($editForm->option) ? '' : $editForm->option;
$editForm->view = empty($editForm->view) ? '' : $editForm->view;
$editForm->checkoriginalid = empty($editForm->checkoriginalid) ? 'false' : $editForm->checkoriginalid;
$id = RFilesystemFile::getUniqueName();

?>
<div class="row row-stripped">
	<div class="col-xs-1">
		<button type="button" class="btn btn-default btn-xs btn-danger editform-remove-row">
			<i class="icon-minus"></i>
			<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_REMOVE'); ?>
		</button>
	</div>
	<div class="col-xs-11 ws-row-edit">
		<div class="form-horizontal">
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_IS_ADMIN_DESC'); ?>">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_IS_ADMIN'); ?>
				</div>
				<fieldset class="radio btn-group">
					<input id="<?php echo $id;?>_admin1" type="radio" name="jform[editForms][<?php echo $id;?>][admin]"
					       value="true" <?php echo $editForm->admin == 'false' ? '' : ' checked="checked" '; ?> />
					<label for="<?php echo $id;?>_admin1" class="btn btn-default"><?php echo JText::_('JYES'); ?></label>
					<input id="<?php echo $id;?>_admin0" type="radio" name="jform[editForms][<?php echo $id;?>][admin]"
					       value="false" <?php echo $editForm->admin == 'false' ? ' checked="checked" ' : ''; ?> />
					<label for="<?php echo $id;?>_admin0" class="btn btn-default"><?php echo JText::_('JNO'); ?></label>
				</fieldset>
			</div>
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_COMPONENT_NAME_DESC'); ?>">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_COMPONENT_NAME'); ?>
				</div>
				<input type="text" name="jform[editForms][<?php echo $id;?>][option]" value="<?php echo $editForm->option;?>" class="form-control" />
			</div>
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_VIEW_NAME_DESC'); ?>">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_VIEW_NAME'); ?>
				</div>
				<input type="text" name="jform[editForms][<?php echo $id;?>][view]" value="<?php echo $editForm->view;?>" class="form-control" />
			</div>
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_LAYOUT_NAME_DESC'); ?>">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_LAYOUT_NAME'); ?>
				</div>
				<input type="text" name="jform[editForms][<?php echo $id;?>][layout]" value="<?php echo $editForm->layout;?>" class="form-control" />
			</div>
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_IDENTIFIER_DESC'); ?>">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_IDENTIFIER'); ?>
				</div>
				<input type="text" name="jform[editForms][<?php echo $id;?>][identifier]" value="<?php echo $editForm->identifier;?>" class="form-control" />
			</div>
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_SHOW_BUTTON_DESC'); ?>">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_SHOW_BUTTON'); ?>
				</div>
				<fieldset class="radio btn-group">
					<input id="<?php echo $id;?>_showbutton1" type="radio" name="jform[editForms][<?php echo $id;?>][showbutton]"
					       value="true" <?php echo $editForm->showbutton == 'false' ? '' : ' checked="checked" '; ?> />
					<label for="<?php echo $id;?>_showbutton1" class="btn btn-default"><?php echo JText::_('JYES'); ?></label>
					<input id="<?php echo $id;?>_showbutton0" type="radio" name="jform[editForms][<?php echo $id;?>][showbutton]"
					       value="false" <?php echo $editForm->showbutton == 'false' ? ' checked="checked" ' : ''; ?> />
					<label for="<?php echo $id;?>_showbutton0" class="btn btn-default"><?php echo JText::_('JNO'); ?></label>
				</fieldset>
			</div>
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_BUTTON_HTML_POSITION_DESC'); ?>">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_BUTTON_HTML_POSITION'); ?>
				</div>
				<input type="text" name="jform[editForms][<?php echo $id;?>][htmlposition]" value="<?php echo $editForm->htmlposition;?>" class="form-control" />
			</div>
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_BUTTON_CHECK_ORIGINAL_ID_DESC'); ?>">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_EDIT_FORM_BUTTON_CHECK_ORIGINAL_ID'); ?>
				</div>
				<fieldset class="radio btn-group">
					<input id="<?php echo $id;?>_checkoriginalid1" type="radio" name="jform[editForms][<?php echo $id;?>][checkoriginalid]"
					       value="true" <?php echo $editForm->checkoriginalid == 'false' ? '' : ' checked="checked" '; ?> />
					<label for="<?php echo $id;?>_checkoriginalid1" class="btn btn-default"><?php echo JText::_('JYES'); ?></label>
					<input id="<?php echo $id;?>_checkoriginalid0" type="radio" name="jform[editForms][<?php echo $id;?>][checkoriginalid]"
					       value="false" <?php echo $editForm->checkoriginalid == 'false' ? ' checked="checked" ' : ''; ?> />
					<label for="<?php echo $id;?>_checkoriginalid0" class="btn btn-default"><?php echo JText::_('JNO'); ?></label>
				</fieldset>
			</div>
		</div>
	</div>
</div>
