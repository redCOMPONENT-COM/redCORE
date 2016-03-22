<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$text          = $displayData['text'];
$active        = $displayData['active'];
$ajaxJS        = $displayData['ajaxJS'];
$numberOfPages = (int) $displayData['numberOfPages'];
$currentPage   = (int) $displayData['currentPage'];

switch ((string) $text)
{
	// Check for "Start" item
	case JText::_('JLIB_HTML_START') :
		$icon   = "icon-first";
		$moveTo = 1;
		break;

	// Check for "Prev" item
	case $text == JText::_('JPREVIOUS') :
		$icon   = "icon-previous";
		$moveTo = $currentPage - 1;
		break;

	// Check for "Next" item
	case JText::_('JNEXT') :
		$icon   = "icon-next";
		$moveTo = $currentPage + 1;
		break;

	// Check for "End" item
	case JText::_('JLIB_HTML_END') :
		$moveTo = $numberOfPages;
		$icon   = "icon-last";
		break;

	default:
		$icon   = null;
		$moveTo = $text;
		break;
}

if ($icon !== null)
{
	$display = '<i class="' . $icon . '"></i>';
}
else
{
	$display = $text;
}

if ($active)
{
	$cssClasses = array();

	$title = '';

	if (!is_numeric($text))
	{
		JHtml::_('bootstrap.tooltip');
		$cssClasses[] = 'hasTooltip';
		$title = ' title="' . $text . '" ';
	}

	$onClick = $ajaxJS . '(' . $moveTo . ');';
}
else
{
	$class = 'disabled';
}
?>
<?php if ($active) : ?>
	<li>
		<a class="<?php echo implode(' ', $cssClasses); ?>" <?php echo $title; ?> href="#" onclick="<?php echo $onClick; ?>">
			<?php echo $display; ?>
		</a>
	</li>
<?php else : ?>
	<li class="<?php echo $class; ?>">
		<span><?php echo $display; ?></span>
	</li>
<?php endif;
