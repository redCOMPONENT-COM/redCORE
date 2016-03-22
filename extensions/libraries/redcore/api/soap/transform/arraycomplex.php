<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * Interface to transform api output
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.4
 */
final class RApiSoapTransformArraycomplex extends RApiSoapTransformBase
{
	/**
	 * string type
	 *
	 * @var    string  Base SOAP type
	 * @since  1.4
	 */
	public $type = '';

	/**
	 * Method to transform a type to publish it in the WSDL file
	 *
	 * @param   array             $field             Field definition.
	 * @param   SimpleXMLElement  &$sequence         XML with the fields sequence
	 * @param   SimpleXMLElement  &$typeSchema       XML of the typeSchema in case new derived types need to be added
	 * @param   string            $elementName       Parent element name to add the new derived types with unique names
	 * @param   boolean           $validateOptional  Optional parameter to validate if the field is optional.  Otherwise it's always set as required
	 * @param   array             $extraFields       Array of extra fields to process - in case of array types
	 * @param   SimpleXMLElmenet  $complexArrays     Complex arrays definitions
	 *
	 * @return void
	 */
	public function wsdlField(
		$field, &$sequence, &$typeSchema, $elementName, $validateOptional = false, $extraFields = array(), $complexArrays = null)
	{
		if (!isset($this->element))
		{
			$this->element = $sequence->addChild('element', null, 'http://www.w3.org/2001/XMLSchema');
		}

		if (!isset($this->element['minOccurs']))
		{
			$this->element->addAttribute(
				'minOccurs',
				(($validateOptional && RApiHalHelper::isAttributeTrue($field, 'isRequiredField') || !$validateOptional) ? '1' : '0')
			);
		}

		$this->element->addAttribute('maxOccurs', 'unbounded');

		if (!isset($this->element['name']) && isset($field['name']))
		{
			$this->element->addAttribute('name', $field['name']);
		}

		$this->element->addAttribute('type', 'tns:' . $elementName . '_' . $field['name']);

		if (!empty($extraFields))
		{
			RApiSoapHelper::addElementFields($extraFields, $typeSchema, $elementName . '_' . $field['name'], true, '', $complexArrays);
		}
	}
}
