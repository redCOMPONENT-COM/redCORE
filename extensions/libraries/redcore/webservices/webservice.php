<?php
/**
 * @package     Redcore
 * @subpackage  Factory
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Class for interacting with webservices.
 *
 * @package     Redcore
 * @subpackage  webservice
 * @since       1.8
 */
class RWebservicesWebservice extends RWebservicesBase
{
	/**
	 * Executes Query from remote server location
	 *
	 * @param   string  $url      The URL for the request.
	 * @param   mixed   $data     The data to include in the request
	 * @param   array   $headers  The headers to send with the request
	 * @param   string  $method   The method with which to send the request
	 * @param   int     $timeout  The timeout for the request
	 *
	 * @return  array  Response
	 *
	 * @since   1.8
	 * @throws  InvalidArgumentException
	 * @throws  RuntimeException
	 */
	public function executeRemoteQuery($url, $data = null, $headers = array(), $method = 'get', $timeout = null)
	{
		if ($this->getOption('authorizationtype', 'token') == 'token')
		{
			$token = $this->getToken();

			if ($token)
			{
				if ($this->getOption('authmethod', 'bearer') == 'bearer')
				{
					$headers['Authorization'] = 'Bearer ' . $token['access_token'];
				}
				elseif ($this->getOption('authmethod', 'bearer') == 'get')
				{
					if (strpos($url, '?'))
					{
						$url .= '&';
					}
					else
					{
						$url .= '?';
					}

					$url .= $this->getOption('tokenparam', 'access_token') . '=' . $token['access_token'];
				}
			}
		}
		elseif ($this->getOption('authorizationtype', 'token') == 'basic')
		{
			$basic = $this->getOption('authorization.basic');

			if (!$basic)
			{
				$basic = base64_encode($this->getOption('authorization.username') . ':' . $this->getOption('authorization.password'));
			}

			$headers['Authorization'] = 'Basic ' . $basic;
		}

		if ($this->getOption('enableRequestCompression', ''))
		{
			$headers['Content-Encoding'] = 'gzip';
		}

		if ($this->getOption('enableResponseCompression', ''))
		{
			$headers['Accept-Encoding'] = 'gzip';
		}

		switch ($method)
		{
			case 'head':
			case 'get':
			case 'delete':
			case 'trace':
				$response = $this->http->$method($url, $headers, $timeout);
				break;
			case 'post':
			case 'put':
			case 'patch':
				$response = $this->http->$method($url, $data, $headers, $timeout);
				break;
			default:
				throw new InvalidArgumentException('Unknown HTTP request method: ' . $method . '.');
		}

		if ($response->code < 200 || $response->code >= 400)
		{
			throw new RuntimeException('Error code ' . $response->code . ' received requesting data: ' . $response->body . '.');
		}

		if ($this->getOption('responseconverttoarray', true))
		{
			if ($response->headers['Content-Type'] == 'application/json' || $response->headers['Content-Type'] == 'application/hal+json; charset=UTF-8')
			{
				$response->body = json_decode($response->body, true);
			}
		}

		return $response;
	}
}
