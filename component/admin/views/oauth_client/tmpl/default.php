<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

$action = JRoute::_('index.php?option=com_redcore&view=oauth_client');

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
?>
<form action="<?php echo $action; ?>" method="post" name="adminForm" id="adminForm"
      class="form-validate form-horizontal">
	<div class="row-fluid">
		<div id="main-params" class="span6">
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('client_id'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('client_id'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('redirect_uri'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('redirect_uri'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('grant_types'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('grant_types'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('scope'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('scope'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('user_id'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('user_id'); ?>
				</div>
			</div>
		</div>
		<?php if ($this->item->client_id) : ?>
			<div class="span6 well" style="word-wrap:break-word;">
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('COM_REDCORE_OAUTH_CLIENTS_CLIENT_SECRET'); ?>
					</div>
					<div class="controls">
						<?php echo $this->item->client_secret; ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('COM_REDCORE_OAUTH_CLIENT_FORM_ACCESS_TOKEN'); ?>
					</div>
					<div class="controls">
						<?php if (!empty($this->item->access_token)) : ?>
							<?php echo $this->item->access_token; ?>
							(<?php echo JText::_('COM_REDCORE_OAUTH_CLIENT_FORM_EXPIRES'); ?> <?php echo $this->item->access_token_expires; ?>)
						<?php else : ?>
							--
						<?php endif; ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('COM_REDCORE_OAUTH_CLIENT_FORM_AUTHORIZATION_CODE'); ?>
					</div>
					<div class="controls">
						<?php if (!empty($this->item->authorization_code)) : ?>
							<?php echo $this->item->authorization_code; ?>
							(<?php echo JText::_('COM_REDCORE_OAUTH_CLIENT_FORM_EXPIRES'); ?> <?php echo $this->item->authorization_code_expires; ?>)
						<?php else : ?>
							--
						<?php endif; ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label">
						<?php echo JText::_('COM_REDCORE_OAUTH_CLIENT_FORM_REFRESH_TOKEN'); ?>
					</div>
					<div class="controls">
						<?php if (!empty($this->item->refresh_token)) : ?>
							<?php echo $this->item->refresh_token; ?>
							(<?php echo JText::_('COM_REDCORE_OAUTH_CLIENT_FORM_EXPIRES'); ?> <?php echo $this->item->refresh_token_expires; ?>)
						<?php else : ?>
							--
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<!-- hidden fields -->
	<input type="hidden" name="option" value="com_redcore">
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
	<input type="hidden" name="task" value="">
	<?php echo JHTML::_('form.token'); ?>
</form>
