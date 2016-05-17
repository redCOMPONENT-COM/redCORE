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
$operationXml = !empty($displayData['options']['operationXml']) ? $displayData['options']['operationXml'] : array();
$operationName = !empty($displayData['options']['operationName']) ? $displayData['options']['operationName'] : '';
$isOperationRead = $operationName == 'read list' || $operationName == 'read item';
$view->resetDocumentResources();
$resources = $view->loadResourceFromConfiguration($operationXml);
$authorizationNotNeeded = (isset($operationXml['authorizationNeeded']) && strtolower($operationXml['authorizationNeeded']) == 'false');
?>
<div class="container-fluid">
	<div class="page-header">
		<h3 id="<?php echo JFilterOutput::stringURLSafe($operationName); ?>">
			<span class="label label-info"><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_OPERATION') ?></span>
			<?php if (!$authorizationNotNeeded) : ?>
				<span class="label label-warning"><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_AUTHORIZATION_NEEDED') ?></span>
			<?php endif; ?>
			<?php echo ucfirst($operationName); ?>
		</h3>
		<?php if (!empty($operationXml->description)) : ?>
			<p><?php echo $operationXml->description ?></p>
		<?php endif; ?>
	</div>

	<?php if (!empty($operationXml['useOperation'])): ?>
		<?php echo JText::sprintf('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_USE_OPERATION',
			'<a href="#' . $operationXml['useOperation'] . '">' . $operationXml['useOperation'] . '</a>'); ?>
	<?php else : ?>
		<?php if (!empty($resources)) : ?>
			<?php if (!empty($operationXml->resources->description)) : ?>
				<p><?php echo $operationXml->resources->description ?></p>
			<?php endif; ?>
			<?php foreach ($resources as $resourceGroupName => $resourceGroup) : ?>
				<?php
					$i = 0;

					foreach ($resourceGroup as $resName => $res)
					{
						$resourceGroup[$resName]['original_order'] = $i;
						$i++;
					}

					usort($resourceGroup, array($view, "sortResourcesByDisplayGroup"));
					$currentDisplayGroup = '--';
				?>
				<h4><?php echo $resourceGroupName == 'rcwsGlobal' ? JText::_('JDEFAULT') : ucfirst($resourceGroupName); ?>
					 <?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_RESOURCES'); ?></h4>

				<div class="container-fluid">
					<?php
						foreach ($resourceGroup as $resource) :
							unset($resource['original_order']);
					?>
						<?php if ($currentDisplayGroup != $resource['displayGroup']) : ?>
							<?php if ($currentDisplayGroup != '--') : ?>
								</table>
							<?php endif; ?>
							<?php $currentDisplayGroup = $resource['displayGroup']; ?>
							<h4><?php echo $currentDisplayGroup == '' ? JText::_('JDEFAULT') : $currentDisplayGroup; ?></h4>
							<table class="table table-striped table-hover">
								<thead>
								<tr>
									<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_RESOURCE_NAME'); ?></th>
									<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_RESOURCE_FORMAT'); ?></th>
									<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_RESOURCE_TRANSFORM'); ?></th>
									<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_RESOURCE_PARAMETERS'); ?></th>
									<th><?php echo JText::_('JGLOBAL_DESCRIPTION'); ?></th>
								</tr>
								</thead>
						<?php endif; ?>
						<tr>
							<td><?php echo $view->assignGlobalValueToResource($resource['displayName']); ?></td>
							<td><?php echo $view->assignGlobalValueToResource($resource['fieldFormat']); ?></td>
							<td><?php echo !empty($resource['transform']) ? $resource['transform'] : 'string'; ?></td>
							<td>
								<?php foreach ($resource as $resourceKey => $resourceValue) : ?>
									<?php if (!empty($resourceValue)
										&& !in_array($resourceKey, array('displayName', 'fieldFormat', 'transform', 'resourceSpecific', 'displayGroup', 'description'))) : ?>
										<strong><?php echo $resourceKey; ?>: </strong> <?php echo $view->assignGlobalValueToResource($resourceValue); ?>
										<br />
									<?php endif; ?>
								<?php endforeach; ?>
							</td>
							<td><?php echo $resource['description']; ?></td>
						</tr>
					<?php endforeach; ?>
					</table>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php if (!empty($operationXml->fields)) : ?>
			<h4><span class="label label-warning"><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_PARAMETERS'); ?></span> <?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELDS'); ?></h4>
			<?php if (!empty($operationXml->fields->description)) : ?>
				<p><?php echo $operationXml->fields->description ?></p>
			<?php endif; ?>
			<table class="table table-striped table-hover">
				<thead>
				<tr>
					<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_NAME'); ?></th>
					<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_TRANSFORM'); ?></th>
					<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_DEFAULT_VALUE'); ?></th>
					<?php if ($isOperationRead) : ?>
						<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_FILTER'); ?></th>
						<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_SEARCHABLE'); ?></th>
					<?php else: ?>
						<th><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_REQUIRED'); ?></th>
					<?php endif; ?>
					<th><?php echo JText::_('JGLOBAL_DESCRIPTION'); ?></th>
				</tr>
				</thead>
				<?php foreach ($operationXml->fields as $fields) : ?>
					<?php foreach ($fields as $fieldKey => $field) : ?>
						<?php if ($fieldKey != 'field') : ?>
							<?php continue; ?>
						<?php endif; ?>
						<tr>
							<td><?php echo $field['name']; ?></td>
							<td><?php echo !empty($field['transform']) ? $field['transform'] : 'string'; ?></td>
							<td><?php echo !empty($field['defaultValue']) ? $field['defaultValue'] : ''; ?></td>
							<?php if ($isOperationRead) : ?>
								<td><?php echo RApiHalHelper::isAttributeTrue($field, 'isFilterField') ? JText::_('JYES') : JText::_('JNO'); ?></td>
								<td><?php echo RApiHalHelper::isAttributeTrue($field, 'isSearchableField') ? JText::_('JYES') : JText::_('JNO'); ?></td>
							<?php else: ?>
								<td><?php echo RApiHalHelper::isAttributeTrue($field, 'isRequiredField') ? JText::_('JYES') : JText::_('JNO'); ?></td>
							<?php endif; ?>
							<td><?php echo !empty($field->description) ? $field->description : ''; ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</table>
		<?php endif; ?>

		<?php echo $this->sublayout('example', $displayData); ?>
	<?php endif; ?>
</div>
