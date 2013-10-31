<?php
/**
 * @package     Redcore
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$data = $displayData;

if (!isset($data['button']))
{
	throw new InvalidArgumentException('The button is not passed to the layout "button.link".');
}

/** @var RToolbarButtonModal $button */
$button = $data['button'];

$class = $button->getClass();
$iconClass = $button->getIconClass();
$text = $button->getText();
$isList = $button->isList();

$dataTarget = $button->getDataTarget();

// Get the button class.
$btnClass = 'btn';

if (!empty($class))
{
	$btnClass .= ' ' . $class;
}

$cmd = "jQuery('" . $dataTarget . "').modal('toggle');";

if ($isList)
{
	// Get the button command.
	JHtml::_('behavior.framework');
	$message = JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
	$message = addslashes($message);
	$cmd = "if (document.adminForm.boxchecked.value == 0) {alert('" . $message . "');jQuery('" . $dataTarget . "').modal('hide');}
	else {jQuery('" . $dataTarget  . "').modal('toggle');}";
}
?>
<button class="<?php echo $btnClass ?>" onclick="<?php echo $cmd ?>">
	<?php if (!empty($iconClass)) : ?>
		<i class="<?php echo $iconClass ?>"></i>
	<?php endif; ?>
	<?php echo $text ?>
</button>
