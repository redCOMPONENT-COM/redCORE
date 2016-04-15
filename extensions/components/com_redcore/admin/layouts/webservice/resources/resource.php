<?php
/**
 * @package     Redcore.Webservice
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$view = $displayData['view'];
$model = isset($displayData['model']) ? $displayData['model'] : $view->getModel();

$operation = !empty($displayData['options']['operation']) ? $displayData['options']['operation'] : 'read';
$fieldList = !empty($displayData['options']['fieldList']) ? $displayData['options']['fieldList'] : '';
$displayName = !empty($displayData['options']['form']['displayName']) ? $displayData['options']['form']['displayName'] : '';
$transform = !empty($displayData['options']['form']['transform']) ? $displayData['options']['form']['transform'] : 'string';
$resourceSpecific = !empty($displayData['options']['form']['resourceSpecific']) ? $displayData['options']['form']['resourceSpecific'] : 'rcwsGlobal';
$displayGroup = !empty($displayData['options']['form']['displayGroup']) ? $displayData['options']['form']['displayGroup'] : '';
$fieldFormat = !empty($displayData['options']['form']['fieldFormat']) ? $displayData['options']['form']['fieldFormat'] : '';
$linkTitle = !empty($displayData['options']['form']['linkTitle']) ? $displayData['options']['form']['linkTitle'] : '';
$linkName = !empty($displayData['options']['form']['linkName']) ? $displayData['options']['form']['linkName'] : '';
$hrefLang = !empty($displayData['options']['form']['hrefLang']) ? $displayData['options']['form']['hrefLang'] : '';
$linkRel = !empty($displayData['options']['form']['linkRel']) ? $displayData['options']['form']['linkRel'] : '';
$linkTemplated = !empty($displayData['options']['form']['linkTemplated']) ? $displayData['options']['form']['linkTemplated'] : 'false';
$description = !empty($displayData['options']['form']['description']) ? $displayData['options']['form']['description'] : '';
$displayData['options']['form'] = !empty($displayData['options']['form']) ? $displayData['options']['form'] : array();
$resourceAttributes = array('displayName', 'transform', 'resourceSpecific', 'displayGroup', 'fieldFormat', 'linkTitle', 'linkName',
	'hrefLang', 'linkRel', 'linkTemplated', 'description');

if ($displayGroup == '_links')
{
	$fieldList = 'link';
}

if ($fieldList == 'link')
{
	$displayGroup = '_links';
}

$id = RFilesystemFile::getUniqueName($operation);
?>
<div class="row row-stripped">
	<div class="col-xs-2">
		<button type="button" class="btn btn-default btn-xs btn-primary ws-row-display fields-edit-row">
			<i class="icon-edit"></i>
			<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_EDIT_LABEL'); ?>
		</button>
		<button type="button" class="btn btn-default btn-xs btn-success ws-row-edit fields-apply-row" style="display: none;">
			<i class="icon-save"></i>
			<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_APPLY_LABEL'); ?>
		</button>
		<button type="button" class="btn btn-default btn-xs btn-danger fields-remove-row">
			<i class="icon-minus"></i>
			<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_REMOVE_LABEL'); ?>
		</button>
		<input type="hidden" class="ws-row-original" name="jform[<?php echo $operation;?>][resources][resource][]"
		       value="<?php echo $this->escape(json_encode($displayData['options']['form'])); ?>" />
	</div>
	<div class="col-xs-2 ws-row-display-cell-displayName">
		<?php echo $displayName;?>
	</div>
	<div class="col-xs-1 ws-row-display-cell-displayGroup">
		<?php echo $displayGroup;?>
	</div>
	<div class="col-xs-4 ws-row-display-cell-fieldFormat">
		<?php echo $fieldFormat;?>
	</div>
	<div class="col-xs-2">
		<?php foreach ($resourceAttributes as $resourceAttribute) : ?>
			<?php if (!in_array($resourceAttribute, array('displayName', 'displayGroup', 'fieldFormat', 'description'))) : ?>
				<?php $attributeValue = !empty($displayData['options']['form'][$resourceAttribute]) ? $displayData['options']['form'][$resourceAttribute] : ''; ?>
				<span <?php echo empty($attributeValue) ? 'style="display:none;"' : ''?>>
					<strong><?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_' . $resourceAttribute . '_LABEL'); ?>: </strong>
					<span class="ws-row-display-cell-<?php echo $resourceAttribute; ?>"><?php echo $attributeValue; ?></span>
					<br />
				</span>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<div class="col-xs-1 ws-row-display-cell-description">
		<?php echo $description;?>
	</div>
	<div class="col-xs-10 col-xs-offset-2 ws-row-edit" style="display: none;">
		<div class="form-horizontal">
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_DISPLAYNAME_DESCRIPTION'); ?>">
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_DISPLAYNAME_LABEL'); ?>
				</div>
				<input type="text" name="displayName" value="<?php echo $displayName;?>" class="form-control" />
			</div>
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_WEBSERVICE_FIELD_TRANSFORM_DESCRIPTION'); ?>">
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_TRANSFORM_LABEL'); ?>
				</div>
				<?php echo JHtml::_(
					'select.genericlist',
					$model->getTransformTypes($operation),
					'transform',
					' class="required" ',
					'value',
					'text',
					$transform
				); ?>
			</div>
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_FIELDFORMAT_DESCRIPTION'); ?>">
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_FIELDFORMAT_LABEL'); ?>
				</div>
				<input type="text" name="fieldFormat" value="<?php echo $fieldFormat;?>" class="form-control" />
			</div>
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_DISPLAYGROUP_DESCRIPTION'); ?>">
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_DISPLAYGROUP_LABEL'); ?>
				</div>
				<input type="text" name="displayGroup" value="<?php echo $displayGroup;?>" class="form-control" />
			</div>

			<?php if ($fieldList == 'link') : ?>
				<div class="input-group input-group-sm">
					<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_LINKTITLE_DESCRIPTION'); ?>">
						<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_LINKTITLE_LABEL'); ?>
					</div>
					<input type="text" name="linkTitle" value="<?php echo $linkTitle;?>" class="form-control" />
				</div>
				<div class="input-group input-group-sm">
					<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_LINKNAME_DESCRIPTION'); ?>">
						<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_LINKNAME_LABEL'); ?>
					</div>
					<input type="text" name="linkName" value="<?php echo $linkName;?>" class="form-control" />
				</div>
				<div class="input-group input-group-sm">
					<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_HREFLANG_DESCRIPTION'); ?>">
						<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_HREFLANG_LABEL'); ?>
					</div>
					<input type="text" name="hrefLang" value="<?php echo $hrefLang;?>" class="form-control" />
				</div>
				<div class="input-group input-group-sm">
					<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_LINKTEMPLATED_DESCRIPTION'); ?>">
						<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_LINKTEMPLATED_LABEL'); ?>
					</div>
					<fieldset class="radio btn-group">
						<input id="<?php echo $id;?>_linkTemplated0" type="radio" name="<?php echo $id;?>_linkTemplated"
						       value="true" <?php echo $linkTemplated == 'false' ? '' : ' checked="checked" '; ?> />
						<label for="<?php echo $id;?>_linkTemplated0" class="btn btn-default"><?php echo JText::_('JYES'); ?></label>
						<input id="<?php echo $id;?>_linkTemplated1" type="radio" name="<?php echo $id;?>_linkTemplated"
						       value="false" <?php echo $linkTemplated == 'false' ? ' checked="checked" ' : ''; ?> />
						<label for="<?php echo $id;?>_linkTemplated1" class="btn btn-default"><?php echo JText::_('JNO'); ?></label>
					</fieldset>
				</div>
				<div class="input-group input-group-sm">
					<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_LINKREL_DESCRIPTION'); ?>">
						<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_LINKREL_LABEL'); ?>
					</div>
					<input type="text" name="linkRel" value="<?php echo $linkRel;?>" class="form-control" />
				</div>
			<?php endif; ?>

			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_RESOURCESPECIFIC_DESCRIPTION'); ?>">
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_RESOURCE_RESOURCESPECIFIC_LABEL'); ?>
				</div>
				<input type="text" name="resourceSpecific"
				       value="<?php echo $resourceSpecific;?>" class="form-control" />
			</div>
		</div>
		<div class="input-group input-group-sm">
			<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_WEBSERVICE_DESCRIPTION_RESOURCE_DESCRIPTION'); ?>">
				<?php echo JText::_('COM_REDCORE_WEBSERVICE_DESCRIPTION_LABEL'); ?>
			</div>
			<input type="text" name="description" value="<?php echo $description;?>" class="form-control" />
		</div>
	</div>
</div>
