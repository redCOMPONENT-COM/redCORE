<?php
/**
 * @package     Redcore.Webservice
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

extract($displayData);

$column = $options['column'];

$columnTypes = RedcoreHelpersTranslation::getTranslationColumnTypes();
$column->column_type = empty($column->column_type) ? RTranslationTable::COLUMN_TRANSLATE : $column->column_type;

$valueTypes = RedcoreHelpersTranslation::getTranslationValueTypes();
$column->value_type = empty($column->value_type) ? 'text' : $column->value_type;

$filterTypes = RedcoreHelpersTranslation::getTranslationFilterTypes();
$column->filter = empty($column->filter) ? 'RAW' : $column->filter;
$id = RFilesystemFile::getUniqueName();
?>
<div class="row row-stripped">
	<div class="col-xs-1">
		<button type="button" class="btn btn-default btn-xs btn-danger columns-remove-row">
			<i class="icon-minus"></i>
			<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_REMOVE_LABEL'); ?>
		</button>
	</div>
	<div class="col-xs-11 ws-row-edit">
		<div class="form-horizontal">
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_NAME_DESC'); ?>">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_NAME'); ?>
				</div>
				<input type="text" name="jform[columns][<?php echo $id;?>][name]" value="<?php echo $column->name;?>" class="form-control" />
			</div>
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_TITLE_DESC'); ?>">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_TITLE'); ?>
				</div>
				<input type="text" name="jform[columns][<?php echo $id;?>][title]" value="<?php echo $column->title;?>" class="form-control" />
			</div>
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_COLUMN_TYPE_DESC'); ?>">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_COLUMN_TYPE'); ?>
				</div>
				<?php echo JHtml::_(
					'select.genericlist',
					$columnTypes,
					'jform[columns][' . $id . '][column_type]',
					' class="required" ',
					'value',
					'text',
					$column->column_type
				); ?>
			</div>
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_VALUE_TYPE_DESC'); ?>">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_VALUE_TYPE'); ?>
				</div>
				<?php echo JHtml::_(
					'select.genericlist',
					$valueTypes,
					'jform[columns][' . $id . '][value_type]',
					' class="required" ',
					'value',
					'text',
					$column->value_type
				); ?>
			</div>
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_FALLBACK_DESC'); ?>">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_FALLBACK'); ?>
				</div>
				<fieldset class="radio btn-group">
					<input id="<?php echo $id;?>_fallback1" type="radio" name="jform[columns][<?php echo $id;?>][fallback]"
					       value="1" <?php echo $column->fallback == '0' ? '' : ' checked="checked" '; ?> />
					<label for="<?php echo $id;?>_fallback1" class="btn btn-default"><?php echo JText::_('JYES'); ?></label>
					<input id="<?php echo $id;?>_fallback0" type="radio" name="jform[columns][<?php echo $id;?>][fallback]"
					       value="0" <?php echo $column->fallback == '0' ? ' checked="checked" ' : ''; ?> />
					<label for="<?php echo $id;?>_fallback0" class="btn btn-default"><?php echo JText::_('JNO'); ?></label>
				</fieldset>
			</div>
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_FILTER_DESC'); ?>">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_FILTER'); ?>
				</div>
				<?php echo JHtml::_(
					'select.genericlist',
					$filterTypes,
					'jform[columns][' . $id . '][filter]',
					' class="required" ',
					'value',
					'text',
					$column->filter
				); ?>
			</div>
			<div class="input-group input-group-sm">
				<div class="input-group-addon hasTooltip" title="<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_DESCRIPTION_DESC'); ?>">
					<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_DESCRIPTION'); ?>
				</div>
				<input type="text" name="jform[columns][<?php echo $id;?>][description]" value="<?php echo $column->description;?>" class="form-control" />
			</div>
			<br />
			<div>
				<strong><?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_EXTRA_PARAMETERS'); ?></strong>
				<button type="button" class="btn btn-default btn-xs btn-primary add-new-parameter">
					<i class="icon-plus"></i>
					<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_ADD_NEW_PARAMETER'); ?>
				</button>
				<div class="column-parameter-empty hide">
					<div class="row-stripped" style="padding:0">
						<div class="form-group">
							<div class="control-label">
								<button type="button" class="btn btn-default btn-xs btn-danger parameter-remove-row">
									<i class="icon-minus"></i>
									<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_REMOVE_LABEL'); ?>
								</button>
							</div>
							<div class="control-label">
								<input type="text" name="jform[columns][<?php echo $id;?>][extra_field_key][]" value="" />
							</div>
							<div class="control-label">
								<input type="text" name="jform[columns][<?php echo $id;?>][extra_field_value][]" value="" />
							</div>
						</div>
					</div>
				</div>
				<div class="column-parameter-list">
					<?php if (!empty($column->params)) : ?>
						<?php foreach ($column->params as $key => $param) :?>
							<div class="row-stripped" style="padding:0">
								<div class="form-group">
									<div class="control-label">
										<button type="button" class="btn btn-default btn-xs btn-danger parameter-remove-row">
											<i class="icon-minus"></i>
											<?php echo JText::_('COM_REDCORE_TRANSLATION_COLUMN_REMOVE_LABEL'); ?>
										</button>
									</div>
									<div class="control-label">
										<input type="text" name="jform[columns][<?php echo $id;?>][extra_field_key][]" value="<?php echo $key;?>" />
									</div>
									<div class="control-label">
										<input type="text" name="jform[columns][<?php echo $id;?>][extra_field_value][]" value="<?php echo $param;?>" />
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
