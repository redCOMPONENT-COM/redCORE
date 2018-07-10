<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

$action = JRoute::_('index.php?option=com_redcore&view=webservice_history_log');

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
RHelperAsset::load('redcore.min.js', 'redcore');
?>
<form action="<?php echo $action; ?>" method="post" name="adminForm" id="adminForm"
      class="form-validate form-horizontal" role="form">
	<div>
        <div class="col-sm-12">
            <div class="well col-sm-6" style="word-break:break-all; word-wrap:break-word;">
                <div>
                    <span class="label"><?php echo JText::_('COM_REDCORE_WEBSERVICE_CLIENT_LABEL'); ?>:</span>
                    <span class="label label-default"><?php echo $this->item->webservice_client; ?></span>
                </div>
                <div>
                    <span class="label"><?php echo JText::_('COM_REDCORE_WEBSERVICE_NAME_LABEL'); ?>:</span>
                    <span class="label label-default"><?php echo $this->item->webservice_name; ?></span>
                </div>
                <div>
                    <span class="label"><?php echo JText::_('COM_REDCORE_WEBSERVICE_VERSION_LABEL'); ?>:</span>
                    <span class="label label-default"><?php echo $this->item->webservice_version; ?></span>
                </div>
                <div>
                    <span class="label"><?php echo JText::_('COM_REDCORE_WEBSERVICE_HISTORY_LOG_OPERATION_LABEL'); ?>:</span>
                    <span class="label label-default"><?php echo $this->item->operation; ?></span>
                </div>
                <div>
                    <span class="label"><?php echo JText::_('COM_REDCORE_WEBSERVICE_HISTORY_LOG_METHOD_LABEL'); ?>:</span>
                    <span class="label label-default"><?php echo $this->item->method; ?></span>
                </div>
                <div>
                    <span class="label"><?php echo JText::_('COM_REDCORE_WEBSERVICE_HISTORY_LOG_STATUS_LABEL'); ?>:</span>
                    <span class="label <?php
                    switch ($this->item->status):
	                    case JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_HISTORY_LOG_SUCCESS'):
		                    echo 'label-success';
		                    break;
	                    case JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_HISTORY_LOG_FAILED'):
		                    echo 'label-danger';
		                    break;
	                    default:
		                    echo 'label-warning';
		                    break;
                    endswitch;
                    ?>"><?php echo $this->item->status; ?></span>
                </div>
            </div>
            <div class="well col-sm-6" style="word-break:break-all; word-wrap:break-word;">
                <div>
                    <span class="label"><?php echo JText::_('COM_REDCORE_WEBSERVICE_HISTORY_LOG_EXECUTION_TIME_LABEL'); ?>:</span>
                    <span class="label label-default"><?php echo $this->item->execution_time; ?></span>
                </div>
                <div>
                    <span class="label"><?php echo JText::_('COM_REDCORE_WEBSERVICE_HISTORY_LOG_EXECUTION_MEMORY_LABEL'); ?>:</span>
                    <span class="label label-default"><?php echo $this->item->execution_memory; ?></span>
                </div>
                <div>
                    <span class="label"><?php echo JText::_('COM_REDCORE_WEBSERVICE_HISTORY_LOG_AUTHENTICATION_LABEL'); ?>:</span>
                    <span class="label label-default"><?php echo $this->item->authentication; ?></span>
                </div>
                <div>
                    <span class="label"><?php echo JText::_('COM_REDCORE_WEBSERVICE_HISTORY_LOG_AUTHENTICATION_USER_LABEL'); ?>:</span>
                    <span class="label label-default"><?php echo $this->item->authentication_user; ?></span>
                </div>
                <div>
                    <span class="label"><?php echo JText::_('COM_REDCORE_WEBSERVICE_HISTORY_LOG_CREATED_DATE_LABEL'); ?>:</span>
                    <span class="label label-default"><?php echo $this->item->created_date; ?></span>
                </div>
                <div>
                    <span class="label"><?php echo JText::_('COM_REDCORE_WEBSERVICE_HISTORY_LOG_USING_SOAP_LABEL'); ?>:</span>
                    <span class="label label-default"><?php echo $this->item->using_soap ? JText::_('JYES') : JText::_('JNO'); ?></span>
                </div>
            </div>
            <div class="well col-sm-12" style="word-break:break-all; word-wrap:break-word;">
                <span class="label"><?php echo JText::_('COM_REDCORE_WEBSERVICE_HISTORY_LOG_URL_LABEL'); ?>:</span>
                <span class="label label-default"><?php echo $this->item->url; ?></span>
            </div>
            <div class="well col-sm-12" style="word-break:break-all; word-wrap:break-word;">
                <span class="label"><?php echo JText::_('COM_REDCORE_WEBSERVICE_HISTORY_LOG_MESSAGES_LABEL'); ?>:</span>
                <?php if (!empty($this->item->messages)) :
                    $this->item->messages = json_decode($this->item->messages);
                    ?>
                    <?php foreach ($this->item->messages as $message): ?>
                    <span class="label  <?php
                    switch ($message->type):
                        case 'success':
                            echo 'label-success';
                            break;
                        case 'error':
                            echo 'label-danger';
                            break;
                        default:
                            echo 'label-warning';
                            break;
                    endswitch;
                    ?>">
                                    <?php echo $message->message; ?>
                                </span>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="well col-sm-12" style="word-break:break-all; word-wrap:break-word;">
                <span class="label"><?php echo JText::_('COM_REDCORE_WEBSERVICE_HISTORY_LOG_FILE_LABEL'); ?>:</span>
                <span class="label label-default"><?php echo $this->item->file_name; ?></span>
                <br /><br />
                <pre><?php echo file_get_contents(JPATH_ROOT . '/' . $this->item->file_name); ?></pre>
            </div>
        </div>
	</div>

	<!-- hidden fields -->
	<?php echo $this->form->getInput('id'); ?>
	<input type="hidden" name="option" value="com_redcore">
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>">
	<input type="hidden" name="task" value="">
	<?php echo JHTML::_('form.token'); ?>
</form>
