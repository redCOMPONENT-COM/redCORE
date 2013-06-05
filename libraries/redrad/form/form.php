<?php
/**
 * @package     RedRad
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_REDRAD') or die;

/**
 * Form class.
 *
 * @package     RedRad
 * @subpackage  Form
 * @since       1.0
 */
class RForm extends JForm
{
	/**
	 * Override the validate() method to store the error per field.
	 *
	 * @param   array   $data   An array of field values to validate.
	 * @param   string  $group  The optional dot-separated form group path on which to filter the
	 *                          fields to be validated.
	 *
	 * @return  mixed  True on sucess.
	 */
	public function validate($data, $group = null)
	{
		// Make sure there is a valid JForm XML document.
		if (!($this->xml instanceof SimpleXMLElement))
		{
			return false;
		}

		$return = true;

		// Create an input registry object from the data to validate.
		$input = new JRegistry($data);

		// Get the fields for which to validate the data.
		$fields = $this->findFieldsByGroup($group);

		if (!$fields)
		{
			// PANIC!
			return false;
		}

		/** @var $field SimpleXMLElement */
		foreach ($fields as $field)
		{
			$value = null;
			$name = (string) $field['name'];

			// Get the group names as strings for ancestor fields elements.
			$attrs = $field->xpath('ancestor::fields[@name]/@name');
			$groups = array_map('strval', $attrs ? $attrs : array());
			$group = implode('.', $groups);

			// Get the value from the input data.
			if ($group)
			{
				$value = $input->get($group . '.' . $name);
			}
			else
			{
				$value = $input->get($name);
			}

			// Validate the field.
			$valid = $this->validateField($field, $group, $value, $input);

			// Check for an error.
			if ($valid instanceof Exception)
			{
				if ($group)
				{
					$this->errors[$group . '.' . $name] = $valid;
				}
				else
				{
					$this->errors[$name] = $valid;
				}

				$return = false;
			}
		}

		return $return;
	}

	/**
	 * Method to get the error for a field input.
	 *
	 * @param   string  $name   The name of the form field.
	 * @param   string  $group  The optional dot-separated form group path on which to find the field.
	 *
	 * @return  string  The form field label.
	 */
	public function getError($name, $group = null)
	{
		if ($group)
		{
			$name = $group . '.' . $name;
		}

		if (isset($this->errors[$name]))
		{
			return $this->errors[$name];
		}

		return '';
	}
}
