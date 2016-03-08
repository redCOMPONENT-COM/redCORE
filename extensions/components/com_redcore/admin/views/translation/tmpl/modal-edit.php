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
$currentEditor = JFactory::getConfig()->get('editor');
?>
	<!-- Tabs for selecting languages -->
	<ul class="nav nav-tabs" id="categoryTab">
		<?php foreach ($contentLanguages as $language) : ?>
			<li>
				<a href="#fields-<?php echo $language->lang_id; ?>" data-toggle="tab"><strong><?php echo $language->title; ?></strong></a>
			</li>
		<?php endforeach;?>
	</ul>

	<!-- Container for the fields of each language -->
	<div class="tab-content">
		<?php foreach ($contentLanguages as $language) : ?>
		
		<?php $input->set('template_language', $language->lang_code); ?>

			<div class="tab-pane" id="fields-<?php echo $language->lang_id; ?>">	
				<form method="post" target="my_iframe_<?php echo $language->lang_id; ?>" name="adminForm_<?php echo $language->lang_id; ?>" id="adminForm_<?php echo $language->lang_id; ?>" class="form-validate form-horizontal">
					<?php echo $this->loadTemplate('fields'); ?>
				</form>
			</div>
			<iframe name="my_iframe_<?php echo $language->lang_id; ?>" style="display:none;"></iframe>
		<?php endforeach;?>
		
	</div>
<script type="text/javascript">
	function setTranslationValue(elementName, elementOriginal, langCode)
	{
		var tabArea = jQuery('#'+langCode);
		var val = elementOriginal != '' ? tabArea.find('[name="original[' + elementOriginal + ']"]').val() : '';
		var targetElement = tabArea.find('[name="translation[' + elementName + ']"]');

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

	Joomla.submitbutton = function(task)
	{
		//Go through each form and submit them individually
		jQuery('form').each(function() 
		{
		    Joomla.submitform(task, this);
		});
	}
</script>