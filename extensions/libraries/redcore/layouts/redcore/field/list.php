<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$data = (object) $displayData;

$attributes = array();

$attributes['id']            = $data->id;
$attributes['class']         = $data->element['class'] ? (string) $data->element['class'] : null;
$attributes['size']          = $data->element['size'] ? (int) $data->element['size'] : null;
$attributes['multiple']      = $data->multiple ? 'multiple' : null;
$attributes['required']      = $data->required ? 'required' : null;
$attributes['aria-required'] = $data->required ? 'true' : null;
$attributes['onchange']      = $data->element['onchange'] ? (string) $data->element['onchange'] : null;

if ((string) $data->element['readonly'] == 'true' || (string) $data->element['disabled'] == 'true')
{
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

$readOnly = ((string) $data->element['readonly'] == 'true');

// If it's readonly the select will have no name
$selectName = $readOnly ? '' : $data->name;
?>

<select name="<?php echo $selectName; ?>" <?php echo $renderedAttributes; ?>>
	<?php if ($data->options) : ?>
		<?php foreach ($data->options as $option) :?>
				<option
					value="<?php echo $option->value; ?>"
					<?php if ((is_array($data->value) && in_array($option->value, $data->value)) || (!is_array($data->value) && (string) $option->value === (string) $data->value)):
						echo 'selected="selected"';
					endif; ?>><?php echo $option->text; ?></option>
		<?php endforeach; ?>
	<?php endif; ?>
</select>
<?php if ((string) $data->element['readonly'] == 'true') : ?>
	<input type="hidden" name="<?php echo $data->name; ?>" value="<?php echo $data->value; ?>"/>
<?php endif;
