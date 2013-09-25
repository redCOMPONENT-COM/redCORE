<?php
/**
 * @package     Redcore
 * @subpackage  Rules
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Range date rule.
 * Can specify max date, min date or both.
 *
 * @package     Redcore
 * @subpackage  Rules
 * @since       1.0
 */
class JFormRuleRangeDate extends RFormRule
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
		// Date format.
		if (!isset($element['format']))
		{
			throw new InvalidArgumentException('No date "format" specified for the range date rule.');
		}

		try
		{
			$date = DateTime::createFromFormat($element['format'], $value);
		}
		catch (Exception $e)
		{
			throw new InvalidArgumentException('Invalid "format" specified for the range date rule.');
		}

		if (!$date instanceof DateTime)
		{
			throw new InvalidArgumentException('Invalid "format" specified for the range date rule.');
		}

		// Min and max.
		if (!isset($element['min']) && !isset($element['max']))
		{
			throw new InvalidArgumentException('No "min" or "max" value specified for the range date rule.');
		}

		// If min date specified.
		if (isset($element['min']))
		{
			try
			{
				$minDate = DateTime::createFromFormat($element['format'], $element['min']);
			}
			catch (Exception $e)
			{
				throw new InvalidArgumentException('Invalid "min" date specified for the range date rule.');
			}

			if (!$minDate instanceof DateTime)
			{
				throw new InvalidArgumentException('Invalid "min" date specified for the range date rule.');
			}

			if ($date < $minDate)
			{
				return false;
			}
		}

		// If max date specified.
		if (isset($element['max']))
		{
			try
			{
				$maxDate = DateTime::createFromFormat($element['format'], $element['max']);
			}
			catch (Exception $e)
			{
				throw new InvalidArgumentException('Invalid "max" date specified for the range date rule');
			}

			if (!$maxDate instanceof DateTime)
			{
				throw new InvalidArgumentException('Invalid "max" date specified for the range date rule');
			}

			if ($date > $maxDate)
			{
				return false;
			}
		}

		return true;
	}
}
