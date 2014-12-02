<?php
/**
 * @package     Redcore.Webservice
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

$webservices = !empty($displayData['webservices']) ? $displayData['webservices'] : array();
$missingWebservices = !empty($displayData['missingWebservices']) ? $displayData['missingWebservices'] : array();
$column = 0;
?>
<script type="text/javascript">
	function setWebservice(client, webservice, version, folder, task)
	{
		document.getElementById('client').value = client;
		document.getElementById('webservice').value = webservice;
		document.getElementById('version').value = version;
		document.getElementById('folder').value = folder;

		if (task != '')
		{
			if (task == 'webservices.uninstallWebservice')
			{
				if (confirm('<?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_UNINSTALL_CONFIRM', true); ?>'))
					submitAction(task, document.getElementById('adminForm'));
			}
			else if (task == 'webservices.deleteWebservice')
			{
				if (confirm('<?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_DELETE_CONFIRM', true); ?>'))
					submitAction(task, document.getElementById('adminForm'));
			}
			else
			{
				submitAction(task, document.getElementById('adminForm'));
			}
		}
	}
	jQuery(document).ready(function () {
		jQuery('[data-target="#webservicePreview"]').click(function(e) {
			e.preventDefault();
			var url = jQuery(this).attr('data-remote');
			var format = jQuery(this).attr('data-remote-format');
			var dataType = format == 'json' ? 'json' : 'text';

			jQuery.get(url, null, function(data){
				if (format == 'xml')
				{
					data = formatXml(data)
						.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/ /g, '&nbsp;').replace(/\n/g,'');
					data = jQuery('<pre></pre>').html(data);
				}
				else if (format == 'json')
				{
					data = syntaxHighlight(data);
					data = jQuery('<pre></pre>').html(data);
				}
				else if (format == 'doc')
				{
					data = jQuery(data).contents();
				}

				jQuery('#webservicePreview .modal-body').html(data);
				jQuery('#webservicePreview').modal('show');
			}, dataType);

		});
	});

	function syntaxHighlight(json) {
		if (typeof json != 'string') {
			json = JSON.stringify(json, undefined, 4);
		}
		json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
		return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
			var cls = 'number';
			if (/^"/.test(match)) {
				if (/:$/.test(match)) {
					cls = 'key';
				} else {
					cls = 'string';
				}
			} else if (/true|false/.test(match)) {
				cls = 'boolean';
			} else if (/null/.test(match)) {
				cls = 'null';
			}
			return '<span class="' + cls + '">' + match + '</span>';
		});
	}

	function formatXml(xml) {
		var formatted = '';
		var reg = /(>)\s*(<)(\/*)/g;
		xml = xml.replace(reg, '$1\r\n$2$3');
		var pad = 0;
		jQuery.each(xml.split('\r\n'), function(index, node) {
			var indent = 0;
			if (node.match( /.+<\/\w[^>]*>$/ )) {
				indent = 0;
			} else if (node.match( /^<\/\w/ )) {
				if (pad != 0) {
					pad -= 1;
				}
			} else if (node.match( /^<\w[^>]*[^\/]>.*$/ )) {
				indent = 1;
			} else {
				indent = 0;
			}

			var padding = '';
			for (var i = 0; i < pad; i++) {
				padding += '  ';
			}

			formatted += padding + node + '\r\n';
			pad += indent;
		});

		return formatted;
	}

	function submitAction(task, form)
	{
		if (typeof Joomla.submitform == 'function')
		{
			Joomla.submitform(task, form);
		}
		else
		{
			if (typeof(task) !== 'undefined' && task !== "") {
				document.getElementById('adminForm').task.value = task;
			}

			// Submit the form.
			if (typeof form.onsubmit == 'function') {
				form.onsubmit();
			}
			if (typeof form.fireEvent == "function") {
				form.fireEvent('submit');
			}
			form.submit();
		}
	}
</script>
<style>
	pre {outline: 1px solid #ccc; padding: 5px; margin: 5px; }
	.string { color: green; }
	.number { color: darkorange; }
	.boolean { color: blue; }
	.null { color: magenta; }
	.key { color: red; }
	.modal.large {
		width: 80%;
		margin-left:-40%;
	}
</style>
<div class="tab-pane" id="mainComponentWebservices">
	<p class="tab-description"><?php echo JText::_('COM_REDCORE_WEBSERVICES_DESC'); ?></p>
	<p class="tab-description">
		<?php echo JText::_('COM_REDCORE_WEBSERVICES_PLUGIN_LABEL'); ?>
		<?php if (RTranslationHelper::$pluginParams->get('enable_webservices', 0) == 1) : ?>
			<span class="badge badge-success"><?php echo JText::_('JENABLED'); ?></span>
		<?php else : ?>
			<span class="badge badge-important"><?php echo JText::_('JDISABLED'); ?></span>
		<?php endif; ?>
	</p>
	<div class="row">
		<div class="col-md-6 well">
			<div class="form-group">
				<div class="control-label">
					<?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_TITLE'); ?>
				</div>
				<div class="controls">
					<input type="file" multiple="multiple" name="redcoreWebservice[]" id="redcoreWebservice" accept="application/xml" class="inputbox" />
					<button
						class="btn btn-success"
						type="button"
						onclick="setWebservice('', '', '', '', 'webservices.uploadWebservice')">
						<i class="icon-upload"></i>
						<?php echo JText::_('JTOOLBAR_UPLOAD') ?>
					</button>
				</div>
			</div>
			<div class="form-group" style="margin-top:40px;margin-bottom: 0;">
				<div class="control-label">
					<?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_ALL'); ?>
				</div>
				<div class="controls">
					<button
						class="btn btn-success"
						type="button"
						onclick="setWebservice('', 'all', '', '', 'webservices.installWebservice')">
						<i class="icon-cogs"></i>
						<?php echo JText::_('JTOOLBAR_INSTALL') . ' / ' . JText::_('COM_REDCORE_UPDATE'); ?>
					</button>
					<button
						class="btn btn-danger"
						type="button"
						onclick="setWebservice('', 'all', '', '', 'webservices.uninstallWebservice')">
						<i class="icon-cogs"></i>
						<?php echo JText::_('JTOOLBAR_UNINSTALL') ?>
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
	<div class="modal fade large"
	     id="webservicePreview"
	     tabindex="-1"
	     role="dialog"
	     aria-labelledby="webservicePreview"
	     aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_PREVIEW') ?></h4>
				</div>
				<div class="modal-body"><pre></pre>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo JText::_('JTOOLBAR_CLOSE') ?></button>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<?php if (empty($webservices)): ?>
			<div class="alert alert-info">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<div class="pagination-centered">
					<h3><?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_NO_FILES_AVAILABLE') ?></h3>
				</div>
			</div>
		<?php else : ?>

		<?php foreach ($webservices as $clients => $webserviceNames): ?>
		<div class='clearfix'></div>
		<h3><?php echo JText::_('J' . $clients); ?></h3>
		<?php foreach ($webserviceNames as $webserviceVersions):
		foreach ($webserviceVersions as $webservice):
		$webserviceClient = RApiHalHelper::getWebserviceClient($webservice);
		$status = RApiHalHelper::getStatus($webserviceClient, (string) $webservice->config->name, (string) $webservice->config->version);
		$webserviceClientUri = '&webserviceClient=' . $webserviceClient;
		$methods = RApiHalHelper::getMethods($webserviceClient, (string) $webservice->config->name, (string) $webservice->config->version);
		$scopes = RApiHalHelper::getScopes(
			$webserviceClient, (string) $webservice->config->name, (string) $webservice->config->version, $getScopeDisplayNames = true
		);
		$webserviceRead = !empty($webservice->operations->read) && strtolower($webservice->operations->read['authorizationNeeded']) == 'false';
		$webserviceDocumentation = !empty($webservice->operations->documentation['authorizationNeeded'])
			&& strtolower($webservice->operations->documentation['authorizationNeeded']) == 'false';
		?>
		<div class="col-md-4 well">
			<h4>
				<?php echo $webservice->name; ?> (<?php echo $webservice->config->name; ?>)
			</h4>
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
						<strong><?php echo JText::_('JVERSION'); ?>:</strong>
					</td>
					<td>
						<strong><?php echo !empty($webservice->config->version) ? $webservice->config->version : ''; ?></strong>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php echo JText::_('JGLOBAL_DESCRIPTION'); ?>:</strong>
					</td>
					<td>
						<strong><?php echo !empty($webservice->description) ? $webservice->description : ''; ?></strong>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php echo JText::_('JSTATUS'); ?>:</strong>
					</td>
					<td>
						<strong><?php echo $status; ?></strong>
					</td>
				</tr>
				<?php if ($status != JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_NOT_INSTALLED')) :?>
					<tr>
						<td>
							<strong><?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_METHODS'); ?>:</strong>
						</td>
						<td>
							<strong><?php echo str_replace(',', ', ', $methods); ?></strong>
						</td>
					</tr>
					<tr>
						<td>
							<strong><?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_AVAILABLE_SCOPES'); ?>:</strong>
						</td>
						<td>
							<ul>
								<li>
									<?php echo str_replace(',', '</li><li>', $scopes); ?>
								</li>
							</ul>
						</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>
			<?php if ($status == JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_NOT_INSTALLED')): ?>
				<button
					class="btn btn-xs btn-success"
					type="button"
					onclick="setWebservice('<?php echo $webserviceClient; ?>', '<?php echo $webservice->config->name; ?>', '<?php echo $webservice->config->version; ?>', '<?php echo $webservice->webservicePath; ?>', 'webservices.installWebservice')">
					<i class="icon-cogs"></i>
					<?php echo JText::_('JTOOLBAR_INSTALL') ?>
				</button>
				<?php $disabled = ' disabled="disabled" '; ?>
			<?php else: ?>
				<button
					class="btn btn-xs btn-primary"
					type="button"
					<?php if (!$webserviceDocumentation) : ?>
						disabled="disabled"
					<?php endif; ?>
					data-remote-format="doc"
					data-remote="../index.php?api=Hal&format=doc&option=<?php echo $webservice->config->name . $webserviceClientUri; ?>"
					data-target="#webservicePreview">
					<i class="icon-file-text"></i>
					<?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_DOCUMENTATION') ?>
				</button>
				<button
					class="btn btn-xs btn-primary"
					type="button"
					<?php if (!$webserviceRead) : ?>
						disabled="disabled"
					<?php endif; ?>
					data-remote-format="json"
					data-remote="../index.php?api=Hal&option=<?php echo $webservice->config->name . $webserviceClientUri; ?>"
					data-target="#webservicePreview">
					<i class="icon-file-text"></i>
					<?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_PREVIEW_JSON') ?>
				</button>
				<button
					class="btn btn-xs btn-primary"
					type="button"
					<?php if (!$webserviceRead) : ?>
						disabled="disabled"
					<?php endif; ?>
					data-remote-format="xml"
					data-remote="../index.php?api=Hal&format=xml&option=<?php echo $webservice->config->name . $webserviceClientUri; ?>"
					data-target="#webservicePreview">
					<i class="icon-file-text"></i>
					<?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_PREVIEW_XML') ?>
				</button>
				<button
					class="btn btn-xs btn-primary"
					type="button"
					onclick="setWebservice('<?php echo $webserviceClient; ?>', '<?php echo $webservice->config->name; ?>', '<?php echo $webservice->config->version; ?>', '<?php echo $webservice->webservicePath; ?>', 'webservices.installWebservice')">
					<i class="icon-cogs"></i>
					<?php echo JText::_('COM_REDCORE_UPDATE') ?>
				</button>
				<button
					class="btn btn-xs btn-danger"
					type="button"
					onclick="setWebservice('<?php echo $webserviceClient; ?>', '<?php echo $webservice->config->name; ?>', '<?php echo $webservice->config->version; ?>', '<?php echo $webservice->webservicePath; ?>', 'webservices.uninstallWebservice')">
					<i class="icon-cogs"></i>
					<?php echo JText::_('JTOOLBAR_UNINSTALL') ?>
				</button>
				<?php $disabled = ''; ?>
			<?php endif; ?>
			<button
				class="btn btn-xs btn-danger"
				type="button"
				onclick="setWebservice('<?php echo $webserviceClient; ?>', '<?php echo $webservice->config->name; ?>', '<?php echo $webservice->config->version; ?>', '<?php echo $webservice->webservicePath; ?>', 'webservices.deleteWebservice')">
				<i class="icon-remove"></i>
				<?php echo JText::_('JTOOLBAR_DELETE') ?>
			</button>
		</div>
		<?php if ((++$column) % 3 == 0 ) : ?>
	</div>
	<div class="row">
		<?php endif; ?>
		<?php endforeach; ?>
		<?php endforeach; ?>
		<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<div class="row">
		<?php if (!empty($missingWebservices)): ?>
		<?php foreach ($missingWebservices as $missingWebservice): ?>
		<div class="col-md-4 well">
			<h4>
				<?php echo $missingWebservice; ?>
			</h4>
			<table class="table table-striped adminlist">
				<tbody>
				<tr>
					<td>
						<strong><?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE'); ?>:</strong>
					</td>
					<td>
						<strong><?php echo !empty($missingWebservice->name) ? $missingWebservice->name : ''; ?></strong>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php echo JText::_('JVERSION'); ?>:</strong>
					</td>
					<td>
						<strong><?php echo !empty($missingWebservice->version) ? implode(', ', $missingWebservice->version) : ''; ?></strong>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?php echo JText::_('JSTATUS'); ?>:</strong>
					</td>
					<td>
						<strong><?php echo JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_MISSING_XML_FILE'); ?></strong>
					</td>
				</tr>
				</tbody>
			</table>
			<button
				class="btn btn-xs btn-danger"
				type="button"
				onclick="setWebservice('', <?php echo $missingWebservice->name; ?>', '<?php echo $missingWebservice->version; ?>', '<?php echo $missingWebservice->path; ?>', 'webservices.uninstallWebservice')">
				<i class="icon-cogs"></i>
				<?php echo JText::_('JTOOLBAR_UNINSTALL') ?>
			</button>
		</div>
		<?php if ((++$column) % 3 == 0 ) : ?>
	</div>
	<div class="row">
		<?php endif; ?>
		<?php endforeach; ?>
		<?php endif; ?>
		<div class="clearfix"></div>
	</div>
</div>
