<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

extract($displayData);

/**
 * Layout variables
 * ---------------------
 * 	$text         : (string)  The label text
 * 	$description  : (string)  An optional description to use in a tooltip
 * 	$for          : (string)  The id of the input this label is for
 * 	$required     : (boolean) True if a required field
 * 	$classes      : (array)   A list of classes
 * 	$position     : (string)  The tooltip position. Bottom for alias
 */

$classes = array_filter((array) $classes);

$id = $for . '-lbl';
$title = '';

if (!empty($description))
{
	$classes[] = 'hasTooltip';

	if (defined('REDCORE_BOOTSTRAPPED') && version_compare(JVERSION, '3.0', '<'))
	{
		JHtml::_('rbootstrap.tooltip', '.hasTooltip');
		$title = ' title="' . RHtml::tooltipText(trim($text, ':'), $description, 0) . '"';
	}
	elseif (version_compare(JVERSION, '3.0', '<'))
	{
		JHTML::_('behavior.tooltip', '.hasTooltip', array('placement' => 'right'));
		$title = ' title="' . (trim($text . '::' . $description)) . '"';
	}
	else
	{
		JHtml::_('rbootstrap.tooltip', '.hasTooltip');
		$title     = ' title="' . JHtml::tooltipText(trim($text, ':'), $description, 0) . '"';
	}
}

if ($required)
{
	$classes[] = 'required';
}

?>
<label id="<?php echo $id; ?>" for="<?php echo $for; ?>"<?php if (!empty($classes)) echo ' class="' . implode(' ', $classes) . '"'; ?><?php echo $title; ?><?php echo $position; ?>>
	<?php echo $text; ?><?php if ($required) : ?><span class="star">&#160;*</span><?php endif; ?>
</label>
