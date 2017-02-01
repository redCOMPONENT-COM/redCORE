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

$input = JFactory::getApplication()->input;

$contentLanguages = JLanguageHelper::getLanguages();

$view = $displayData['view'];
$model = RModel::getAdminInstance('translation', array(), 'com_redcore');
$view->setModel($model, true);
$first = true;
?>
<form method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
<!-- Tabs for selecting languages -->
	<ul class="nav nav-tabs" id="categoryTab">
		<?php foreach ($contentLanguages as $language) : ?>
			<li class="<?php echo $first ? 'active' : '';?>">
				<a href="#fields-<?php echo $language->lang_id; ?>" data-toggle="tab"><strong><?php echo $language->title; ?></strong></a>
			</li>
			<?php $first = false; ?>
		<?php endforeach;?>
	</ul>
	<!-- Container for the fields of each language -->
	<div class="tab-content">	
		<?php $first = true; ?>
		<?php foreach ($contentLanguages as $language) : ?>
			<div class="tab-pane<?php echo $first ? ' active in' : '';?>" id="fields-<?php echo $language->lang_id; ?>">
				<?php
					$rctranslationId = RTranslationHelper::getTranslationItemId($input->getString('id', ''), $language->lang_code, $view->translationTable->primaryKeys, $view->translationTable->name);
					$view->setItem($rctranslationId);
					$properties = $view->getLayoutProperties();

					echo RLayoutHelper::render(
						'translation.input',
						array(
							'item' => $properties['item'],
							'columns' => $view->columns,
							'editor' => $properties['editor'],
							'translationTable' => $view->translationTable,
							'languageCode' => $language->lang_code,
							'form' => $properties['form'],
							'noTranslationColumns' => $view->noTranslationColumns,
							'modal' => true,
						),
						JPATH_ROOT . '/administrator/components/com_redcore/layouts'
					);
				?>
			</div>
			<?php $first = false; ?>
		<?php endforeach; ?>
	</div>
</form>
<script type="text/javascript">
	function setTranslationValue(elementName, elementOriginal, setParams, langCode)
	{
		var tabArea = jQuery('#'+langCode);
		if (setParams)
		{
			var originalValue = '';
			var name = '';
			var originalField = {};
			tabArea.find('#translation_field_' + elementName + ' :input').each(function(){
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
			var val = elementOriginal != '' ? tabArea.find('[name="original[' + elementOriginal + ']"]').val() : '';
			var targetElement = tabArea.find('[name="translation['+ langCode +'][' + elementName + ']"]');

			if (tabArea.find(targetElement).is('textarea'))
			{
				tabArea.find(targetElement).val(val);
				tabArea.find(targetElement).parent().find('iframe').contents().find('body').html(val);
			}
			else
			{
				tabArea.find(targetElement).val(val);
			}
		}
	}

	Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
</script>
