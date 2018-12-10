<?php
/**
 * @package     Redcore
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Base form class.
 *
 * @package     Redcore
 * @subpackage  Form
 * @since       1.0
 */
abstract class RFormBase extends JForm
{
	/**
	 * Method to get an instance of a form.
	 *
	 * @param   string          $name     The name of the form.
	 * @param   string          $data     The name of an XML file or string to load as the form definition.
	 * @param   array           $options  An array of form options.
	 * @param   boolean         $replace  Flag to toggle whether form fields should be replaced if a field
	 *                                    already exists with the same group/name.
	 * @param   string|boolean  $xpath    An optional xpath to search for the fields.
	 *
	 * @return  JForm  JForm instance.
	 *
	 * @since   11.1
	 * @throws  InvalidArgumentException if no data provided.
	 * @throws  RuntimeException if the form could not be loaded.
	 */
	public static function getInstance($name, $data = null, $options = array(), $replace = true, $xpath = false)
	{
		// Reference to array with form instances
		/** @var  RForm[] $forms */
		$forms = &self::$forms;

		// Only instantiate the form if it does not already exist.
		if (!isset($forms[$name]))
		{
			$data = trim($data);

			if (empty($data))
			{
				throw new InvalidArgumentException(sprintf('RForm::getInstance(name, *%s*)', gettype($data)));
			}

			// Instantiate the form.
			$forms[$name] = new static($name, $options);

			// Load the data.
			if (substr(trim($data), 0, 1) == '<')
			{
				if ($forms[$name]->load($data, $replace, $xpath) == false)
				{
					throw new RuntimeException('RForm::getInstance could not load form');
				}
			}
			else
			{
				if ($forms[$name]->loadFile($data, $replace, $xpath) == false)
				{
					throw new RuntimeException('RForm::getInstance could not load file');
				}
			}
		}

		return $forms[$name];
	}

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
	 * @return  string  The form field error.
	 */
	public function getError($name, $group = null)
	{
		if (!empty($group))
		{
			$name = $group . '.' . $name;
		}

		if (isset($this->errors[$name]))
		{
			return $this->errors[$name];
		}

		return '';
	}

	/**
	 * Returns the value of an attribute of the form itself
	 *
	 * @param   string  $name     Name of the attribute to get
	 * @param   mixed   $default  Optional value to return if attribute not found
	 *
	 * @return  mixed             Value of the attribute / default
	 *
	 * @since   3.2
	 */
	public function getAttribute($name, $default = null)
	{
		if ($this->xml instanceof SimpleXMLElement)
		{
			$attributes = $this->xml->attributes();

			// Ensure that the attribute exists
			if (property_exists($attributes, $name))
			{
				$value = $attributes->$name;

				if ($value !== null)
				{
					return (string) $value;
				}
			}
		}

		return $default;
	}

	/**
	 * Method to get the XML form object
	 *
	 * @return  SimpleXMLElement  The form XML object
	 *
	 * @since   3.2
	 */
	public function getXml()
	{
		return $this->xml;
	}
}
