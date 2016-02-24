<?php
/**
 * @package     Redcore
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
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
			$forms[$name] = new RForm($name, $options);

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
	 * Method to get the error for a field input.
	 *
	 * @param   string  $name   The name of the form field.
	 * @param   string  $group  The optional dot-separated form group path on which to find the field.
	 *
	 * @return  string  The form field error.
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

	/**
	 * Returns the value of an attribute of the form itself
	 *
	 * @param   string  $attribute  The name of the attribute
	 * @param   mixed   $default    Optional default value to return
	 *
	 * @return  mixed  The attribute value.
	 */
	public function getAttribute($attribute, $default = null)
	{
		$value = $this->xml->attributes()->$attribute;

		if (is_null($value))
		{
			return $default;
		}
		else
		{
			return (string) $value;
		}
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
