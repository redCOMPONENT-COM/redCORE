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

if ($item->base > 0)
{
	$limit = 'limitstart.value=' . $item->base;
}
else
{
	$limit = 'limitstart.value=0';
}

$cssClasses = array();

$title = '';

if (!is_numeric($item->text))
{
	JHtml::_('rbootstrap.tooltip');
	$cssClasses[] = 'hasTooltip';
	$title = ' title="' . $item->text . '" ';
}

$onClick = "document." . $item->formName . "." . $item->prefix . $limit . "; Joomla.submitform(document.forms['" . $item->formName . "'].task.value, document.forms['" . $item->formName . "']);return false;";
?>
<li>
	<a class="<?php echo implode(' ', $cssClasses); ?>" <?php echo $title; ?> href="#" onclick="<?php echo $onClick; ?>">
		<?php echo $display; ?>
	</a>
</li>
