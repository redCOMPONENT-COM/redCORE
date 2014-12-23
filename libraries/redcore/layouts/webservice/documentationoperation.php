<?php
/**
 * @package     Redcore.Webservice
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
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

?>
<div class="container-fluid">
	<h3><?php echo ucfirst($operationName); ?></h3>
	<?php if (!empty($operationXml->description)) : ?>
		<p><?php echo $operationXml->description ?></p>
	<?php endif; ?>

	<?php if (!empty($operationXml['useOperation'])): ?>
		<?php echo JText::sprintf('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_USE_OPERATION',
			'<a href="#' . $operationXml['useOperation'] . '">' . $operationXml['useOperation'] . '</a>'); ?>
	<?php else : ?>
		<?php if (!empty($resources)) : ?>
			<?php if (!empty($operationXml->resources->description)) : ?>
				<p><?php echo $operationXml->resources->description ?></p>
			<?php endif; ?>
			<?php foreach ($resources as $resourceGroupName => $resourceGroup) : ?>
				<?php usort($resourceGroup, array($view, "sortResourcesByDisplayGroup"));
				$currentDisplayGroup = '--'; ?>
				<h4><?php echo $resourceGroupName == 'rcwsGlobal' ? JText::_('JDEFAULT') : ucfirst($resourceGroupName); ?>
					 <?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_RESOURCES'); ?></h4>

				<div class="container-fluid">
					<?php foreach ($resourceGroup as $resource) : ?>
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
							<td><?php echo $resource['displayName']; ?></td>
							<td><?php echo $resource['fieldFormat']; ?></td>
							<td><?php echo !empty($resource['transform']) ? $resource['transform'] : 'string'; ?></td>
							<td>
								<?php foreach ($resource as $resourceKey => $resourceValue) : ?>
									<?php if (!empty($resourceValue)
										&& !in_array($resourceKey, array('displayName', 'fieldFormat', 'transform', 'resourceSpecific', 'displayGroup', 'description'))) : ?>
										<strong><?php echo $resourceKey; ?>: </strong> <?php echo $resourceValue; ?>
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
			<h4><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELDS'); ?></h4>
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
	<?php endif; ?>
</div>
