<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$data = $displayData;

// Add jquery UI js.
JHtml::_('rjquery.datepicker');

$doc = JFactory::getDocument();

$script = "
(function($){
	$(document).ready(function () {
		$('" . $data->cssId . "').datepicker(" . $data->datepickerOptions . ");
	});
})(jQuery);
";

$doc->addScriptDeclaration($script);

// Load the common css
RHelperAsset::load('rdatepicker.min.css', 'redcore');

echo $data->fieldHtml;
