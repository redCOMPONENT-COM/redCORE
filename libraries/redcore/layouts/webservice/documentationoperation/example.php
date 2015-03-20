<?php
/**
 * @package     Redcore.Webservice
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$view = !empty($displayData['view']) ? $displayData['view'] : null;
$soapEnabled = $displayData['options']['soapEnabled'];
$operationXml = !empty($displayData['options']['operationXml']) ? $displayData['options']['operationXml'] : array();
$operationName = !empty($displayData['options']['operationName']) ? $displayData['options']['operationName'] : '';
$authorizationNotNeeded = (isset($operationXml['authorizationNeeded']) && strtolower($operationXml['authorizationNeeded']) == 'false');
$url = RApiHalHelper::buildWebserviceFullUrl($view->client, $view->webserviceName, $view->webserviceVersion);
$view->resetDocumentResources();
$resources = $view->loadResourceFromConfiguration($operationXml);
$method = 'GET';
$soapFunction = '';
$taskName = '';
$noteName = '';
$currentDisplayGroup = '';
$errorList = array();

if (!empty($displayData['options']['taskName']) )
{
	$taskName = $displayData['options']['taskName'];
	$operationName = 'task';
}

switch ($operationName)
{
	case 'create' :
		$soapFunction = 'create($data = array())';
		$method = 'POST';
		$errorList = array(201, 400, 405, 406, 500);
		break;
	case 'read list' :
		$soapFunction = 'readList($limitStart = 0, $limit = 20, $filterSearch = null, $filters = array(), $ordering = null, $orderingDirection = null)';
		$method = 'GET';
		$noteName = '_LIST';
		$errorList = array(200, 405, 500);
		break;
	case 'read item' :
		$soapFunction = 'readItem($id = array())';
		$method = 'GET';
		$noteName = '_ITEM';
		$errorList = array(200, 404, 405, 500);

		// If there are more than one primary key involved we are displaying them all
		$primaryKeysFromFields = $view->getPrimaryKeysFromFields($operationXml);

		foreach ($primaryKeysFromFields as $primaryKey => $primaryKeyField)
		{
			$url .= '&' . $primaryKey . '=1';
		}
		break;
	case 'update' :
		$soapFunction = 'update($data = array())';
		$method = 'PUT';
		$errorList = array(200, 400, 405, 406, 500);
		break;
	case 'delete' :
		$soapFunction = 'delete($data = array())';
		$method = 'DELETE';
		$errorList = array(200, 400, 405, 406, 500);
		break;
	case 'task' :
		$soapFunction = 'task_' . $taskName . '($data = array())';
		$method = 'GET / POST';
		$errorList = array(200, 400, 405, 406, 500);
		break;
}

if (!$authorizationNotNeeded):
	$errorList[] = 401;
	sort($errorList);
endif;
?>
<div>
	<h4>
		<span class="label label-success">
			<?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_EXAMPLE_USAGE'); ?>
		</span>
		<?php if (!$authorizationNotNeeded) : ?>
			 &nbsp;- <span class="label label-warning"><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_AUTHORIZATION_NEEDED') ?></span>
		<?php endif; ?>
	</h4>

	<strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_BASIC'); ?></strong><br />
	<h5>
		<span class="label label-success">
			<?php echo $method; ?>
		</span> &nbsp;<?php echo $url . ($operationName == 'task' ? '&task=' . $taskName : '') . '&api=hal'; ?>
	</h5>
	<em><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_BASIC' . $noteName . '_NOTE'); ?></em><br /><br />

	<?php if ($soapEnabled): ?>
		<strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_SOAP'); ?></strong><br />
		<h5>
			<span class="label label-success">
				$client-><?php echo $soapFunction; ?>
			</span> &nbsp;<?php echo $url . '&api=soap'; ?>
		</h5>
		<em><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_SOAP' . $noteName . '_NOTE'); ?></em><br /><br />
	<?php endif; ?>

	<div class="row">
		<div class="col-xs-4 col-md-4 well" style="border: 2px solid #fff;">
			<h5 style="border-bottom: 1px solid #ddd"><strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_REQUEST'); ?></strong></h5>
			<small>
				<?php if ($operationName == 'read item' && empty($operationXml->fields)):
					echo '<strong>id</strong> (<em>int, '
						. JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_REQUIRED') . '</em>)';
				endif;

				if (!empty($operationXml->fields)) :
					foreach ($operationXml->fields as $fields) :
						$fieldsContainer = array();

						if ($operationName == 'read list'):
							$fieldsContainer[] = '<strong>list[limitstart]</strong> (<em>int, '
								. JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_OPTIONAL') . '</em>)';
							$fieldsContainer[] = '<strong>list[limit]</strong> (<em>int, '
								. JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_OPTIONAL') . '</em>)';
							$fieldsContainer[] = '<strong>filter[search]</strong> (<em>string, '
								. JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_OPTIONAL') . '</em>)';
							$fieldsContainer[] = '<strong>filter[order]</strong> (<em>string, '
								. JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_OPTIONAL') . '</em>)';
							$fieldsContainer[] = '<strong>filter[order_Dir]</strong> (<em>string, '
								. JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_OPTIONAL') . '</em>)';
						endif;

						if ($operationName == 'read item'):
							$primaryKeys = $view->getPrimaryFields($operationXml);

							if (empty($primaryKeys)):
								echo '<strong>id</strong> (<em>int, '
									. JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_REQUIRED') . '</em>)';
							endif;
						endif;

						foreach ($fields as $fieldKey => $field) :
							if ($fieldKey != 'field') :
								continue;
							endif;
							if ($operationName == 'read list'):
								if (RApiHalHelper::isAttributeTrue($field, 'isFilterField')):
									$field['name'] = 'filter[' . $field['name'] . ']';
								else:
									continue;
								endif;
							endif;

							if ($operationName == 'read item'):
								if (!RApiHalHelper::isAttributeTrue($field, 'isPrimaryField')):
									continue;
								endif;
							endif;

							$fieldsContainer[] = '<strong>' . $field['name'] . '</strong> '
								. '(<em>' . (!empty($field['transform']) ? $field['transform'] : 'string') . ', '
								. (RApiHalHelper::isAttributeTrue($field, 'isRequiredField') ?
								JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_REQUIRED')
								: JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_FIELD_OPTIONAL')) . '</em>)';
						endforeach;
						echo implode(',<br />', $fieldsContainer);
					endforeach;
				endif; ?>
			</small>
		</div>
		<div class="col-xs-4 col-md-4 well" style="border: 2px solid #fff;">
			<h5 style="border-bottom: 1px solid #ddd"><strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_RESPONSE'); ?></strong></h5>
			<small>
				<?php if (!empty($resources)) :
					$output = array();
					foreach ($resources as $resourceGroupName => $resourceGroup) :
						$resourcesForDisplay = array();
						$resourceGroupName = $resourceGroupName == 'listItem' ? '_embedded{ item: [' : $resourceGroupName;
						foreach ($resourceGroup as $resName => $resValue):
							$resourcesForDisplay[$resValue['displayGroup']][$resName] = $resValue;
						endforeach;

						$outputOuter = $resourceGroupName == 'rcwsGlobal' ? '' : '<div><strong>' . ucfirst($resourceGroupName) . ' {</strong></div>';
						$outputGroup = array();
						foreach ($resourcesForDisplay as $currentDisplayGroup => $resourceDisplayGroup) :
							$outputGroupRow = $currentDisplayGroup == '' ? '' : '<div><strong>' . $currentDisplayGroup . ' {</strong></div>';
							$resourceContainer = array();
							foreach ($resourceDisplayGroup as $resourceName => $resource) :
								$resourceContainer[] = '<strong>' . $view->assignGlobalValueToResource($resource['displayName']) . '</strong>'
									. ' (<em>' . (!empty($resource['transform']) ? $resource['transform'] : 'string') . '</em>)';
							endforeach;
							$outputGroupRow .= implode(', ', $resourceContainer);
							$outputGroupRow .= $currentDisplayGroup == '' ? '' : '<br /><strong>}</strong>';
							$outputGroup[] = $outputGroupRow;
						endforeach;
						$outputOuter .= implode(', <br />', $outputGroup);
						$outputOuter .= $resourceGroupName == '_embedded{ item: [' ? '<br /><strong>}]</strong>' : '';
						$outputOuter .= $resourceGroupName == 'rcwsGlobal' ? '' : '<br /><strong>}</strong>';
						$output[] = $outputOuter;
					endforeach;

				echo implode(', <br />', $output);
				endif; ?>
			</small>
		</div>
		<div class="col-xs-4 col-md-4 well" style="border: 2px solid #fff;">
			<h5 style="border-bottom: 1px solid #ddd"><strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_HEADERS'); ?></strong></h5>
			<small>
				<?php if (!empty($errorList)) : ?>
					<?php foreach ($errorList as $error) : ?>
						<?php echo '<strong>' . $error . ':</strong> ' . RApiBase::$statusTexts[$error]; ?><br />
					<?php endforeach; ?>
				<?php endif; ?>
			</small>
		</div>
	</div>
</div>
