<?php
/**
 * @package     Redcore.Webservice
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHtml::_('rbootstrap.tooltip');

$view = !empty($displayData['view']) ? $displayData['view'] : null;
$xml = !empty($displayData['options']['xml']) ? $displayData['options']['xml'] : array();
$soapEnabled = $displayData['options']['soapEnabled'];
$print = $displayData['options']['print'];
$halLink = RApiHalHelper::buildWebserviceFullUrl($view->client, $view->webserviceName, $view->webserviceVersion, 'hal');
$docsLink = RApiHalHelper::buildWebserviceFullUrl($view->client, $view->webserviceName, $view->webserviceVersion, 'hal', 'doc');
$translationFallback = RBootstrap::getConfig('enable_translation_fallback_webservices', '1') == '1' ? JText::_('JENABLED') : JText::_('JDISABLED');
$defaultLanguage = RTranslationHelper::getSiteLanguage();
$defaultFormat = RBootstrap::getConfig('webservices_default_format', 'json');
$defaultStatefulness = RBootstrap::getConfig('webservices_stateful', '1') == '1' ? JText::_('JENABLED') : JText::_('JDISABLED');
$languages = JLanguageHelper::getLanguages();
$availableLanguages = array();

foreach ($languages as $language)
{
	$availableLanguages[] = $language->sef . ' <em>(' . $language->title . ')</em>';
}

$availableLanguages = implode(', ', $availableLanguages);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<link type="text/css" href="<?php echo JUri::root(true) . '/media/redcore/css/component.bs3.min.css' ?>" rel="stylesheet" />
	<link type="text/javascript" href="<?php echo JUri::root(true) . '/media/redcore/js/lib/bootstrap3/js/bootstrap.min.js' ?>" />
	<?php if ($print) :?>
		<style type="text/css">
			.table-nonfluid {
				width: auto !important;
			}
		</style>

		<script type="text/javascript">
			function printWindow()
			{
				window.print();
			}
		</script>
	<?php endif; ?>
</head>
<body<?php if ($print) : ?> onload="printWindow()"<?php endif; ?> class="redcore">
<div class="container-fluid">
	<?php if (empty($xml)) : ?>
		<h1><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_NONE'); ?></h1>
	<?php else : ?>
		<h1><?php echo $xml->name; ?> (<?php echo JText::_('JVERSION'); ?> <?php echo $xml->config->version; ?>)</h1>
		<table class="table table-striped table-hover table-nonfluid">
			<?php if (isset($xml->author)) : ?>
				<tr>
					<th><?php echo JText::_('JAUTHOR'); ?></th>
					<td><?php echo (string) $xml->author; ?></td>
				</tr>
			<?php endif; ?>
			<?php if (isset($xml->copyright)) : ?>
				<tr>
					<th><?php echo JText::_('LIB_REDCORE_COPYRIGHT'); ?></th>
					<td><?php echo (string) $xml->copyright; ?></td>
				</tr>
			<?php endif; ?>
			<tr>
				<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_GENERATED'); ?></th>
				<td><?php echo JHtml::_('date', new JDate, JText::_('DATE_FORMAT_LC2')); ?></td>
			</tr>
			<tr>
				<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_SUPPORTED_FORMATS'); ?></th>
				<td>
					json<?php echo $defaultFormat == 'json' ? ' (' . JText::_('JDEFAULT') . ')' : ''; ?>
					, xml<?php echo $defaultFormat == 'xml' ? ' (' . JText::_('JDEFAULT') . ')' : ''; ?>
				</td>
			</tr>
			<tr>
				<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_CLIENT'); ?></th>
				<td><?php echo ucfirst($view->client); ?></td>
			</tr>
			<tr>
				<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_ACCESS_OPTION'); ?></th>
				<td><?php echo (string) $xml->config->name; ?> (com_<?php echo (string) $xml->config->name; ?>)</td>
			</tr>
			<tr>
				<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_ACCESS_URL'); ?></th>
				<td>
					<small>
						<a href="<?php echo $halLink ?>">
							<?php echo $halLink ?>
						</a>
					</small>
				</td>
			</tr>
			<?php if ($soapEnabled) :
				$wsdlLink = RApiHalHelper::buildWebserviceFullUrl($view->client, $view->webserviceName, $view->webserviceVersion, 'soap') . '&wsdl';
			?>
				<tr>
					<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_WSDL_ACCESS_URL'); ?></th>
					<td>
						<small>
							<a href="<?php echo $wsdlLink ?>">
								<?php echo $wsdlLink ?>
							</a>
						</small>
					</td>
				</tr>
			<?php endif; ?>
				<tr>
					<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_DOCUMENTATION_URL'); ?></th>
					<td>
						<small>
							<a href="<?php echo $docsLink ?>">
								<?php echo $docsLink ?>
							</a>
						</small>
					</td>
				</tr>
		</table>
		<?php if (isset($xml->description)) : ?>
		<div class="well">
			<h4><?php echo JText::_('JGLOBAL_DESCRIPTION'); ?></h4>
			<p><?php echo (string) $xml->description; ?></p>
		</div>
		<?php endif; ?>

		<h2><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_ALLOWED_OPERATIONS'); ?></h2>
		<p>
			<?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_ALLOWED_OPERATIONS_DESC'); ?>
			<div class="before">
				<nav role='navigation' class='table-of-contents'>
					<ul>
						<?php foreach ($xml->operations as $operations) : ?>
							<?php foreach ($operations as $operationName => $operation) : ?>
								<?php if ($operationName == 'documentation') :
									continue;
								elseif ($operationName == 'read') :
									if (isset($xml->operations->read->list)) : ?>
										<li>
											<a href="#<?php echo JFilterOutput::stringURLSafe(strtolower($operationName . ' list')); ?>"><?php echo ucfirst($operationName . ' list'); ?></a>
										</li>
									<?php endif;

									if (isset($xml->operations->read->item)) : ?>
										<li>
											<a href="#<?php echo JFilterOutput::stringURLSafe(strtolower($operationName . ' item')); ?>"><?php echo ucfirst($operationName . ' item'); ?></a>
										</li>
									<?php endif;
								elseif ($operationName == 'task') :
									foreach ($operation as $taskName => $task) : ?>
										<li>
											<a href="#<?php echo JFilterOutput::stringURLSafe(strtolower($operationName . ' ' . $taskName)); ?>"><?php echo ucfirst($operationName . ' ' . $taskName); ?></a>
										</li>
									<?php endforeach;
								else : ?>
									<li>
										<a href="#<?php echo JFilterOutput::stringURLSafe(strtolower($operationName)); ?>"><?php echo ucfirst($operationName); ?></a>
									</li>
								<?php endif; ?>
								
							<?php endforeach; ?>
						<?php endforeach; ?>
					</ul>
				</nav>
			</div>
		</p>

		<br />
		<h3><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_REQUEST_HEADER_OPTIONS'); ?></h3>
		<p>
			<?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_REQUEST_HEADER_OPTIONS_DESC'); ?>
		</p>
		<table class="table table-striped table-hover table-nonfluid">
			<tr>
				<th>Accept</th>
				<td><?php echo JText::sprintf(
						'LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_REQUEST_HEADER_OPTIONS_OUTPUT_FORMAT',
						'<strong>application/hal+' . $defaultFormat . '</strong>',
						'application/hal+json, application/hal+xml, application/hal+doc'
					); ?></td>
			</tr>
			<tr>
				<th>Accept-Language</th>
				<td><?php echo JText::sprintf(
						'LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_REQUEST_HEADER_OPTIONS_ACCEPT_LANGUAGE',
						'<strong>' . $defaultLanguage . '</strong>',
						$availableLanguages
					); ?></td>
			</tr>
            <tr>
                <th>Accept-Encoding</th>
                <td><?php echo JText::sprintf(
						'LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_REQUEST_HEADER_OPTIONS_ACCEPT_ENCODING',
						'<strong>gzip</strong>'
					); ?></td>
            </tr>
            <tr>
                <th>Content-Encoding</th>
                <td><?php echo JText::sprintf(
						'LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_REQUEST_HEADER_OPTIONS_CONTENT_ENCODING',
						'<strong>gzip</strong>'
					); ?></td>
            </tr>
			<tr>
				<th>X-Webservice-Stateful</th>
				<td><?php echo JText::sprintf(
						'LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_REQUEST_HEADER_OPTIONS_STATEFUL',
						'<strong>' . $defaultStatefulness . '</strong>'
					); ?></td>
			</tr>
			<tr>
				<th>X-Webservice-Translation-Fallback</th>
				<td><?php echo JText::sprintf(
						'LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_REQUEST_HEADER_OPTIONS_TRANSLATION_FALLBACK',
						'<strong>' . $translationFallback . '</strong>'
					); ?></td>
			</tr>
		</table>

		<?php foreach ($xml->operations as $operations) : ?>
			<?php foreach ($operations as $operationName => $operation) : ?>
					<?php if ($operationName == 'documentation') :
						continue;
					elseif ($operationName == 'read') :
						if (isset($xml->operations->read->list)) : ?>
							<a name="<?php echo $operationName . 'list'; ?>"></a>
							<?php echo RLayoutHelper::render(
								'webservice.documentationoperation',
								array(
									'view' => $view,
									'options' => array (
										'xml' => $xml,
										'operationXml' => $operation->list,
										'operationName' => $operationName . ' ' . 'list',
										'soapEnabled' => $soapEnabled,
									)
								)
							);?>
							<br />
						<?php endif;

						if (isset($xml->operations->read->item)) : ?>
							<a name="<?php echo $operationName . 'item'; ?>"></a>
							<?php echo RLayoutHelper::render(
								'webservice.documentationoperation',
								array(
									'view' => $view,
									'options' => array (
										'xml' => $xml,
										'operationXml' => $operation->item,
										'operationName' => $operationName . ' ' . 'item',
										'soapEnabled' => $soapEnabled,
									)
								)
							);?>
							<br />
						<?php endif;
					elseif ($operationName == 'task') :
						foreach ($operation as $taskName => $task) : ?>
							<a name="<?php echo $operationName . $taskName; ?>"></a>
							<?php echo RLayoutHelper::render(
								'webservice.documentationoperation',
								array(
									'view' => $view,
									'options' => array (
										'xml' => $xml,
										'operationXml'  => $task,
										'operationName' => $operationName . ' ' . $taskName,
										'taskName'      => $taskName,
										'soapEnabled' => $soapEnabled,
									)
								)
							);?>
							<br />
						<?php endforeach;
					else : ?>
						<a name="<?php echo $operationName; ?>"></a>
							<?php echo RLayoutHelper::render(
								'webservice.documentationoperation',
								array(
									'view' => $view,
									'options' => array (
										'xml' => $xml,
										'operationXml' => $operation,
										'operationName' => $operationName,
										'soapEnabled' => $soapEnabled,
									)
								)
							);?>
						<br />
					<?php endif; ?>
			<?php endforeach; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
</body>
</html>
