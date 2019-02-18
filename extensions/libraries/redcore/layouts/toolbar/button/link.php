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
	throw new InvalidArgumentException(JText::sprintf('LIB_REDCORE_LAYOUTS_TOOLBAR_BUTTON_ERROR_MISSING_BUTTON', 'button.link'));
}

/** @var RToolbarButtonLink $button */
$button = $data['button'];
$isOption = $data['isOption'];

$class = $button->getClass();
$iconClass = $button->getIconClass();
$url = $button->getUrl();
$text = $button->getText();
$extraProperties = $button->getExtraProperties();

// Get the button class.
$btnClass = $isOption ? '' : 'btn btn-default';

if (!empty($class))
{
	$btnClass .= ' ' . $class;
}

?>

<?php if ($isOption) : ?>
	<li>
		<a class="<?php echo $btnClass ?>" href="<?php echo $url ?>"<?php if ($extraProperties != '') : echo ' ' . $extraProperties; endif; ?>>
			<?php if (!empty($iconClass)) : ?>
				<i class="<?php echo $iconClass ?>"></i>
			<?php endif; ?>
			<?php echo $text ?>
		</a>
	</li>
<?php else:?>
	<button class="<?php echo $btnClass ?>" onclick="location.href='<?php echo $url ?>';"<?php if ($extraProperties != '') : echo ' ' . $extraProperties; endif; ?>>
		<?php if (!empty($iconClass)) : ?>
			<i class="<?php echo $iconClass ?>"></i>
		<?php endif; ?>
		<?php echo $text ?>
	</button>
<?php endif;?>
