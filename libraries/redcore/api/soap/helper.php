<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Helper class for SOAP calls
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.4
 */
class RApiSoapHelper
{
	/**
	 * Returns generated WSDL file for the webservice
	 *
	 * @param   string  $message    Message for the soap Fault
	 * @param   string  $faultCode  Fault code for soap response
	 *
	 * @return  string
	 */
	public static function createSoapFaultResponse($message, $faultCode = 'SOAP-ENV:Server')
	{
		return '<?xml version="1.0" encoding="UTF-8"?>
			<SOAP-ENV:Envelope
			    xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
			    <SOAP-ENV:Body>
			        <SOAP-ENV:Fault>
			            <faultcode>' . $faultCode . '</faultcode>
			            <faultstring>' . $message . '</faultstring>
			        </SOAP-ENV:Fault>
			    </SOAP-ENV:Body>
			</SOAP-ENV:Envelope>';
	}

	/**
	 * Returns generated WSDL file for the webservice
	 *
	 * @param   SimpleXMLElement  $webservice  Webservice configuration xml
	 *
	 * @return  SimpleXMLElement
	 */
	public static function generateWsdl($webservice)
	{
		$wsdl = new RApiSoapWsdl($webservice);

		return $wsdl->generateWsdl();
	}

	/**
	 * Add an element with a certain collection of fields
	 *
	 * @param   array             $fields            Array of fields to add
	 * @param   SimpleXMLElement  &$typeSchema       typeSchema to add the new elements to
	 * @param   string            $typeName          Name of the complexType to create (if $elementName is included, this is ignored)
	 * @param   boolean           $validateOptional  Optional parameter to validate if the fields are optional.  Otherwise they're always set as required
	 * @param   string            $elementName       Name of the optional element to create
	 *
	 * @return  void
	 */
	public static function addElementFields($fields, &$typeSchema, $typeName, $validateOptional = false, $elementName = '')
	{
		if ($elementName != '')
		{
			// Element
			$element = $typeSchema->addChild('element', null, 'http://www.w3.org/2001/XMLSchema');
			$element->addAttribute('name', $elementName);

			// Complex type
			$complexType = $element->addChild('complexType', null, 'http://www.w3.org/2001/XMLSchema');
		}
		else
		{
			// Complex type
			$complexType = $typeSchema->addChild('complexType', null, 'http://www.w3.org/2001/XMLSchema');
			$complexType->addAttribute('name', $typeName);
		}

		if ($fields && !empty($fields))
		{
			// Sequence
			$sequence = $complexType->addChild('sequence', null, 'http://www.w3.org/2001/XMLSchema');

			foreach ($fields as $field)
			{
				$transformClass = 'RApiSoapTransform' . ucfirst(isset($field['transform']) ? $field['transform'] : 'string');

				if (!class_exists($transformClass))
				{
					$transformClass = 'RApiSoapTransformBase';
				}

				$transform = new $transformClass;
				$transform->wsdlField(
					$field, $sequence, $typeSchema,
					($elementName != '' ? $elementName : $typeName),
					$validateOptional,
					(isset($field['fields']) ? $field['fields'] : array())
				);
			}
		}
	}

	/**
	 * Returns output resources by filtering out _links and _messages
	 *
	 * @param   SimpleXMLElement  $xmlElement        Xml element
	 * @param   string            $resourceSpecific  Optionally limits the results to a certain specific resource
	 *
	 * @return  array
	 */
	public static function getOutputResources($xmlElement, $resourceSpecific = '')
	{
		$outputResources = array();

		if (isset($xmlElement->resources->resource))
		{
			foreach ($xmlElement->resources->resource as $resource)
			{
				$displayGroup = RApiHalHelper::attributeToString($resource, 'displayGroup');

				switch ($displayGroup)
				{
					case '_links':
					case '_messages':
						break;

					default:
						if (($resourceSpecific != '' && RApiHalHelper::attributeToString($resource, 'resourceSpecific') == $resourceSpecific)
							|| $resourceSpecific == '')
						{
							$resource->addAttribute('name', $resource['displayName']);
							$outputResources[] = $resource;
						}
				}
			}
		}

		return $outputResources;
	}

	/**
	 * Returns the resoult resource from a certain operation
	 *
	 * @param   SimpleXMLElement  $xmlElement  Xml element
	 *
	 * @return  array
	 */
	public static function getResultResource($xmlElement)
	{
		if (isset($xmlElement->resources->resource))
		{
			foreach ($xmlElement->resources->resource as $resource)
			{
				$displayName = RApiHalHelper::attributeToString($resource, 'displayName');
				$resourceSpecific = RApiHalHelper::attributeToString($resource, 'resourceSpecific');

				if ($displayName == 'result' && $resourceSpecific == 'rcwsGlobal')
				{
					$resource->addAttribute('name', $resource['displayName']);

					return $resource;
				}
			}
		}

		$resource = new SimpleXMLElement('<resource name="result" displayName="result" transform="array" fieldFormat="{result}" />');

		return $resource;
	}
}
