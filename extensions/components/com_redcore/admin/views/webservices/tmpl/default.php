<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$action    = JRoute::_('index.php?option=com_redcore&view=webservices');
?>
<script type="text/javascript">
    function setWebservice(client, webservice, version, folder, task)
    {
        document.getElementById("client").value     = client;
        document.getElementById("webservice").value = webservice;
        document.getElementById("version").value    = version;
        document.getElementById("folder").value     = folder;

        if (task != "") {
            if (task == "webservices.delete") {
                if (confirm('<?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_UNINSTALL_CONFIRM', true); ?>'))
                    submitAction(task, document.getElementById("adminForm"));
            }
            else if (task == "webservices.deleteWebservice") {
                if (confirm('<?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_DELETE_CONFIRM', true); ?>'))
                    submitAction(task, document.getElementById("adminForm"));
            }
            else {
                submitAction(task, document.getElementById("adminForm"));
            }
        }
    }

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
                document.getElementById("adminForm").task.value = task;
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
</style>
<form action="<?php echo $action; ?>" id="adminForm" method="post" name="adminForm" autocomplete="off" class="adminForm form-validate form-horizontal"
      enctype="multipart/form-data">
	<?php
	echo RLayoutHelper::render(
		'searchtools.default',
		array(
			'view'    => $this,
			'options' => array(
				'filtersHidden'       => false,
				'searchField'         => 'search_webservices',
				'searchFieldSelector' => '#filter_search_webservices',
				'limitFieldSelector'  => '#list_webservices_limit',
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
    <ul class="nav nav-tabs" id="mainTabs">
        <li role="presentation" class="active">
            <a href="#mainComponentWebservices" data-toggle="tab"><?php echo JText::_('COM_REDCORE_WEBSERVICES_INSTALLED_WEBSERVICES'); ?></a>
        </li>
        <li role="presentation">
            <a href="#mainComponentWebservicesXmls" data-toggle="tab" class="lc-not_installed_webservices">
				<?php echo JText::_('COM_REDCORE_WEBSERVICES_AVAILABLE_WEBSERVICES'); ?> <span
                        class="badge"><?php echo $this->xmlFilesAvailable; ?></span>
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active in" id="mainComponentWebservices">
            <p class="tab-description"><?php echo JText::_('COM_REDCORE_WEBSERVICES_DESC'); ?></p>

            <div class="row-fluid">
                <table class="table table-striped table-hover" id="oauthClientsList">
                    <thead>
                    <tr>
                        <th class="hidden-xs" width="1">
                            <input type="checkbox" name="checkall-toggle" value=""
                                   title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
                        </th>
                        <th class="nowrap center" width="20">
							<?php echo JHtml::_('rsearchtools.sort', 'JSTATUS', 'w.state', $listDirn, $listOrder); ?>
                        </th>
                        <th class="nowrap" width="30%">
							<?php echo JHtml::_('rsearchtools.sort', 'JGLOBAL_TITLE', 'w.title', $listDirn, $listOrder); ?>
                        </th>
                        <th width="20">
							<?php echo JText::_('COM_REDCORE_WEBSERVICE_VERSION_LABEL'); ?>
                        </th>
                        <th width="50">
							<?php echo JHtml::_('rsearchtools.sort', 'COM_REDCORE_WEBSERVICE_CLIENT_LABEL', 'w.client', $listDirn, $listOrder); ?>
                        </th>
                        <th width="20%">
							<?php echo JHtml::_('rsearchtools.sort', 'COM_REDCORE_WEBSERVICES_PATH_LABEL', 'w.path', $listDirn, $listOrder); ?>
                        </th>
                        <th class="nowrap hidden-xs">
							<?php echo JHtml::_('rsearchtools.sort', 'COM_REDCORE_WEBSERVICES_WEBSERVICE_AVAILABLE_SCOPES', 'w.scopes', $listDirn, $listOrder); ?>
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
									<?php echo JHtml::_('rgrid.published', $item->state, $i, 'webservices.', $canChange = true, 'cb'); ?>
                                </td>
                                <td>
                                    <a href="<?php echo JRoute::_('index.php?option=com_redcore&task=webservice.edit&id=' . $item->id); ?>">
                                        <strong><?php echo $item->title; ?></strong>
                                    </a>
                                    <br/>
									<?php if ($item->xml): ?>
										<?php $webserviceClientUri = '&webserviceClient=' . $item->client; ?>
                                        <em><?php echo $item->xml->description; ?></em>
                                        <br/>
                                        <div class="btn-group pull-right">
                                            <button
                                                    class="btn btn-xs btn-primary"
                                                    type="button"
												<?php if (RApiHalHelper::isAttributeTrue($item->xml->operations->documentation, 'authorizationNeeded', true)) : ?>
                                                    disabled="disabled"
												<?php endif; ?>
                                                    data-remote-format="doc"
                                                    data-remote="../index.php?api=Hal&format=doc&option=<?php echo $item->xml->config->name . $webserviceClientUri . '&webserviceVersion=' . $item->xml->config->version; ?>"
                                                    data-target="#webservicePreview">
                                                <i class="icon-file-text"></i>
												<?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_DOCUMENTATION') ?>
                                            </button>
                                            <button
                                                    class="btn btn-xs btn-primary"
                                                    type="button"
												<?php if (RApiHalHelper::isAttributeTrue($item->xml->operations->read, 'authorizationNeeded', true)) : ?>
                                                    disabled="disabled"
												<?php endif; ?>
                                                    data-remote-format="json"
                                                    data-remote="../index.php?api=Hal&option=<?php echo $item->xml->config->name . $webserviceClientUri . '&webserviceVersion=' . $item->xml->config->version; ?>"
                                                    data-target="#webservicePreview">
                                                <i class="icon-file-text"></i>
												<?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_PREVIEW_JSON') ?>
                                            </button>
                                        </div>
									<?php endif; ?>
                                </td>
                                <td><strong><?php echo $item->version; ?></strong></td>
                                <td>
                                    <span class="label label-<?php echo $item->client == 'site' ? 'info' : 'danger' ?>"><?php echo JText::_('J' . $item->client); ?></span>
                                </td>
                                <td style="word-break:break-all; word-wrap:break-word;">
									<?php $folder = !empty($item->path) ? '/' . $item->path : ''; ?>
									<?php echo RApiHalHelper::getWebservicesRelativePath(); ?>
                                    <strong><?php echo $folder ?>/<span class="lc-webservice-file"><?php echo $item->xmlFile; ?></span></strong>
									<?php if (!JFile::exists(RApiHalHelper::getWebservicesPath() . $folder . '/' . $item->xmlFile)) : ?>
                                        <div class="label label-danger pull-right"><?php echo JText::_('COM_REDCORE_WEBSERVICES_XML_MISSING'); ?></div>
									<?php elseif ($item->xmlHashed != md5($item->xml)) : ?>
                                        <div class="label label-warning pull-right"><?php echo JText::_('COM_REDCORE_WEBSERVICES_XML_CHANGED'); ?></div>
									<?php else : ?>
                                        <div class="label label-success pull-right"><?php echo JText::_('COM_REDCORE_WEBSERVICES_XML_VALID'); ?></div>
									<?php endif; ?>
                                </td>
                                <td>
									<?php if (!empty($item->scopes)) : ?>
                                        <ol class="breadcrumb">
											<?php foreach ($item->scopes as $scope): ?>
                                                <li><?php echo $scope['scopeDisplayName']; ?></li>
											<?php endforeach; ?>
                                        </ol>
									<?php endif; ?>
                                </td>
                            </tr>
						<?php endforeach; ?>
                        </tbody>
					<?php endif; ?>
                </table>
				<?php echo $this->pagination->getListFooter(); ?>
            </div>

        </div>
        <div class="tab-pane" id="mainComponentWebservicesXmls">
			<?php if (empty($this->xmlFiles)): ?>
                <br/>
                <div class="alert alert-info">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <div class="pagination-centered">
                        <h3><?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_NO_FILES_AVAILABLE') ?></h3>
                    </div>
                </div>
			<?php else : ?>
                <div class="row-fluid">
                    <div class="col-md-6 well" style="margin-top: 15px;">
                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_TITLE'); ?></label>
                            </div>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="file" multiple="multiple" name="redcoreWebservice[]"
                                           id="redcoreWebservice" accept="application/xml" class="form-control"
                                           aria-describedby="redcoreWebservice-upload-addon"/>
                                    <div class="input-group-btn">
                                        <button
                                                class="btn btn-success"
                                                id="redcoreWebservice-upload-addon"
                                                type="button"
                                                onclick="setWebservice('', '', '', '', 'webservices.uploadWebservice')">
                                            <i class="icon-upload"></i>
											<?php echo JText::_('JTOOLBAR_UPLOAD') ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <div class="col-md-4">
                                <label><?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_ALL'); ?></label>
                            </div>
                            <div class="col-md-8">
                                <button
                                        class="btn btn-success lc-install_all_webservices"
                                        type="button"
                                        onclick="setWebservice('', 'all', '', '', 'webservices.installWebservice')">
                                    <i class="icon-cogs"></i>
									<?php echo JText::_('JTOOLBAR_INSTALL'); ?>
                                </button>
                                <button
                                        class="btn btn-danger"
                                        type="button"
                                        onclick="setWebservice('', 'all', '', '', 'webservices.deleteWebservice')">
                                    <i class="icon-remove"></i>
									<?php echo JText::_('JTOOLBAR_DELETE') ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
				<?php if (empty($this->xmlFiles)): ?>
                    <div class="alert alert-info">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <div class="pagination-centered">
                            <h3><?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_NO_FILES_AVAILABLE') ?></h3>
                        </div>
                    </div>
				<?php else : ?>

					<?php foreach ($this->xmlFiles as $clients => $webserviceNames): ?>
						<?php $column = 0; ?>
                        <div class='clearfix'></div>
                        <div class="container-fluid">
                            <h3 class="text-primary"><?php echo JText::_('J' . $clients); ?></h3>
                            <hr/>
                        </div>
						<?php foreach ($webserviceNames as $webserviceVersions):
							foreach ($webserviceVersions as $webservice):
								$webserviceClient = RApiHalHelper::getWebserviceClient($webservice);
								?>
                                <div class="col-md-4">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3><?php echo $webservice->name ?></h3>
                                            <p><?php echo $webservice->config->name; ?>
                                                &nbsp;<?php echo !empty($webservice->config->version) ? '<span class="label">' . $webservice->config->version . '</span>' : ''; ?></p>
                                        </div>
                                        <div class="panel-body">
                                            <table class="table table-striped adminlist">
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo JText::_('JAUTHOR'); ?>:</strong>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo !empty($webservice->author) ? $webservice->author : ''; ?></strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo JText::_('COM_REDCORE_WEBSERVICE_PATH_LABEL'); ?>:</strong>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo $webservice->webservicePath ?></strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo JText::_('JGLOBAL_DESCRIPTION'); ?>:</strong>
                                                    </td>
                                                    <td>
														<?php echo !empty($webservice->description) ? $webservice->description : '' ?>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <button
                                                    class="btn btn-xs btn-success"
                                                    type="button"
                                                    onclick="setWebservice('<?php echo $webserviceClient; ?>', '<?php echo $webservice->config->name; ?>', '<?php echo $webservice->config->version; ?>', '<?php echo $webservice->webservicePath; ?>', 'webservices.installWebservice')">
                                                <i class="icon-cogs"></i>
												<?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_INSTALL_XML') ?>
                                            </button>
                                            <button
                                                    class="btn btn-xs btn-danger"
                                                    type="button"
                                                    onclick="setWebservice('<?php echo $webserviceClient; ?>', '<?php echo $webservice->config->name; ?>', '<?php echo $webservice->config->version; ?>', '<?php echo $webservice->webservicePath; ?>', 'webservices.deleteWebservice')">
                                                <i class="icon-remove"></i>
												<?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_DELETE_XML') ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
								<?php if ((++$column) % 3 == 0) : ?>
                                </div>
                                <div class="row">
							<?php endif; ?>
							<?php endforeach; ?>
						<?php endforeach; ?>
					<?php endforeach; ?>
				<?php endif; ?>
                </div>
			<?php endif; ?>
            <div class="clearfix"></div>
        </div>
    </div>
    <div>
        <input type="hidden" name="return" value="<?php echo $this->return; ?>"/>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="client" id="client" value=""/>
        <input type="hidden" name="webservice" id="webservice" value=""/>
        <input type="hidden" name="version" id="version" value=""/>
        <input type="hidden" name="folder" id="folder" value=""/>
        <input type="hidden" name="boxchecked" value="0">
    </div>
	<?php echo JHtml::_('form.token'); ?>
</form>
<div style="display: none;">
    <iframe id="redcore-print"></iframe>
</div>
