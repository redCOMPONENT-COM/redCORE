<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;
jimport('joomla.html.editor');

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
JHtml::_('rsearchtools.main');

$input = JFactory::getApplication()->input;

// Option parameters from layout
$item = $displayData['item'];
$columns = $displayData['columns'];
$editor = $displayData['editor'];
$translationTable = $displayData['translationTable'];
$languageCode = $displayData['languageCode'];
$form = $displayData['form'];
$noTranslationColumns = $displayData['noTranslationColumns'];
$modal = !empty($displayData['modal']) ? $displayData['modal'] : false;

$status = RTranslationHelper::getTranslationItemStatus($item->original, array_keys($columns));

$predefinedOptions = array(
	1   => 'JPUBLISHED',
	0   => 'JUNPUBLISHED',
	2   => 'JARCHIVED',
	-2  => 'JTRASHED',
	'*' => 'JALL'
);

?>
<div id="<?php echo $languageCode; ?>">
	<div class="translation-status">
		<div class="col-md-3">
				<?php echo JText::_('JSTATUS'); ?>
			<br>
				<span class="<?php echo $status['badge']; ?>">
					<?php echo JText::_($status['status']); ?>
				</span>
		</div>
		<div class="col-md-3">
			<?php echo $form->getLabel('rctranslations_state'); ?>
			<br>
			<?php echo $form->getInput('rctranslations_state'); ?>	
		</div>
		<div class="col-md-3">
			<?php echo $form->getLabel('rctranslations_modified'); ?>
			<br>
			<?php echo $form->getInput('rctranslations_modified'); ?>
		</div>
		<div class="col-md-3">
			<?php echo $form->getLabel('rctranslations_modified_by'); ?>
			<br>
			<?php echo $form->getInput('rctranslations_modified_by'); ?>
		</div>
		<?php if ($modal == false) : ?>
		<div class="col-md-3">
			<?php echo $form->getLabel('rctranslations_language'); ?>
			<br>
			<?php echo $form->getInput('rctranslations_language'); ?>
		</div>
		<?php else : ?>
			<input type="hidden" name="jform[rctranslations_language]" id="jform_rctranslations_language" value="<?php echo $languageCode; ?>"/>
		<?php endif; ?>
		<hr>
	</div>
	<br>

	<!-- Write out all fields -->
	<?php foreach ($columns as $columnKey => $column) : ?>
		<?php if ($column['value_type'] == 'referenceid' || $column['value_type'] == 'hiddentext') : ?>
			<?php continue; ?>
		<?php endif; ?>
			
		<?php
			$length = !empty($column['length']) ? $column['length'] : 60;
			$maxLength = !empty($column['maxlength']) ? 'maxlength="' . $column['maxlength'] . '"' : '';
			$maxRows = !empty($column['rows']) ? $column['rows'] : 15;
			$maxCols = !empty($column['columns']) ? $column['columns'] : 30;
		?>

		<!-- Name of field -->
			<div class="col-md-12">
				<hr class="translation-divider">
				<hr class="translation-divider">
				<?php echo JText::_('COM_REDCORE_TRANSLATIONS_FIELD') . ': <strong>' . $column['title']; ?></strong>
				
				<!-- Copy button -->
				<button
					class="pull-right btn btn-default"
					type="button"
					onclick="setTranslationValue('<?php echo $columnKey;?>', '<?php echo $columnKey;?>', <?php echo ($column['value_type'] != 'params') ? 'false' : 'true' ?>, '<?php echo $languageCode; ?>');">
					<span class="icon-copy"></span>
					<?php echo JText::_('RTOOLBAR_COPY');?>
				</button>
				<button
					class="pull-right btn btn-default"
					type="button"
					onclick="setTranslationValue('<?php echo $columnKey;?>', '', <?php echo ($column['value_type'] != 'params') ? 'false' : 'true' ?>, '<?php echo $languageCode; ?>');">
					<span class="icon-trash"></span>
					<?php echo JText::_('JCLEAR');?>
				</button>
				<hr class="translation-divider">
			</div>

		<?php if ($column['value_type'] != 'params') : ?>
			<!-- Value of field in the original item -->
			<div class="col-md-4">
				<strong><?php echo JText::_('COM_REDCORE_TRANSLATIONS_ORIGINAL');?></strong>
				<br>
				<?php if ($column['value_type'] == 'state'): ?>
					<?php echo isset($predefinedOptions[$item->original->{$columnKey}]) ?
						JText::_($predefinedOptions[$item->original->{$columnKey}]) : $item->original->{$columnKey}; ?>
				<?php else: ?>
					<?php echo $item->original->{$columnKey}; ?>
				<?php endif; ?>
				<textarea name="original[<?php echo $columnKey;?>]" style="display:none"><?php echo $item->original->{$columnKey};?></textarea>
			</div>

			<!-- Field for entering translation -->
			<div class="col-md-8">
				<strong><?php echo JText::_('COM_REDCORE_TRANSLATIONS_TRANSLATION');?></strong>
				<br>
				<!-- Text field -->
				<?php if ($column['value_type'] == 'text' || $column['value_type'] == 'titletext'): ?>
					<input
						class="inputbox"
						type="text"
						name="translation[<?php echo $columnKey;?>]"
						size="<?php echo $length;?>"
						value="<?php echo $item->translation->{$columnKey}; ?>"
						<?php echo $maxLength;?> />
				<!-- State of field -->
				<?php elseif ($column['value_type'] == 'state'): ?>
					<?php echo RLayoutHelper::render(
						'translation.fields.state',
						array(
							'original' => $item->original->{$columnKey},
							'translation' => $item->translation->{$columnKey},
							'name' => $columnKey,
							'column' => $column,
							'translationForm' => true,
							'predefinedOptions' => $predefinedOptions
						),
						JPATH_ROOT . '/administrator/components/com_redcore/layouts'
					); ?>
				<!-- Textarea field -->
				<?php elseif ($column['value_type'] == 'textarea'): ?>
					<textarea
						name="translation[<?php echo $columnKey;?>]"
						rows="<?php echo $maxRows;?>"
						cols="<?php echo $maxCols;?>"
						><?php echo $item->translation->{$columnKey}; ?></textarea>
				<!-- WYSIWYG editor field -->
				<?php elseif($column['value_type'] == 'htmltext'): ?>
					<?php
					$editorid = 'translation[' . $columnKey . ']_' . $languageCode;
					echo $editor->display(
						// Area name
						'translation[' . $columnKey . ']',
						// Content
						$item->translation->{$columnKey},
						// Width
						'60%',
						// Height
						'300',
						// Rows
						'70',
						// Cols
						'15',
						// Buttons
						(!empty($column['ebuttons']) ? $column['ebuttons'] : ''),
						// ID
						$editorid
					);
					?>
				<!-- Field for uploading and saving images -->
				<?php elseif ($column['value_type'] == 'images'): ?>
					<div class="input-group">
						<input
							class="input-lg"
							type="text"
							name="translation[<?php echo $columnKey;?>]"
							id="translation<?php echo $columnKey;?>"
							size="<?php echo $length;?>"
							value="<?php echo $item->translation->{$columnKey}; ?>" <?php echo $maxLength;?>/>
						<a class="modal btn btn-default" title="<?php echo JText::_("JSELECT")?>"
							href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;fieldid=translation<?php echo $columnKey;?>"
							rel="{handler: 'iframe', size: {x: 800, y: 500}}"><?php echo JText::_("JSELECT")?></a>
					</div>
				<?php elseif ($column['value_type'] == 'readonlytext'): ?>
					<?php $value = !empty($item->translation->{$columnKey}) ? $item->translation->{$columnKey} : $item->original->{$columnKey}; ?>
					<input class="inputbox" readonly="yes" type="text" name="translation[<?php echo $columnKey;?>]" size="<?php echo $length;?>" value="<?php echo $value; ?>" maxlength="<?php echo $maxLength;?>"/>
				<?php endif; ?>
			</div>
			<?php else: ?>
				<!-- Parameters -->
				<table>
				<td id="original_field_<?php echo $columnKey;?>">
					<button
						class="btn btn-default"
						type="button"
						onclick="jQuery(this).parent().find('div:first').toggle()">
						<span class="icon-plus"></span>
						<?php echo JText::_('COM_REDCORE_TRANSLATIONS_SHOW_HIDE_ORIGINALS');?>
					</button>
				<div style="display:none">
					<br />
					<?php echo RLayoutHelper::render(
						'translation.params',
						array(
							'form' => RTranslationHelper::loadParamsForm($column, $translationTable, $item->original, 'original', JPATH_ADMINISTRATOR),
							'original' => $item->original->{$columnKey},
							'translation' => $item->translation->{$columnKey},
							'name' => $columnKey,
							'column' => $column,
							'translationForm' => false,
							'suffix' => $languageCode,
						),
						JPATH_ROOT . '/administrator/components/com_redcore/layouts'
					); ?>
					<textarea name="original[params_<?php echo $columnKey;?>]" style="display:none"><?php
						if (is_array($item->original->{$columnKey})) :
							echo json_encode($item->original->{$columnKey});
						else:
							echo $item->original->{$columnKey};
						endif;
					?></textarea>
				</div>
				</td>
					</tr>
					<tr>
					<td><?php echo JText::_('COM_REDCORE_TRANSLATIONS_TRANSLATION');?></td>
					<td id="translation_field_<?php echo $columnKey;?>">
						<?php echo RLayoutHelper::render(
							'translation.params',
							array(
								'form' => RTranslationHelper::loadParamsForm($column, $translationTable, $item->translation, 'translation', JPATH_ADMINISTRATOR),
								'original' => $item->original->{$columnKey},
								'translation' => $item->translation->{$columnKey},
								'name' => $columnKey,
								'column' => $column,
								'translationForm' => true,
								'suffix' => $languageCode,
							),
						JPATH_ROOT . '/administrator/components/com_redcore/layouts'
						);
						?>
					</td>
				</tr>
				</table>
			<?php endif; ?>
	<?php endforeach; ?>

	<?php foreach ($noTranslationColumns as $columnKey => $column) : ?>
		<div class="col-md-12">
			<hr class="translation-divider">
			<hr class="translation-divider">
			<?php echo JText::_('COM_REDCORE_TRANSLATIONS_FIELD') . ': <strong>' . $column['title']; ?></strong>
			<hr class="translation-divider">
		</div>
		<div class="col-md-4">
			<strong><?php echo JText::_('COM_REDCORE_TRANSLATIONS_ORIGINAL');?></strong>
			<br>
			<span id="original_field_<?php echo $columnKey;?>">
				<?php echo !empty($item->original->{$columnKey}) ? $item->original->{$columnKey} : '--'; ?>
			</span>
		</div>
	<?php endforeach; ?>

	<?php foreach ($columns as $columnKey => $column) : ?>
		<?php if ($column['value_type'] == 'hiddentext') : ?>
			<textarea name="original[<?php echo $columnKey;?>]" style="display:none"><?php echo $item->original->{$columnKey};?></textarea>
			<textarea name="translation[<?php echo $columnKey;?>]"  style="display:none"><?php echo $item->translation->{$columnKey}; ?></textarea>
		<?php endif; ?>
	<?php endforeach; ?>

	<!-- Hidden fields -->
	<?php foreach ($translationTable->primaryKeys as $primaryKey): ?>
		<input type="hidden" name="translation[<?php echo $primaryKey; ?>]" value="<?php echo $item->original->{$primaryKey}; ?>"/>
	<?php endforeach; ?>
	<input type="hidden" name="option" value="com_redcore"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="id" value="<?php echo $input->getString('id', ''); ?>"/>
	<?php if ($modal == false) : ?>
		<input type="hidden" name="rctranslations_id" value="<?php echo $item->id; ?>" />
	<?php endif; ?>
	<input type="hidden" name="translationTableName" value="<?php echo $input->getString('translationTableName', ''); ?>"/>
	<input type="hidden" name="component" value="<?php echo $input->getString('component', ''); ?>"/>
	<?php echo JHTML::_('form.token'); ?>
</div>
