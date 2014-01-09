<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

JHtml::_('script', 'system/html5fallback.js', false, true);
JHtml::_('behavior.colorpicker');

$data       = (object) $displayData;
$attributes = array();
$hint       = $data->field->translateHint ? JText::_($data->field->hint) : $data->field->hint;
$color      = strtolower($data->field->value);

$attributes['id']            = $data->id;
$attributes['class']         = $data->element['class'] ? (string) trim('minicolors ' . $data->element['class']) : 'minicolors';
$attributes['size']          = $data->element['size'] ? (int) $data->element['size'] : 7;
$attributes['required']      = $data->required ? 'required' : null;
$attributes['aria-required'] = $data->required ? 'true' : null;
$attributes['onchange']      = $data->element['onchange'] ? (string) $data->element['onchange'] : null;
$attributes['data-control']  = $data->element['control'] ? $data->element['control'] : null;
$attributes['placeholder']   = $hint ? $hint : '#rrggbb';
$attributes['autofocus']     = $data->element['autofocus'] ? (string) $data->element['autofocus'] : null;
$attributes['autocomplete']  = ($data->element['autocomplete'] == 'false') ? 'off' : '';

if ((string) $data->element['readonly'] == 'true' || (string) $data->element['disabled'] == 'true')
{
	$attributes['readonly'] = ($data->element['readonly'] == 'true') ? 'true' : null;
	$attributes['disabled'] = 'disabled';
}

$renderedAttributes = null;

if ($attributes)
{
	foreach ($attributes as $attribute => $value)
	{
		if (null !== $value)
		{
			$renderedAttributes .= ' ' . $attribute . '="' . (string) $value . '"';
		}
	}
}

if (!$color || in_array($color, array('none', 'transparent')))
{
	$color = 'none';
}
elseif ($color['0'] != '#')
{
	$color = '#' . $color;
}
$value = htmlspecialchars($color, ENT_COMPAT, 'UTF-8');
?>
<input type="text" style="padding: 4px 6px 4px 30px;"
		name="<?php echo $data->name; ?>"
		value="<?php echo $value; ?>"
		<?php echo $renderedAttributes; ?> />