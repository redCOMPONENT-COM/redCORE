<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$item = $displayData;

$display = $item->text;

switch ((string) $item->text)
{
	// Check for "Start" item
	case JText::_('JLIB_HTML_START') :
		$icon = "icon-backward";
		break;

	// Check for "Prev" item
	case $item->text == JText::_('JPREV') :
		$item->text = JText::_('JPREVIOUS');
		$icon = "icon-step-backward";
		break;

	// Check for "Next" item
	case JText::_('JNEXT') :
		$icon = "icon-step-forward";
		break;

	// Check for "End" item
	case JText::_('JLIB_HTML_END') :
		$icon = "icon-forward";
		break;

	default:
		$icon = null;
		break;
}

if ($icon !== null)
{
	$display = '<i class="' . $icon . '"></i>';
}

$class = (property_exists($item, 'active') && $item->active) ? 'active' : 'disabled';
?>
<li class="<?php echo $class; ?>">
	<span><?php echo $display; ?></span>
</li>
