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

$hiddenFields = array();

// HTML helpers
JHtml::_('behavior.keepalive');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');
JHtml::_('rsearchtools.main');
$input = JFactory::getApplication()->input;
$action = JRoute::_('index.php?option=com_redcore&view=translation&language=' . $input->getString('language', ''));
$predefinedOptions = array(
	1   => 'JPUBLISHED',
	0   => 'JUNPUBLISHED',
	2   => 'JARCHIVED',
	-2  => 'JTRASHED',
	'*' => 'JALL'
);
?>
<script type="text/javascript">
	function setTranslationValue(elementName, elementOriginal, setParams, langCode)
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
			var targetElement = jQuery('[name="translation[' + langCode + '][' + elementName + ']"]');

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
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
</script>
<form action="<?php echo $action; ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<?php
	echo RLayoutHelper::render(
		'translation.input',
		array(
			'item' => $this->item,
			'columns' => $this->columns,
			'editor' => $this->editor,
			'translationTable' => $this->translationTable,
			'languageCode' => $input->getString('language', ''),
			'noTranslationColumns' => $this->noTranslationColumns,
			'form' => $this->form,
		)
	);
	?>
</form>
