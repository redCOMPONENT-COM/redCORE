<?php
/**
 * @package     Redcore
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$data = $displayData;

if (!isset($data['button']))
{
	throw new InvalidArgumentException(JText::sprintf('LIB_REDCORE_LAYOUTS_TOOLBAR_BUTTON_ERROR_MISSING_BUTTON', 'button.standard'));
}

/** @var RToolbarButtonStandard $button */
$button = $data['button'];
$isOption = $data['isOption'];

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
	$cmd = "if (document.adminForm.boxchecked.value==0){alert('$message');}else{ Joomla.submitbutton('$task')} return false;";
}
else
{
	$cmd = "Joomla.submitbutton('$task'); return false;";
}

// Get the button class.
$btnClass = $isOption ? '' : 'btn btn-default';

if (!empty($class))
{
	$btnClass .= ' ' . $class;
}
?>

<?php if ($isOption) :?>
	<li>
		<a href="#" class="<?php echo $btnClass ?>" onclick="<?php echo $cmd ?>">
			<?php if (!empty($iconClass)) : ?>
				<i class="<?php echo $iconClass ?>"></i>
			<?php endif; ?>
			<?php echo $text ?>
		</a>
	</li>
<?php else:?>
	<button onclick="<?php echo $cmd ?>" class="<?php echo $btnClass ?>">
		<?php if (!empty($iconClass)) : ?>
			<i class="<?php echo $iconClass ?>"></i>
		<?php endif; ?>
		<?php echo $text ?>
	</button>
<?php endif;?>
