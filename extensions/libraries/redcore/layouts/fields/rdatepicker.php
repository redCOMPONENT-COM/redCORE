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

$data->picker = isset($data->picker) ? $data->picker : 'datepicker';

// Add jquery UI js.
JHtml::_('rjquery.' . $data->picker);

$doc = JFactory::getDocument();

$script = "
(function($){
	$(document).ready(function () {
		$('" . $data->cssId . "')." . $data->picker . "(" . $data->datepickerOptions . ");
	});
})(jQuery);
";

$doc->addScriptDeclaration($script);

// Load the common css
RHelperAsset::load('rdatepicker.min.css', 'redcore');

echo $data->fieldHtml;
