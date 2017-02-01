<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
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
	<div class="container-fluid">
		<div id="main-params">
			<div class="form-group">
				<div class="col-md-2 col-sm-3">
					<?php echo $this->form->getLabel('client_id'); ?>
				</div>
				<div class="col-md-10 col-sm-9">
					<?php echo $this->form->getInput('client_id'); ?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-2 col-sm-3">
					<?php echo $this->form->getLabel('redirect_uri'); ?>
				</div>
				<div class="col-md-10 col-sm-9">
					<?php echo $this->form->getInput('redirect_uri'); ?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-2 col-sm-3">
					<?php echo $this->form->getLabel('user_id'); ?>
				</div>
				<div class="col-md-10 col-sm-9">
					<?php echo $this->form->getInput('user_id'); ?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-2 col-sm-3">
					<?php echo $this->form->getLabel('grant_types'); ?>
				</div>
				<div class="col-md-10 col-sm-9">
					<?php echo $this->form->getInput('grant_types'); ?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-2 col-sm-3">
					<?php echo $this->form->getLabel('scope'); ?>
				</div>
				<div class="col-md-10 col-sm-9">
					<?php echo $this->form->getInput('scope'); ?>
				</div>
			</div>
		</div>
		<?php if ($this->item->client_id) : ?>
			<div class="well" style="word-break:break-all; word-wrap:break-word;">
				<span class="label label-default"><?php echo JText::_('COM_REDCORE_OAUTH_CLIENTS_CLIENT_SECRET'); ?>: </span>
				<div>
					<?php echo $this->item->client_secret; ?>
				</div>
				<span class="label label-default"><?php echo JText::_('COM_REDCORE_OAUTH_CLIENT_FORM_ACCESS_TOKEN'); ?>: </span>
				<div>
					<?php if (!empty($this->item->access_token)) : ?>
						<?php echo $this->item->access_token; ?>
						(<?php echo JText::_('COM_REDCORE_OAUTH_CLIENT_FORM_EXPIRES'); ?> <?php echo $this->item->access_token_expires; ?>)
					<?php else : ?>
						--
					<?php endif; ?>
				</div>
				<span class="label label-default"><?php echo JText::_('COM_REDCORE_OAUTH_CLIENT_FORM_AUTHORIZATION_CODE'); ?>: </span>
				<div>
					<?php if (!empty($this->item->authorization_code)) : ?>
						<?php echo $this->item->authorization_code; ?>
						(<?php echo JText::_('COM_REDCORE_OAUTH_CLIENT_FORM_EXPIRES'); ?> <?php echo $this->item->authorization_code_expires; ?>)
					<?php else : ?>
						--
					<?php endif; ?>
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
