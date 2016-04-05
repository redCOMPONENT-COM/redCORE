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

$status = RedcoreHelpersTranslation::getTranslationItemStatus($this->item->original, array_keys($this->columns));
$hiddenFields = array();

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
JHtml::_('rsearchtools.main');
$action = JRoute::_('index.php?option=com_redcore&view=translation');
$input = JFactory::getApplication()->input;
$predefinedOptions = array(
	1   => 'JPUBLISHED',
	0   => 'JUNPUBLISHED',
	2   => 'JARCHIVED',
	-2  => 'JTRASHED',
	'*' => 'JALL'
);
?>
<script type="text/javascript">
	function setTranslationValue(elementName, elementOriginal, setParams)
	{
		if (setParams)
		{
			var originalValue = '';
			var name = '';
			var originalField = {};
			jQuery('#translation_field_' + elementName + ' :input').each(function(){
				name = jQuery(this).attr('name');
				originalValue = '';
				originalField = {};
				if (name)
				{
					if (jQuery(this).is(':checkbox, :radio'))
					{
						originalField = jQuery('[name="' + name.replace('translation', 'original') + '"][value="' + jQuery(this).val() + '"]');
						var checked = (originalField.length > 0) ? jQuery(originalField).is(':checked') : false;
						var label = jQuery(this).parent().find('[for="' + jQuery(this).attr('id') + '"]');

						jQuery(this).attr('checked', checked);
						jQuery(label).removeClass('active btn-success btn-danger btn-primary');
						if (checked)
						{
							var css = '';
							switch(jQuery(this).val()) {
								case '' : css = 'btn-primary'; break;
								case '0': css = 'btn-danger'; break;
								default : css = 'btn-success'; break;
							}
							jQuery(label).addClass('active ' + css).button('toggle');
						}
					}
					else
					{
						originalField = jQuery('[name="' + name.replace('translation', 'original') + '"]');
						if (originalField.length > 0)
						{
							originalValue = jQuery(originalField).val();
						}
						jQuery(this)
							.val(originalValue)
							.trigger("liszt:updated");
					}
				}
			});
		}
		else
		{
			var val = elementOriginal != '' ? jQuery('[name="original[' + elementOriginal + ']"]').val() : '';
			var targetElement = jQuery('[name="translation[' + elementName + ']"]');

			if (jQuery(targetElement).is('textarea'))
			{
				jQuery(targetElement).val(val);
				jQuery(targetElement).parent().find('iframe').contents().find('body').html(val);
			}
			else
			{
				jQuery(targetElement).val(val);
			}
		}
	}

	Joomla.submitbutton = function(task)
	{
		if (task != 'translation.cancel' && document.getElementById("jform_rctranslations_language").value == '')
		{
			alert('<?php echo JText::_('COM_REDCORE_TRANSLATIONS_SELECT_LANGUAGE', true) ?>');
		}
		else
		{
			<?php
			foreach ($this->columns as $editorColumnKey => $editorColumn) :
				if ($editorColumn['value_type'] == 'htmltext') :
					echo $this->editor->save('translation[' . $editorColumnKey . ']');
				endif;
			endforeach;
			?>
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>
<form action="<?php echo $action; ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<div class="row">
		<div class="col-md-8" id="translationDetails">
		<table class="table table-striped">
			<?php foreach ($this->columns as $columnKey => $column) : ?>
				<?php if ($column['value_type'] == 'referenceid' || $column['value_type'] == 'hiddentext') : ?>
					<?php continue; ?>
				<?php endif; ?>
				<?php
					$length = !empty($column['length']) ? $column['length'] : 60;
					$maxLength = !empty($column['maxlength']) ? 'maxlength="' . $column['maxlength'] . '"' : '';
					$maxRows = !empty($column['rows']) ? $column['rows'] : 15;
					$maxCols = !empty($column['columns']) ? $column['columns'] : 30;
				?>
					<tr>
						<td colspan="3"><?php echo JText::_('COM_REDCORE_TRANSLATIONS_FIELD') . ': <strong>' . $column['title']; ?></strong>
						<?php if (!empty($this->item->translation->rctranslations_originals[$columnKey])
							&& $this->item->translation->rctranslations_originals[$columnKey] != md5($this->item->original->{$columnKey})): ?>
							<span class="label label-warning"><?php echo JText::_('COM_REDCORE_TRANSLATIONS_STATUS_CHANGED'); ?></span>
						<?php endif; ?>
							<button
								class="pull-right btn btn-default"
								type="button"
								onclick="setTranslationValue('<?php echo $columnKey;?>', '<?php echo $columnKey;?>', <?php echo ($column['value_type'] != 'params') ? 'false' : 'true' ?>);">
								<span class="icon-copy"></span>
								<?php echo JText::_('RTOOLBAR_COPY');?>
							</button>
							<button
								class="pull-right btn btn-default"
								type="button"
								onclick="setTranslationValue('<?php echo $columnKey;?>', '', <?php echo ($column['value_type'] != 'params') ? 'false' : 'true' ?>);">
								<span class="icon-trash"></span>
								<?php echo JText::_('JCLEAR');?>
							</button>
						</td>
					</tr>
			<?php if ($column['value_type'] != 'params') : ?>
					<tr>
						<td><?php echo JText::_('COM_REDCORE_TRANSLATIONS_ORIGINAL');?></td>
						<td id="original_field_<?php echo $columnKey;?>">
							<?php if ($column['value_type'] == 'state'): ?>
								<?php echo isset($predefinedOptions[$this->item->original->{$columnKey}]) ?
									JText::_($predefinedOptions[$this->item->original->{$columnKey}]) : $this->item->original->{$columnKey}; ?>
							<?php else: ?>
								<?php echo $this->item->original->{$columnKey}; ?>
							<?php endif; ?>
							<textarea name="original[<?php echo $columnKey;?>]" style="display:none"><?php echo $this->item->original->{$columnKey};?></textarea>
						</td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_REDCORE_TRANSLATIONS_TRANSLATION');?></td>
						<td id="translation_field_<?php echo $columnKey;?>">
							<?php if ($column['value_type'] == 'text' || $column['value_type'] == 'titletext'): ?>
								<input
									class="inputbox"
									type="text"
									name="translation[<?php echo $columnKey;?>]"
									size="<?php echo $length;?>"
									value="<?php echo $this->item->translation->{$columnKey}; ?>"
									<?php echo $maxLength;?> />
							<?php elseif ($column['value_type'] == 'state'): ?>
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
							<?php elseif ($column['value_type'] == 'textarea'): ?>
								<textarea
									name="translation[<?php echo $columnKey;?>]"
									rows="<?php echo $maxRows;?>"
									cols="<?php echo $maxCols;?>"
									><?php echo $this->item->translation->{$columnKey}; ?></textarea>
							<?php elseif($column['value_type'] == 'htmltext'): ?>
								<?php $editorFields[] = array('editor_' . $columnKey, 'translation[' . $columnKey . ']');
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
									(!empty($column['ebuttons']) ? $column['ebuttons'] : '')
								);
								?>
							<?php elseif ($column['value_type'] == 'images'): ?>
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
							<?php elseif ($column['value_type'] == 'readonlytext'): ?>
								<?php $value = !empty($this->item->translation->{$columnKey}) ? $this->item->translation->{$columnKey} : $this->item->original->{$columnKey}; ?>
								<input class="inputbox" readonly="yes" type="text" name="translation[<?php echo $columnKey;?>]" size="<?php echo $length;?>" value="<?php echo $value; ?>" maxlength="<?php echo $maxLength;?>"/>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td colspan="2">
						</td>
					</tr>
				<?php else:
					?>
					<tr>
						<td><?php echo JText::_('COM_REDCORE_TRANSLATIONS_ORIGINAL');?></td>
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
										'form' => RTranslationHelper::loadParamsForm($column, $this->translationTable, $this->item->original, 'original'),
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
						</td>
					</tr>
					<tr>
						<td><?php echo JText::_('COM_REDCORE_TRANSLATIONS_TRANSLATION');?></td>
						<td id="translation_field_<?php echo $columnKey;?>">
							<?php echo RLayoutHelper::render(
								'translation.params',
								array(
									'form' => RTranslationHelper::loadParamsForm($column, $this->translationTable, $this->item->translation, 'translation'),
									'original' => $this->item->original->{$columnKey},
									'translation' => $this->item->translation->{$columnKey},
									'name' => $columnKey,
									'column' => $column,
									'translationForm' => true,
								)
							);
							?>
						</td>
					</tr>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php foreach ($this->noTranslationColumns as $columnKey => $column) : ?>
				<tr>
					<td colspan="2"><?php echo JText::_('COM_REDCORE_TRANSLATIONS_FIELD') . ': <strong>' . $column['title']; ?></strong></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_REDCORE_TRANSLATIONS_ORIGINAL');?></td>
					<td id="original_field_<?php echo $columnKey;?>">
						<?php echo !empty($this->item->original->{$columnKey}) ? $this->item->original->{$columnKey} : '--'; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php foreach ($this->columns as $columnKey => $column) : ?>
			<?php if ($column['value_type'] == 'hiddentext') : ?>
				<textarea name="original[<?php echo $columnKey;?>]" style="display:none"><?php echo $this->item->original->{$columnKey};?></textarea>
				<textarea name="translation[<?php echo $columnKey;?>]"  style="display:none"><?php echo $this->item->translation->{$columnKey}; ?></textarea>
			<?php endif; ?>
		<?php endforeach; ?>
		</div>
		<div class="col-md-4">
			<div class="form-group">
				<div class="control-label">
					<?php echo JText::_('JSTATUS'); ?>
				</div>
				<div class="controls">
					<span class="<?php echo $status['badge']; ?>">
						<?php echo JText::_($status['status']); ?>
					</span>
				</div>
			</div>
			<div class="form-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('rctranslations_language'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('rctranslations_language'); ?>
				</div>
			</div>
			<div class="form-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('rctranslations_state'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('rctranslations_state'); ?>
				</div>
			</div>
			<div class="form-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('rctranslations_modified'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('rctranslations_modified'); ?>
				</div>
			</div>
		</div>
	</div>
	<?php foreach ($this->translationTable->primaryKeys as $primaryKey): ?>
		<input type="hidden" name="translation[<?php echo $primaryKey; ?>]" value="<?php echo $this->item->original->{$primaryKey}; ?>"/>
	<?php endforeach; ?>
	<input type="hidden" name="option" value="com_redcore"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="id" value="<?php echo $input->getString('id', ''); ?>"/>
	<input type="hidden" name="rctranslations_id" value="<?php echo $this->item->id; ?>"/>
	<input type="hidden" name="translationTableName" value="<?php echo $input->getString('translationTableName', ''); ?>"/>
	<input type="hidden" name="component" value="<?php echo $input->getString('component', ''); ?>"/>
	<?php echo JHTML::_('form.token'); ?>
</form>
