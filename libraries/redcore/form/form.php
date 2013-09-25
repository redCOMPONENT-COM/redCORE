<?php
/**
 * @package     Redcore
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

if (version_compare(JVERSION, '3.0', 'lt'))
{
	/**
	 * Form class.
	 *
	 * @package     Redcore
	 * @subpackage  Form
	 * @since       1.0
	 */
	class RForm extends RFormBase
	{
		/**
		 * Method to validate a JFormField object based on field data.
		 *
		 * @param   SimpleXMLElement  $element  The XML element object representation of the form field.
		 * @param   string            $group    The optional dot-separated form group path on which to find the field.
		 * @param   mixed             $value    The optional value to use as the default for the field.
		 * @param   JRegistry         $input    An optional JRegistry object with the entire data set to validate
		 *                                      against the entire form.
		 *
		 * @return  mixed  Boolean true if field value is valid, Exception on failure.
		 *
		 * @throws  InvalidArgumentException
		 * @throws  UnexpectedValueException
		 */
		protected function validateField($element, $group = null, $value = null, $input = null)
		{
			$valid = true;

			// Check if the field is required.
			$required = ((string) $element['required'] == 'true' || (string) $element['required'] == 'required');

			if ($required)
			{
				// If the field is required and the value is empty return an error message.
				if (($value === '') || ($value === null))
				{
					if ($element['label'])
					{
						$message = JText::_($element['label']);
					}
					else
					{
						$message = JText::_($element['name']);
					}

					$message = JText::sprintf('JLIB_FORM_VALIDATE_FIELD_REQUIRED', $message);

					return new RuntimeException($message);
				}
			}

			// Get the field validation rule.
			if ($type = (string) $element['validate'])
			{
				// Load the JFormRule object for the field.
				$rule = $this->loadRuleType($type);

				// If the object could not be loaded return an error message.
				if ($rule === false)
				{
					throw new UnexpectedValueException(sprintf('%s::validateField() rule `%s` missing.', get_class($this), $type));
				}

				// Run the field validation rule test.
				$valid = $rule->test($element, $value, $group, $input, $this);

				// Check for an error in the validation test.
				if ($valid instanceof Exception)
				{
					return $valid;
				}
			}

			// Check if the field is valid.
			if ($valid === false)
			{
				// Does the field have a defined error message?
				$message = (string) $element['message'];

				if ($message)
				{
					$message = JText::_($element['message']);

					// Trick to use attributes as an array
					$tags = current($element->attributes());
					$tags['value'] = $value;

					$message = RText::replace($message, $tags);

					return new UnexpectedValueException($message);
				}
				else
				{
					$message = JText::_($element['label']);
					$message = JText::sprintf('JLIB_FORM_VALIDATE_FIELD_INVALID', $message);

					return new UnexpectedValueException($message);
				}
			}

			return true;
		}
	}
}

else
{
	/**
	 * Form class.
	 *
	 * @package     Redcore
	 * @subpackage  Form
	 * @since       1.0
	 */
	class RForm extends RFormBase
	{
		/**
		 * Method to validate a JFormField object based on field data.
		 *
		 * @param   SimpleXMLElement  $element  The XML element object representation of the form field.
		 * @param   string            $group    The optional dot-separated form group path on which to find the field.
		 * @param   mixed             $value    The optional value to use as the default for the field.
		 * @param   JRegistry         $input    An optional JRegistry object with the entire data set to validate
		 *                                      against the entire form.
		 *
		 * @return  mixed  Boolean true if field value is valid, Exception on failure.
		 *
		 * @throws  InvalidArgumentException
		 * @throws  UnexpectedValueException
		 */
		protected function validateField(SimpleXMLElement $element, $group = null, $value = null, JRegistry $input = null)
		{
			$valid = true;

			// Check if the field is required.
			$required = ((string) $element['required'] == 'true' || (string) $element['required'] == 'required');

			if ($required)
			{
				// If the field is required and the value is empty return an error message.
				if (($value === '') || ($value === null))
				{
					if ($element['label'])
					{
						$message = JText::_($element['label']);
					}
					else
					{
						$message = JText::_($element['name']);
					}

					$message = JText::sprintf('JLIB_FORM_VALIDATE_FIELD_REQUIRED', $message);

					return new RuntimeException($message);
				}
			}

			// Get the field validation rule.
			if ($type = (string) $element['validate'])
			{
				// Load the JFormRule object for the field.
				$rule = $this->loadRuleType($type);

				// If the object could not be loaded return an error message.
				if ($rule === false)
				{
					throw new UnexpectedValueException(sprintf('%s::validateField() rule `%s` missing.', get_class($this), $type));
				}

				// Run the field validation rule test.
				$valid = $rule->test($element, $value, $group, $input, $this);

				// Check for an error in the validation test.
				if ($valid instanceof Exception)
				{
					return $valid;
				}
			}

			// Check if the field is valid.
			if ($valid === false)
			{
				// Does the field have a defined error message?
				$message = (string) $element['message'];

				if ($message)
				{
					$message = JText::_($element['message']);

					// Trick to use attributes as an array
					$tags = current($element->attributes());
					$tags['value'] = $value;

					$message = RText::replace($message, $tags);

					return new UnexpectedValueException($message);
				}
				else
				{
					$message = JText::_($element['label']);
					$message = JText::sprintf('JLIB_FORM_VALIDATE_FIELD_INVALID', $message);

					return new UnexpectedValueException($message);
				}
			}

			return true;
		}
	}
}
