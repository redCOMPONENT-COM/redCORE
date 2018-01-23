<?php
/**
 * @package     Redcore
 * @subpackage  Rules
 *
 * @copyright   Copyright (C) 2012 - 2018 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Range value rule.
 * Can specify max value, min value or both.
 *
 * @package     Redcore
 * @subpackage  Rules
 * @since       1.0
 */
class JFormRuleRangeValue extends RFormRule
{
	/**
	 * Method to test if two values are not equal. To use this rule, the form
	 * XML needs a validate attribute of equals and a field attribute
	 * that is equal to the field to test against.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 * @param   JRegistry         $input    An optional JRegistry object with the entire data set to validate against the entire form.
	 * @param   JForm             $form     The form object for which the field is being tested.
	 *
	 * @return  boolean  True if the value is valid, false otherwise.
	 *
	 * @throws  InvalidArgumentException
	 * @throws  UnexpectedValueException
	 */
	public function test(SimpleXMLElement $element, $value, $group = null, JRegistry $input = null, JForm $form = null)
	{
		if (!isset($element['min']) && !isset($element['max']))
		{
			throw new InvalidArgumentException('No "min" or "max" value specified for the range value rule.');
		}

		$value = (float) $value;

		// If min lenght specified.
		if (isset($element['min']))
		{
			if ($value < (float) $element['min'])
			{
				return false;
			}
		}

		// If max lenght specified.
		if (isset($element['max']))
		{
			if ($value > (float) $element['max'])
			{
				return false;
			}
		}

		return true;
	}
}
