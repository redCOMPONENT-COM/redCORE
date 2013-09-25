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
	throw new InvalidArgumentException('The button is not passed to the layout "button.standard".');
}

/** @var RToolbarButtonStandard $button */
$button = $data['button'];

$text = $button->getText();
$iconClass = $button->getIconClass();
$task = $button->getTask();
$isList = $button->isList();
$class = $button->getClass();

// Get the button command.
JHtml::_('behavior.framework');
$message = JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
$message = addslashes($message);

if ($isList)
{
	$cmd = "if (document.adminForm.boxchecked.value==0){alert('$message');}else{ Joomla.submitbutton('$task')}";
}
else
{
	$cmd = "Joomla.submitbutton('$task')";
}

// Get the button class.
$btnClass = 'btn';

if (!empty($class))
{
	$btnClass .= ' ' . $class;
}
?>

<button href="#" onclick="<?php echo $cmd ?>" class="<?php echo $btnClass ?>">
	<?php if (!empty($iconClass)) : ?>
		<i class="<?php echo $iconClass ?>"></i>
	<?php endif; ?>
	<?php echo $text ?>
</button>
