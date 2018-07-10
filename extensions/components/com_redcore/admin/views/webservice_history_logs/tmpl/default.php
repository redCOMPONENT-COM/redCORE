<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2018 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
$listOrder          = $this->state->get('list.ordering');
$listDirn           = $this->state->get('list.direction');
$action             = JRoute::_('index.php?option=com_redcore&view=webservice_history_logs');
$averageMemoryUsage = memory_get_usage() + 5000000;
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("[data-target='#webservicePreview']").click(function (e) {
            e.preventDefault();
            var url      = jQuery(this).attr("data-remote");
            var format   = jQuery(this).attr("data-remote-format");
            var dataType = format == "json" ? "json" : "text";

            jQuery.get(url, null, function (data) {
                if (format == "json") {
                    data = syntaxHighlight(data);
                    data = jQuery("<pre></pre>").html(data);
                }
                else if (format == "doc") {
                    data = jQuery(data).contents();
                }
                else if (format == "text") {
                    data = jQuery("<pre></pre>").html(data);
                }

                jQuery("#webservicePreview .modal-body").html(data);
                jQuery("#webservicePreview").modal("show");
                jQuery("#webservicePreview").data("url", url);
            }, dataType);

        });
    });

    function syntaxHighlight(json) {
        if (typeof json != "string") {
            json = JSON.stringify(json, undefined, 4);
        }
        json = json.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
            function (match) {
                var cls = "number";
                if (/^"/.test(match)) {
                    if (/:$/.test(match)) {
                        cls = "key";
                    } else {
                        cls = "string";
                    }
                } else if (/true|false/.test(match)) {
                    cls = "boolean";
                } else if (/null/.test(match)) {
                    cls = "null";
                }
                return "<span class='" + cls + "'>" + match + "</span>";
            });
    }

    function submitAction(task, form) {
        if (typeof Joomla.submitform == "function") {
            Joomla.submitform(task, form);
        }
        else {
            if (typeof(task) !== "undefined" && task !== "") {
                document.getElementById("adminFormWebserviceHistoryLog").task.value = task;
            }

            // Submit the form.
            if (typeof form.onsubmit == "function") {
                form.onsubmit();
            }
            if (typeof form.fireEvent == "function") {
                form.fireEvent("submit");
            }
            form.submit();
        }
    }

    function printDocumentation() {
        url        = jQuery("#webservicePreview").data("url") + "&print";
        var iframe = jQuery("#redcore-print");
        iframe.attr("src", url);
    }
</script>
<style>
    pre {
        outline: 1px solid #ccc;
        padding: 5px;
        margin: 5px;
    }

    .string {
        color: green;
    }

    .number {
        color: darkorange;
    }

    .boolean {
        color: blue;
    }

    .null {
        color: magenta;
    }

    .key {
        color: red;
    }

    .modal.large {
        width: 80%;
        margin-left: -40%;
    }
    #webserviceHistoryLogList >tbody>tr>td{
        padding:1px;
        border-top: 0;
        font-size: 12px;
    }
    #webserviceHistoryLogList .label, #webserviceHistoryLogList .btn
    {
        font-size: 11px;
    }
    .adminFormWebserviceHistoryLog .modal-body
    {
        max-height: none;
    }
</style>
<form action="<?php echo $action; ?>" id="adminForm" method="post" name="adminForm" autocomplete="off" class="adminForm adminFormWebserviceHistoryLog form-validate form-horizontal">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view'    => $this,
			'options' => array(
				'filtersHidden'       => false,
				'searchField'         => 'search_webservice_history_logs',
				'searchFieldSelector' => '#filter_search_webservice_history_logs',
				'limitFieldSelector'  => '#list_webservice_history_logs_limit',
				'activeOrder'         => $listOrder,
				'activeDirection'     => $listDirn
			)
		)
	);
	?>
    <hr/>
    <div class="modal fade"
         id="webservicePreview"
         tabindex="-1"
         role="dialog"
         aria-labelledby="webservicePreview"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_PREVIEW') ?></h4>
                </div>
                <div class="modal-body">
                    <pre></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" onclick="printDocumentation();"><i class="icon-print"></i></button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo JText::_('JTOOLBAR_CLOSE') ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <table class="table table-striped table-hover table-condensed table-sm" id="webserviceHistoryLogList">
            <thead>
            <tr>
                <th class="hidden-xs" width="1">
                    <input type="checkbox" name="checkall-toggle" value=""
                           title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
                </th>
                <th class="nowrap center" width="20">
                    <?php echo JHtml::_('rsearchtools.sort', 'JSTATUS', 'whl.status', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap text-center">
                    <?php echo JHtml::_('rsearchtools.sort', 'JGLOBAL_TITLE', 'whl.webservice_name', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap">
                    <?php echo JHtml::_('rsearchtools.sort', 'COM_REDCORE_WEBSERVICE_HISTORY_LOGS_OPERATION_LABEL', 'whl.operation', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap">
                    <?php echo JHtml::_('rsearchtools.sort', 'COM_REDCORE_WEBSERVICE_HISTORY_LOGS_EXECUTION_TIME_LABEL', 'whl.execution_time', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap">
                    <?php echo JHtml::_('rsearchtools.sort', 'COM_REDCORE_WEBSERVICE_HISTORY_LOGS_EXECUTION_MEMORY_LABEL', 'whl.execution_memory', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap">
		            <?php echo JHtml::_('rsearchtools.sort', 'COM_REDCORE_WEBSERVICE_HISTORY_LOGS_MESSAGES_LABEL', 'whl.messages', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap">
		            <?php echo JHtml::_('rsearchtools.sort', 'COM_REDCORE_WEBSERVICE_HISTORY_LOGS_AUTHENTICATION_USER_LABEL', 'whl.authentication_user', $listDirn, $listOrder); ?>
                </th>
                <th class="nowrap">
		            <?php echo JHtml::_('rsearchtools.sort', 'COM_REDCORE_WEBSERVICE_HISTORY_LOGS_CREATED_DATE_LABEL', 'whl.created_date', $listDirn, $listOrder); ?>
                </th>
            </tr>
            </thead>
            <?php if ($this->items): ?>
                <tbody>
                <?php foreach ($this->items as $i => $item): ?>
                    <tr>
                        <td>
                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td>
	                        <label class="label <?php
                            switch ($item->status):
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

                            ?>"><?php echo $item->status; ?></label>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button
                                        class="btn btn-default btn-xs"
                                        type="button"
                                        data-remote-format="text"
                                        data-remote="index.php?option=com_redcore&task=webservice_history_log.getFileData&id=<?php echo $item->id; ?>&<?php echo JSession::getFormToken(); ?>=1"
                                        data-target="#webservicePreview">
                                    <i class="glyphicon glyphicon-log-out"></i>
			                        <?php echo JText::_('COM_REDCORE_WEBSERVICE_HISTORY_LOGS_GET_RESPONSE_LABEL') ?>
                                </button>
                            </div>
                            <?php if ($item->using_soap) : ?>
                                <span class="pull-right label label-warning"><?php echo JText::_('COM_REDCORE_WEBSERVICE_HISTORY_LOGS_USING_SOAP') ?></span>
                            <?php endif; ?>
                            <a href="<?php echo JRoute::_('index.php?option=com_redcore&task=webservice_history_log.edit&id=' . $item->id); ?>">
                                <?php echo $item->webservice_name . ' ' . $item->webservice_version . ' (' . $item->webservice_client . ')'; ?>
                            </a>
                        </td>
                        <td><?php echo $item->operation; ?> (<?php echo $item->method; ?>)</td>
                        <td>
                            <span class="label label-<?php echo $item->execution_time < 5 ? 'info' : 'danger' ?>"><?php echo date('H:i:s', strtotime('00:00:00 +'. $item->execution_time. ' seconds')); ?></span>
                        </td>
                        <td>
                            <span class="label label-<?php echo $item->execution_memory < $averageMemoryUsage ? 'info' : 'danger' ?>"><?php echo RFilesystemFile::getReadableFileSize($item->execution_memory); ?></span>
                        </td>
                        <td>
		                    <?php if (!empty($item->messages)) :
			                    $item->messages = json_decode($item->messages);
                                ?>
				                    <?php foreach ($item->messages as $message): ?>
                                    <label class="label  <?php
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
                                    </label>
				                    <?php endforeach; ?>
		                    <?php endif; ?>
                        </td>
                        <td><?php echo $item->authentication_user; ?></td>
                        <td><?php echo $item->created_date; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            <?php endif; ?>
        </table>
        <?php echo $this->pagination->getListFooter(); ?>
    </div>
    <div>
        <input type="hidden" name="return" value="<?php echo $this->return; ?>"/>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0">
	    <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<div style="display: none;">
    <iframe id="redcore-print"></iframe>
</div>
