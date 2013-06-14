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
JHtml::_('rjquery.datepicker');

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
