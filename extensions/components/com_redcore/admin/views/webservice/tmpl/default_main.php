<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

?>
<div id="main-params">
	<h2><?php echo JText::_('COM_REDCORE_WEBSERVICE_TAB_GENERAL'); ?></h2>
	<div class="form-group">
		<?php echo $this->form->getLabel('client', 'main'); ?>
		<div class="col-sm-10">
			<?php echo $this->form->getInput('client', 'main'); ?>
		</div>
	</div>

	<div class="form-group">
		<label for="jform_name" class="col-sm-2 control-label">
			<?php echo JText::_('COM_REDCORE_WEBSERVICE_NAME_LABEL'); ?> / <?php echo JText::_('COM_REDCORE_WEBSERVICE_VERSION_LABEL'); ?>
		</label>
		<div class="form-inline col-sm-10">
			<div class="input-group">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_WEBSERVICE_NAME_DESCRIPTION'); ?>">
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_NAME_LABEL'); ?>
				</div>
				<?php echo $this->form->getInput('name', 'main'); ?>
			</div>
			<div class="input-group">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_WEBSERVICE_VERSION_DESCRIPTION'); ?>">
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_VERSION_LABEL'); ?>
				</div>
				<?php echo $this->form->getInput('version', 'main'); ?>
			</div>
		</div>
	</div>

	<div class="form-group">
		<?php echo $this->form->getLabel('path', 'main'); ?>
		<div class="form-inline col-sm-10">
			<div class="input-group">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_WEBSERVICE_PATH_DESCRIPTION'); ?>">
					/<?php echo RApiHalHelper::getWebservicesRelativePath(); ?>/
				</div>
				<?php echo $this->form->getInput('path', 'main'); ?>
				<span class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_WEBSERVICE_FILE_DESCRIPTION'); ?>">
					/<?php echo $this->form->getValue('xmlFile', 'main'); ?>
				</span>
			</div>
		</div>
	</div>

	<div class="form-group">
		<?php echo $this->form->getLabel('authorizationAssetName', 'main'); ?>
		<div class="col-sm-10">
			<?php echo $this->form->getInput('authorizationAssetName', 'main'); ?>
		</div>
	</div>
	<div class="form-group">
		<?php echo $this->form->getLabel('title', 'main'); ?>
		<div class="col-sm-10">
			<?php echo $this->form->getInput('title', 'main'); ?>
		</div>
	</div>
	<div class="form-group">
		<?php echo $this->form->getLabel('author', 'main'); ?>
		<div class="col-sm-10">
			<?php echo $this->form->getInput('author', 'main'); ?>
		</div>
	</div>
	<div class="form-group">
		<?php echo $this->form->getLabel('copyright', 'main'); ?>
		<div class="col-sm-10">
			<?php echo $this->form->getInput('copyright', 'main'); ?>
		</div>
	</div>
	<div class="form-group">
		<?php echo $this->form->getLabel('state', 'main'); ?>
		<div class="col-sm-10">
			<?php echo $this->form->getInput('state', 'main'); ?>
		</div>
	</div>
	<div class="form-group">
		<?php echo $this->form->getLabel('description', 'main'); ?>
		<div class="col-sm-10">
			<?php echo $this->form->getInput('description', 'main'); ?>
		</div>
	</div>
</div>
