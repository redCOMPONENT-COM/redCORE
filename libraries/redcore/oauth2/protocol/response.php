<?php
/**
 * @package     Redcore
 * @subpackage  OAuth2
 *
 * This work is based on a Louis Landry work about oauth1 server suport for Joomla! Platform.
 * URL: https://github.com/LouisLandry/joomla-platform/tree/9bc988185ccc3e1c437256cc2c927e49312b3d00/libraries/joomla/oauth1
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die( 'Restricted access' );

/**
 * OAuth2 response data object class.
 *
 * @package     Redcore
 * @subpackage  OAuth2
 * @since       1.0
 */
class ROauth2ProtocolResponse
{
	/**
	 * @var    integer  The server response code.
	 * @since  1.0
	 */
	public $code;

	/**
	 * @var    array  Response description.
	 * @since  1.0
	 */
	public $description;

	/**
	 * @var    string  Server response body.
	 * @since  1.0
	 */
	public $uri;

	/**
	 * @var    array  Response headers.
	 * @since  1.0
	 */
	public $headers = array();

	/**
	 * @var    string  MimeType.
	 * @since  1.0
	 */
	public $mimeType;

	/**
	 * @var    string  MimeType.
	 * @since  1.0
	 */
	public $charSet;

	/**
	 * @var    bool  Cachable.
	 * @since  1.0
	 */
	public $cachable = false;

	/**
	 * Method to send the application response to the client.  All headers will be sent prior to the main
	 * application output data.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function respond()
	{
		// Send the content-type header.
		$this->setHeader('Content-Type', $this->mimeType . '; charset=' . $this->charSet);

		// If the response is set to uncachable, we need to set some appropriate headers so browsers don't cache the response.
		if (!$this->cachable)
		{
			// Expires in the past.
			$this->setHeader('Expires', 'Mon, 1 Jan 2001 00:00:00 GMT', true);

			// Always modified.
			$this->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT', true);
			$this->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0', false);

			// HTTP 1.0
			$this->setHeader('Pragma', 'no-cache');
		}
		else
		{
			// Expires.
			$this->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 900) . ' GMT');

			// Last modified.
			if ($this->modifiedDate instanceof JDate)
			{
				$this->setHeader('Last-Modified', $this->modifiedDate->format('D, d M Y H:i:s'));
			}
		}

		$this->setHeader('X-Powered-By', 'JoomlaWebAPI/1.0', true);

		$this->sendHeaders();

		echo $this->getBody();
	}

	/**
	 * Method to send a header to the client.  We are wrapping this to isolate the header() function
	 * from our code base for testing reasons.
	 *
	 * @param   string   $string   The header string.
	 * @param   boolean  $replace  The optional replace parameter indicates whether the header should
	 *                             replace a previous similar header, or add a second header of the same type.
	 * @param   integer  $code     Forces the HTTP response code to the specified value. Note that
	 *                             this parameter only has an effect if the string is not empty.
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 * @see     header()
	 * @since   1.0
	 */
	protected function header($string, $replace = true, $code = null)
	{
		header($string, $replace, $code);
	}

	/**
	 * Method to set a response header.  If the replace flag is set then all headers
	 * with the given name will be replaced by the new one.  The headers are stored
	 * in an internal array to be sent when the site is sent to the browser.
	 *
	 * @param   string   $name     The name of the header to set.
	 * @param   string   $value    The value of the header to set.
	 * @param   boolean  $replace  True to replace any headers with the same name.
	 *
	 * @return  ROauth2ProtocolResponse  Instance of $this to allow chaining.
	 *
	 * @since   1.0
	 */
	public function setHeader($name, $value, $replace = false)
	{
		// Sanitize the input values.
		$name = (string) $name;
		$value = (string) $value;

		// If the replace flag is set, unset all known headers with the given name.
		if ($replace)
		{
			foreach ($this->headers as $key => $header)
			{
				if ($name == $header['name'])
				{
					unset($this->headers[$key]);
				}
			}

			// Clean up the array as unsetting nested arrays leaves some junk.
			$this->headers = array_values($this->headers);
		}

		// Add the header to the internal array.
		$this->headers[] = array('name' => $name, 'value' => $value);

		return $this;
	}

	/**
	 * Method to get the array of response headers to be sent when the response is sent
	 * to the client.
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * Method to clear any set response headers.
	 *
	 * @return  ROauth2ProtocolResponse  Instance of $this to allow chaining.
	 *
	 * @since   1.0
	 */
	public function clearHeaders()
	{
		$this->headers = array();

		return $this;
	}

	/**
	 * Send the response headers.
	 *
	 * @return  ROauth2ProtocolResponse  Instance of $this to allow chaining.
	 *
	 * @since   1.0
	 */
	public function sendHeaders()
	{
		if (!$this->checkHeadersSent())
		{
			foreach ($this->headers as $header)
			{
				if ('status' == strtolower($header['name']))
				{
					// 'status' headers indicate an HTTP status, and need to be handled slightly differently
					$this->header(ucfirst(strtolower($header['name'])) . ': ' . $header['value'], null, (int) $header['value']);
				}
				else
				{
					$this->header($header['name'] . ': ' . $header['value']);
				}
			}
		}

		return $this;
	}

	/**
	 * Method to check to see if headers have already been sent.  We are wrapping this to isolate the
	 * headers_sent() function from our code base for testing reasons.
	 *
	 * @return  boolean  True if the headers have already been sent.
	 *
	 * @codeCoverageIgnore
	 * @see     headers_sent()
	 * @since   11.3
	 */
	protected function checkHeadersSent()
	{
		return headers_sent();
	}

	/**
	 * Set body content.  If body content already defined, this will replace it.
	 *
	 * @param   string  $content  The content to set as the response body.
	 *
	 * @return  ROauth2ProtocolResponse  Instance of $this to allow chaining.
	 *
	 * @since   1.0
	 */
	public function setBody($content)
	{
		$this->body = array((string) $content);

		return $this;
	}

	/**
	 * Prepend content to the body content
	 *
	 * @param   string  $content  The content to prepend to the response body.
	 *
	 * @return  ROauth2ProtocolResponse  Instance of $this to allow chaining.
	 *
	 * @since   1.0
	 */
	public function prependBody($content)
	{
		array_unshift($this->body, (string) $content);

		return $this;
	}

	/**
	 * Append content to the body content
	 *
	 * @param   string  $content  The content to append to the response body.
	 *
	 * @return  ROauth2ProtocolResponse  Instance of $this to allow chaining.
	 *
	 * @since   1.0
	 */
	public function appendBody($content)
	{
		array_push($this->body, (string) $content);

		return $this;
	}

	/**
	 * Return the body content
	 *
	 * @param   boolean  $asArray  True to return the body as an array of strings.
	 *
	 * @return  mixed  The response body either as an array or concatenated string.
	 *
	 * @since   1.0
	 */
	public function getBody($asArray = false)
	{
		return $asArray ? $this->body : implode((array) $this->body);
	}

	/**
	 * Method to close the application.
	 *
	 * @param   integer  $code  The exit code (optional; default is 0).
	 *
	 * @return  void
	 *
	 * @codeCoverageIgnore
	 * @since   12.1
	 */
	public function close($code = 0)
	{
		exit($code);
	}
}
