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
RHelperAsset::load('component.bs3.min.css', 'redcore');



$status = RedcoreHelpersTranslation::getTranslationItemStatus($this->item->original, array_keys($this->columns));
$hiddenFields = array();

$action = JRoute::_('index.php?option=com_redcore&view=translation');
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
	Joomla.submitbutton = function(task)
	{
		//Go through each form and submit them individually
		jQuery('form').each(function() 
		{
		    Joomla.submitform(task, this);
		});
	}
</script>