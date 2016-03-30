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
$languageCode = $input->getString('template_language', '');

$predefinedOptions = array(
	1   => 'JPUBLISHED',
	0   => 'JUNPUBLISHED',
	2   => 'JARCHIVED',
	-2  => 'JTRASHED',
	'*' => 'JALL'
);

//Set the item property to the proper translation item
$rctranslationId = RedcoreHelpersTranslation::getTranslationItemId($input->getString('id', ''),$languageCode,$this->translationTable->primaryKeys);
$this->setItem($rctranslationId);
?>
<div id="<?php echo $languageCode; ?>">
	<!-- Write out all fields -->
	<?php foreach ($this->columns as $columnKey => $column) : ?>
		<?php if ($column['type'] == 'referenceid' || $column['type'] == 'hiddentext') : ?>
			<?php continue; ?>
		<?php endif; ?>
		
		<?php
			$length = !empty($column['length']) ? $column['length'] : 60;
			$maxLength = !empty($column['maxlength']) ? 'maxlength="' . $column['maxlength'] . '"' : '';
			$maxRows = !empty($column['rows']) ? $column['rows'] : 15;
			$maxCols = !empty($column['columns']) ? $column['columns'] : 30;
		?>
		<?php if ($column['type'] != 'params') : ?>
			<!-- Name of field -->
			<div class="field-name">
				<hr>
				<?php echo JText::_('COM_REDCORE_TRANSLATIONS_FIELD') . ': <strong>' . $column['titleLabel']; ?></strong>
				<hr>
				<!-- Copy button -->
				<button
					class="pull-right btn btn-default"
					type="button"
					onclick="setTranslationValue('<?php echo $columnKey;?>', '<?php echo $columnKey;?>', '<?php echo $languageCode; ?>');">
					<span class="icon-copy"></span>
					<?php echo JText::_('RTOOLBAR_COPY');?>
				</button>
			</div>

			<!-- Value of field in the original item -->
			<div class="original-field">
				<strong><?php echo JText::_('COM_REDCORE_TRANSLATIONS_ORIGINAL');?></strong>
				<br>
				<?php if ($column['type'] == 'state'): ?>
					<?php echo isset($predefinedOptions[$this->item->original->{$columnKey}]) ?
						JText::_($predefinedOptions[$this->item->original->{$columnKey}]) : $this->item->original->{$columnKey}; ?>
				<?php else: ?>
					<?php echo $this->item->original->{$columnKey}; ?>
				<?php endif; ?>
				<textarea name="original[<?php echo $columnKey;?>]" style="display:none"><?php echo $this->item->original->{$columnKey};?></textarea>
			</div>

			<!-- Field for entering translation -->
			<div class="translate-field">
				<strong><?php echo JText::_('COM_REDCORE_TRANSLATIONS_TRANSLATION');?></strong>
				<br>
				<!-- Text field -->
				<?php if ($column['type'] == 'text' || $column['type'] == 'titletext'): ?>
					<input
						class="inputbox"
						type="text"
						name="translation[<?php echo $columnKey;?>]"
						size="<?php echo $length;?>"
						value="<?php echo $this->item->translation->{$columnKey}; ?>"
						<?php echo $maxLength;?> />
				<!-- State field -->
				<?php elseif ($column['type'] == 'state'): ?>
					<?php echo RLayoutHelper::render(
						'translation.fields.state',
						array(
							'original' => $this->item->original->{$columnKey},
							'translation' => $this->item->translation->{$columnKey},
							'name' => $columnKey,
							'column' => $column,
							'translationForm' => true,
							'predefinedOptions' => $predefinedOptions
						)
					); ?>
				<!-- Textarea field -->
				<?php elseif ($column['type'] == 'textarea'): ?>
					<textarea
						name="translation[<?php echo $columnKey;?>]"
						rows="<?php echo $maxRows;?>"
						cols="<?php echo $maxCols;?>"
						><?php echo $this->item->translation->{$columnKey}; ?></textarea>
				<!-- WYSIWYG editor field -->
				<?php elseif($column['type'] == 'htmltext'): ?>
					<?php //$editorFields[] = array('editor_' . $columnKey, 'translation[' . $columnKey . ']');
					$editorid = 'translation[' . $columnKey . ']_' . $languageCode;
					echo $this->editor->display(
						// Area name
						'translation[' . $columnKey . ']',
						// Content
						$this->item->translation->{$columnKey},
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
						//ID
						$editorid
					);
					$this->editorList[] = $editorid;
					?>
				<!-- Field for uploading and saving images -->
				<?php elseif ($column['type'] == 'images'): ?>
					<div class="input-group">
						<input
							class="input-lg"
							type="text"
							name="translation[<?php echo $columnKey;?>]"
							id="translation<?php echo $columnKey;?>"
							size="<?php echo $length;?>"
							value="<?php echo $this->item->translation->{$columnKey}; ?>" <?php echo $maxLength;?>/>
						<a class="modal btn btn-default" title="<?php echo JText::_("JSELECT")?>"
							href="index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;fieldid=translation<?php echo $columnKey;?>"
							rel="{handler: 'iframe', size: {x: 800, y: 500}}"><?php echo JText::_("JSELECT")?></a>
					</div>
				<?php elseif ($column['type'] == 'readonlytext'): ?>
					<?php $value = !empty($this->item->translation->{$columnKey}) ? $this->item->translation->{$columnKey} : $this->item->original->{$columnKey}; ?>
					<input class="inputbox" readonly="yes" type="text" name="translation[<?php echo $columnKey;?>]" size="<?php echo $length;?>" value="<?php echo $value; ?>" maxlength="<?php echo $maxLength;?>"/>
				<?php endif; ?>
			</div>
		<?php else: ?>
		<!-- Parameters -->
			<div style="display:none">
				<br />
				<?php echo RLayoutHelper::render(
					'translation.params',
					array(
						'form' => RTranslationHelper::loadParamsForm($column, $this->contentElement, $this->item->original, 'original'),
						'original' => $this->item->original->{$columnKey},
						'translation' => $this->item->translation->{$columnKey},
						'name' => $columnKey,
						'column' => $column,
						'translationForm' => false,
					)
				); ?>
				<textarea name="original[params_<?php echo $columnKey;?>]" style="display:none"><?php
					if (is_array($this->item->original->{$columnKey})) :
						echo json_encode($this->item->original->{$columnKey});
					else:
						echo $this->item->original->{$columnKey};
					endif;
					?></textarea>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>

	<!-- Hidden fields -->
	<?php foreach ($this->translationTable->primaryKeys as $primaryKey): ?>
		<input type="hidden" name="translation[<?php echo $primaryKey; ?>]" value="<?php echo $this->item->original->{$primaryKey}; ?>"/>
	<?php endforeach; ?>
	<input type="hidden" name="option" value="com_redcore"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="id" value="<?php echo $input->getString('id', ''); ?>"/>
	<input type="hidden" name="rctranslations_id" value=""/>
	<input type="hidden" name="contentelement" value="<?php echo $input->getString('contentelement', ''); ?>"/>
	<input type="hidden" name="component" value="<?php echo $input->getString('component', ''); ?>"/>
	<input type="hidden" name="jform[rctranslations_language]" value="<?php echo $languageCode; ?>"/>
	<?php echo JHTML::_('form.token'); ?>
</div>
