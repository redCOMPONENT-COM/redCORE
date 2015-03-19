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
}
