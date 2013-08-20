<?php
/**
 * @package     RedRad
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDRAD') or die;

$data = $displayData;

// Add jquery UI js.
JHtml::_('rjquery.framework');

$link = (string) $data->element['link'];

$automaticLink = 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=' . $asset
		. '&amp;author=' . $data->form->getValue($authorField)) . '&amp;fieldid=' . $data->id
		. '&amp;folder=' . $folder;

$script = "
$('#openBtn').click(function() {
  	$('.modal-body').load('" . $data. "',function(result){
	    $('#myModal').modal({show:true});
	});
});
"

$script = "(function($){
	$(document).ready(function () {
	$('" . $data->cssId . "').datepicker(
	" . $data->datepickerOptions . "
	);
	});
	})(jQuery);
";

$doc = JFactory::getDocument();
$doc->addScriptDeclaration($script);

echo $data->fieldHtml;
