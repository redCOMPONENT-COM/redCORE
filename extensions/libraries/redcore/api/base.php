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
 * Interface to handle api calls
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.2
 */
abstract class RApiBase implements RApiInterface
{
	/**
	 * Options object
	 *
	 * @var    JRegistry
	 * @since  1.2
	 */
	public $options = null;

	/**
	 * Debug information messages
	 *
	 * @var    array
	 * @since  1.2
	 */
	protected $debugMessages = array();

	/**
	 * @var    int  Status code for current api call
	 * @since  1.2
	 */
	public $statusCode = 200;

	/**
	 * @var    string  Status text for current api call
	 * @since  1.2
	 */
	public $statusText = '';

	/**
	 * Standard status codes for RESTfull api
	 *
	 * @var    array
	 * @since  1.2
	 */
	public static $statusTexts = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'m a teapot',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
	);

	/**
	 * Set the options
	 *
	 * @param   mixed  $options  Array / JRegistry object with the options to load
	 *
	 * @return  RApiBase      An instance of itself for chaining
	 */
	public function setOptions($options = null)
	{
		// Received JRegistry
		if ($options instanceof JRegistry)
		{
			$this->options = $options;

			return $this;
		}

		$data = array();

		// Received array
		if (is_array($options))
		{
			$data = $options;
		}

		$this->options = new JRegistry($data);

		return $this;
	}

	/**
	 * Transform a source field data value.
	 *
	 * Creates a custom response text error the given errors
	 *
	 * @param   string  $errorCode  HTTP error code
	 * @param   array   $errors     Array with errors to be set in the message
	 *
	 * @return string
	 */
	public function createCustomHttpError($errorCode, $errors)
	{
		if (!empty($errors))
		{
			return self::$statusTexts[$errorCode] . ': ' . implode(". \n", $errors);
		}

		return self::$statusTexts[$errorCode];
	}

	/**
	 * Set status code for current api call
	 *
	 * @param   int     $statusCode  Status code
	 * @param   string  $text        Text to replace default api message
	 *
	 * @throws Exception
	 * @return  RApiBase      An instance of itself for chaining
	 */
	public function setStatusCode($statusCode, $text = null)
	{
		$this->statusCode = (int) $statusCode;
		$this->statusText = false === $text ? '' : (null === $text ? self::$statusTexts[$this->statusCode] : $text);

		if ($this->isInvalid())
		{
			throw new Exception(JText::sprintf('LIB_REDCORE_API_STATUS_CODE_INVALID', $statusCode));
		}

		return $this;
	}

	/**
	 * Checks if status code is invalid according to RFC specification http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
	 *
	 * @return boolean
	 *
	 * @api
	 */
	public function isInvalid()
	{
		return $this->statusCode < 100 || $this->statusCode >= 600;
	}

	/**
	 * Get the options
	 *
	 * @return  JRegistry  Object with the options
	 *
	 * @since   1.2
	 */
	public function getOptions()
	{
		// Always return a JRegistry instance
		if (!($this->options instanceof JRegistry))
		{
			$this->resetOptions();
		}

		return $this->options;
	}

	/**
	 * Set the option
	 *
	 * @param   string  $key    Key on which to store the option
	 * @param   mixed   $value  Value of the option
	 *
	 * @return  RApiBase  Object with the options
	 *
	 * @since   1.2
	 */
	public function setOption($key, $value)
	{
		$this->getOptions();
		$this->options->set($key, $value);

		return $this;
	}

	/**
	 * Function to empty all the options
	 *
	 * @return  RApiBase  Instance of $this to allow chaining.
	 *
	 * @since   1.2
	 */
	public function resetOptions()
	{
		return $this->setOptions(null);
	}

	/**
	 * Get the debug messages array
	 *
	 * @return  array
	 *
	 * @since   1.2
	 */
	public function getDebugMessages()
	{
		return $this->debugMessages;
	}

	/**
	 * Render the list of debug messages
	 *
	 * @return  string  Output text/HTML code
	 *
	 * @since   1.2
	 */
	public function renderDebugMessages()
	{
		return implode($this->debugMessages, "\n");
	}

	/**
	 * Add a debug message to the debug messages array
	 *
	 * @param   string  $message  Message to save
	 *
	 * @return  void
	 *
	 * @since   1.2
	 */
	public function addDebugMessage($message)
	{
		$this->debugMessages[] = $message;
	}

	/**
	 * Execute the Api operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 * @throws  RuntimeException
	 */
	public function execute()
	{
		return null;
	}

	/**
	 * Method to render the api call.
	 *
	 * @return  string  Api call output
	 *
	 * @since   1.2
	 */
	public function render()
	{
		return '';
	}
}
