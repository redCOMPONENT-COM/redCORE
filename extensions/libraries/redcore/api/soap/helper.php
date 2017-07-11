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
	public static function createSoapFaultResponse($message, $faultCode = 'env:Sender')
	{
		$errorCodeMaxCharacters = 7650;
		$message = trim((strlen($message . $faultCode) > $errorCodeMaxCharacters) ? substr($message, 0, $errorCodeMaxCharacters - 3) . '...' : $message);
		$message = "<![CDATA[" . $message . "]]>";

		return '<?xml version="1.0" encoding="UTF-8"?><env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope"><env:Body><env:Fault><env:Code>'
		. '<env:Value>' . $faultCode . '</env:Value></env:Code><env:Reason><env:Text xml:lang="en">' . $message . '</env:Text></env:Reason><env:Detail/>'
		. '</env:Fault></env:Body></env:Envelope>';
	}

	/**
	 * Returns generated WSDL file for the webservice
	 *
	 * @param   SimpleXMLElement  $webservice      Webservice configuration xml
	 * @param   string            $wsdlPath        Path of WSDL file
	 * @param   string            $webservicePath  Path to webservice file. If not specified the path from the saved webservice will be used in its place
	 *
	 * @return  SimpleXMLElement
	 */
	public static function generateWsdl($webservice, $wsdlPath = null, $webservicePath = null)
	{
		if (empty($wsdlPath) && !empty($webservice))
		{
			$client = RApiHalHelper::attributeToString($webservice, 'client', 'site');
			$version = !empty($webservice->config->version) ? (string) $webservice->config->version : '1.0.0';
			$name = (string) $webservice->config->name;

			if (!$webservicePath)
			{
				$webserviceInstance = RApiHalHelper::getInstalledWebservice($client, $name, $version);
				$wsPath = $webserviceInstance['path'];
			}
			else
			{
				$wsPath = $webservicePath;
			}

			$wsdlPath = self::getWebserviceFilePath(
					$client,
					$name,
					$version,
					'wsdl',
					$wsPath
			);
		}

		$wsdl = new RApiSoapWsdl($webservice);

		return $wsdl->generateWsdl($wsdlPath);
	}

	/**
	 * Add an element with a certain collection of fields
	 *
	 * @param   array             $fields            Array of fields to add
	 * @param   SimpleXMLElement  &$typeSchema       typeSchema to add the new elements to
	 * @param   string            $typeName          Name of the complexType to create (if $elementName is included, this is ignored)
	 * @param   boolean           $validateOptional  Optional parameter to validate if the fields are optional.  Otherwise they're always set as required
	 * @param   string            $elementName       Name of the optional element to create
	 * @param   SimpleXMLElmenet  $complexArrays     Complex arrays definitions
	 *
	 * @return  void
	 */
	public static function addElementFields($fields, &$typeSchema, $typeName, $validateOptional = false, $elementName = '', $complexArrays = null)
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
				$transformType = isset($field['transform']) ? $field['transform'] : 'string';
				$additionalFields = isset($field['fields']) ? $field['fields'] : array();
				$fieldValidateOptional = $validateOptional;

				if (preg_match('/^array\[(.+)\]$/im', $transformType, $matches))
				{
					if (isset($complexArrays->{$matches[1]}) && isset($complexArrays->{$matches[1]}->field) && count($complexArrays->{$matches[1]}->field))
					{
						$transformType = 'arraycomplex';
						$additionalFields = $complexArrays->{$matches[1]}->field;
						$fieldValidateOptional = true;
					}
					else
					{
						$transformType = 'array';
					}
				}

				$transformClass = 'RApiSoapTransform' . ucfirst(isset($transformType) ? $transformType : 'string');

				if (!class_exists($transformClass))
				{
					$transformClass = 'RApiSoapTransformBase';
				}

				$transform = new $transformClass;
				$transform->wsdlField(
					$field, $sequence, $typeSchema,
					($elementName != '' ? $elementName : $typeName),
					$fieldValidateOptional,
					$additionalFields,
					$complexArrays
				);
			}
		}
	}

	/**
	 * Returns output resources by filtering out _links and _messages
	 *
	 * @param   SimpleXMLElement  $xmlElement        Xml element
	 * @param   string            $resourceSpecific  Optionally limits the results to a certain specific resource
	 * @param   boolean           $namesOnly         Optionally create an array of names only
	 *
	 * @return  array
	 */
	public static function getOutputResources($xmlElement, $resourceSpecific = '', $namesOnly = false)
	{
		$outputResources = array();

		if (!isset($xmlElement->resources->resource))
		{
			return $outputResources;
		}

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
						if ($namesOnly)
						{
							$outputResources[] = RApiHalHelper::attributeToString($resource, 'displayName');
						}
						else
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
		$resource = new SimpleXMLElement('<resource name="result" displayName="result" transform="boolean" fieldFormat="{result}" />');

		if (!isset($xmlElement->resources->resource))
		{
			return $resource;
		}

		foreach ($xmlElement->resources->resource as $xmlResource)
		{
			$displayName = RApiHalHelper::attributeToString($xmlResource, 'displayName');
			$resourceSpecific = RApiHalHelper::attributeToString($xmlResource, 'resourceSpecific');

			if ($displayName != 'result' || $resourceSpecific != 'rcwsGlobal')
			{
				continue;
			}

			$xmlResource->addAttribute('name', $resource['displayName']);
			$resource = $xmlResource;
		}

		return $resource;
	}

	/**
	 * Method to determine the wsdl file name
	 *
	 * @param   string  $client          Client
	 * @param   string  $webserviceName  Name of the webservice
	 * @param   string  $version         Suffixes to the file name (ex. 1.0.0)
	 * @param   string  $extension       Extension of the file to search
	 * @param   string  $path            Path to webservice files
	 *
	 * @return  string  The full path to the api file
	 *
	 * @since   1.4
	 */
	public static function getWebserviceFilePath($client, $webserviceName, $version = '', $extension = 'xml', $path = '')
	{
		JLoader::import('joomla.filesystem.path');

		if (empty($webserviceName))
		{
			return '';
		}

		$version = !empty($version) ? JPath::clean($version) : '1.0.0';
		$webservicePath = !empty($path) ? RApiHalHelper::getWebservicesRelativePath() . '/' . $path : RApiHalHelper::getWebservicesRelativePath();

		$rawPath = $webserviceName . '.' . $version;
		$rawPath = !empty($extension) ? $rawPath . '.' . $extension : $rawPath;
		$rawPath = !empty($client) ? $client . '.' . $rawPath : $rawPath;

		return $webservicePath . '/' . $rawPath;
	}

	/**
	 * Select resources from output array to display them in SOAP output list
	 *
	 * @param   array  $outputResources  Selected output resources from the ws xml config file
	 * @param   array  $items            Output resources with final values
	 *
	 * @return  array  Array of selected resources and value in simple array for SOAP output
	 *
	 * @since   1.4
	 */
	public static function selectListResources($outputResources, $items)
	{
		$response = array();

		if (empty($items))
		{
			return $response;
		}

		foreach ($items as $item)
		{
			$object = new stdClass;

			foreach ($item as $field => $value)
			{
				if (in_array($field, $outputResources))
				{
					$object->$field = $value;
				}
			}

			$response[] = $object;
		}

		return $response;
	}

	/**
	 * Gets an array of fields ready for SOAP documentation purposes
	 *
	 * @param   array    $fields       Array of fields using their xml properties (using 'name' for the field name itself)
	 * @param   boolean  $allRequired  Mark all the fields as required
	 * @param   string   $assignation  Assignation operation
	 *
	 * @return  array
	 *
	 * @since   1.4
	 */
	public static function documentationFields($fields, $allRequired = false, $assignation = '=')
	{
		$fieldsArray = array();

		if (empty($fields) || !is_array($fields))
		{
			return $fieldsArray;
		}

		foreach ($fields as $field)
		{
			$transform = RApiHalHelper::attributeToString($field, 'transform', 'string');
			$defaultValue = RApiHalHelper::attributeToString($field, 'defaultValue', 'null');

			if ($defaultValue == 'null' && ($allRequired || RApiHalHelper::isAttributeTrue($field, 'isRequiredField')))
			{
				$transformClass = 'RApiSoapTransform' . ucfirst($transform);

				if (!class_exists($transformClass))
				{
					$transformClass = 'RApiSoapTransformBase';
				}

				$transformObject = new $transformClass;
				$defaultValue = $transformObject->defaultValue;
			}

			$fieldsArray[] = '$' .
				RApiHalHelper::attributeToString($field, 'name') .
				' ' . $assignation . ' (' . $transform . ') ' .
				$defaultValue;
		}

		return $fieldsArray;
	}

	/**
	 * Recreate or create new SOAP WSDL files
	 *
	 * @param   string  $path  Path to the webservices folder
	 *
	 * @return  void
	 */
	public static function generateWsdlFromFolder($path)
	{
		if (!$handle = opendir($path))
		{
			return;
		}

		while (false !== ($entry = readdir($handle)))
		{
			if ($entry == "." || $entry == "..")
			{
				continue;
			}

			$defaultPath = JPath::clean(RApiHalHelper::getWebservicesPath());
			$webservicePath = str_replace($defaultPath . '/', '', $path);
			$file = $path . '/' . $entry;

			if (is_dir($file))
			{
				self::generateWsdlFromFolder($file);
			}
			elseif (is_file($file))
			{
				// Only handle XML files
				if (JFile::getExt($file) == 'xml')
				{
					$content = @file_get_contents($file);

					if (is_string($content))
					{
						$webserviceXml = new SimpleXMLElement($content);
						$wsdl = self::generateWsdl($webserviceXml, null, $webservicePath);
						$fullWsdlPath = substr($file, 0, -4) . '.wsdl';

						// Save the generated WSDL file
						self::saveWsdlContentToPath($wsdl, $fullWsdlPath);
					}
				}
			}
		}

		closedir($handle);
	}

	/**
	 * Save WSDL file in correct format
	 *
	 * @param   SimpleXMLElement  $wsdl  WSDL Xml element
	 * @param   string            $path  Path to the webservices folder
	 *
	 * @return  boolean
	 */
	public static function saveWsdlContentToPath($wsdl, $path)
	{
		$domWsdl = dom_import_simplexml($wsdl)->ownerDocument;
		$domWsdl->preserveWhiteSpace = false;
		$domWsdl->formatOutput = true;

		return $domWsdl->save($path);
	}
}
