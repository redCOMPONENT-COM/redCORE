<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$model = isset($displayData['model']) ? $displayData['model'] : 'xmlForm';
$return = isset($displayData['return']) ? $displayData['return'] : null;
$action = RRoute::_('index.php?option=com_redcore&task=translation_tables.uploadXmlFile');
?>
<button
	class="btn btn-success"
	type="button"
	onclick="jQuery('#redcoreContentElement').click()">
	<i class="icon-upload"></i>
	<?php echo JText::_('COM_REDCORE_TRANSLATION_TABLE_UPLOAD_CONTENT_ELEMENT_XML') ?>
</button>
<form action="<?php echo $action; ?>" method="POST" enctype="multipart/form-data" id="xmlForm_<?php echo $model; ?>">
	<div class="input_upload_button">
		<input onchange="jQuery('#xmlForm_<?php echo $model; ?>').submit();"
		       type="file"
		       multiple="multiple"
		       name="redcoreContentElement[]"
		       id="redcoreContentElement"
		       accept="application/xml"
		       class="hide" />
	</div>

	<input type="hidden" name="model" value="<?php echo $model; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
	<?php if ($return): ?>
		<input type="hidden" value="<?php echo $return; ?>" name="return" />
	<?php endif; ?>
</form>
