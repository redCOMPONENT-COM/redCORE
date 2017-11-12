<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Interface to handle api calls
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.2
 */
class RApi extends RApiBase
{
	/**
	 * @var    array  RApi instances container.
	 * @since  1.2
	 */
	public static $instances = array();

	/**
	 * @var    string  Name of the Api
	 * @since  1.2
	 */
	public $apiName = '';

	/**
	 * @var    string  Operation that will be preformed with this Api call. supported: CREATE, READ, UPDATE, DELETE
	 * @since  1.2
	 */
	public $operation = 'read';

	/**
	 * The start time for measuring the execution time.
	 *
	 * @var    float
	 * @since  1.2
	 */
	public $startTime;

	/**
	 * Method to return a RApi instance based on the given options.  There is one global option and then
	 * the rest are specific to the Api.  The 'api' option defines which RApi class is
	 * used for, default is 'hal'.
	 *
	 * Instances are unique to the given options and new objects are only created when a unique options array is
	 * passed into the method.  This ensures that we don't end up with unnecessary api resources.
	 *
	 * @param   array  $options  Parameters to be passed to the creating api.
	 *
	 * @return  RApi  Api object.
	 *
	 * @since   1.2
	 * @throws  RuntimeException
	 */
	public static function getInstance($options = array())
	{
		// Sanitize the api options.
		$options['api'] = (isset($options['api'])) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $options['api']) : 'hal';

		// Get the options signature for the api connector.
		$signature = md5(serialize($options));

		// If we already have a api connector instance for these options then just use that.
		if (!empty(self::$instances[$signature]))
		{
			return self::$instances[$signature];
		}

		// Derive the class name from the driver.
		$class = 'RApi' . ucfirst(strtolower($options['api'])) . ucfirst(strtolower($options['api']));

		// If the class still doesn't exist we have nothing left to do but throw an exception.
		if (!class_exists($class))
		{
			throw new RuntimeException(JText::sprintf('LIB_REDCORE_API_UNABLE_TO_LOAD_API', $options['api']));
		}

		// Create our new RApi connector based on the options given.
		try
		{
			$instance = new $class($options);
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException(JText::sprintf('LIB_REDCORE_API_UNABLE_TO_CONNECT_TO_API', $e->getMessage()));
		}

		// Set the new connector to the global instances based on signature.
		self::$instances[$signature] = $instance;

		return self::$instances[$signature];
	}

	/**
	 * Method to instantiate the file-based api call.
	 *
	 * @param   mixed  $options  Optional custom options to load. JRegistry or array format
	 *
	 * @since   1.2
	 */
	public function __construct($options = null)
	{
		$this->startTime = microtime(true);

		// Initialise / Load options
		$this->setOptions($options);

		// Main properties
		$this->setOptionsFromHeader();

		// Main properties
		$this->setApi($this->options->get('api', 'hal'));

		// Load Library language
		$this->loadExtensionLanguage('lib_joomla', JPATH_ADMINISTRATOR);
	}

	/**
	 * Set options received from Headers of the request
	 *
	 * @return  RApi
	 *
	 * @since   1.7
	 */
	public function setOptionsFromHeader()
	{
		$app = JFactory::getApplication();
		$headers = self::getHeaderVariablesFromGlobals();

		// Setting the language from the header options information
		if (isset($headers['ACCEPT_LANGUAGE']))
		{
			// We are only using header options if the URI does not contain lang parameter as it have higher priority
			if ($app->input->get('lang', '') == '')
			{
				$acceptLanguages = explode(',', $headers['ACCEPT_LANGUAGE']);

				// We go through all proposed languages. First language that is found installed on the website is used
				foreach ($acceptLanguages as $acceptLanguage)
				{
					$acceptLanguage = explode(';', $acceptLanguage);

					if (RTranslationHelper::setLanguage($acceptLanguage[0]))
					{
						$this->options->set('lang', $acceptLanguage[0]);
						$app->input->set('lang', $acceptLanguage[0]);

						break;
					}
				}
			}
		}

		// Setting option for compressed output
		if (isset($headers['ACCEPT_ENCODING']))
		{
			$acceptCompression = strpos(strtolower($headers['ACCEPT_ENCODING']), 'gzip') !== false ? 1 : 0;
			$this->options->set('enable_gzip_compression', $acceptCompression);
		}

		// Setting option for compressed output
		if (isset($headers['CONTENT_ENCODING']))
		{
			$acceptCompression = strpos(strtolower($headers['CONTENT_ENCODING']), 'gzip') !== false ? 1 : 0;
			$this->options->set('enable_gzip_input_compression', $acceptCompression);
		}

		return $this;
	}

	/**
	 * Change the Api
	 *
	 * @param   string  $apiName  Api instance to render
	 *
	 * @return  void
	 *
	 * @since   1.2
	 */
	public function setApi($apiName)
	{
		$this->apiName = $apiName;
	}

	/**
	 * Change the debug mode
	 *
	 * @param   boolean  $debug  Enable / Disable debug
	 *
	 * @return  void
	 *
	 * @since   1.2
	 */
	public function setDebug($debug)
	{
		$this->options->set('debug', (boolean) $debug);
	}

	/**
	 * Load extension language file.
	 *
	 * @param   string  $option  Option name
	 * @param   string  $path    Path to language file
	 *
	 * @return  object
	 */
	public function loadExtensionLanguage($option, $path = JPATH_SITE)
	{
		// Load common and local language files.
		$lang = JFactory::getLanguage();

		// Load language file
		$lang->load($option, $path, null, false, false)
		|| $lang->load($option, $path . "/components/$option", null, false, false)
		|| $lang->load($option, $path, $lang->getDefault(), false, false)
		|| $lang->load($option, $path . "/components/$option", $lang->getDefault(), false, false);

		return $this;
	}

	/**
	 * Returns posted data in array format
	 *
	 * @return  array
	 *
	 * @since   1.2
	 */
	public static function getPostedData()
	{
		$headers   = self::getHeaderVariablesFromGlobals();
		$input     = JFactory::getApplication()->input;
		$inputData = file_get_contents("php://input");

		// Is data is compressed we will fetch it through separate function
		if (isset($headers['CONTENT_ENCODING']))
		{
			if (strpos(strtolower($headers['CONTENT_ENCODING']), 'gzip') !== false)
			{
				$decompressed = gzdecode($inputData);

				if ($decompressed)
				{
					$inputData = $decompressed;
				}
			}
		}

		if (is_object($inputData))
		{
			$inputData = ArrayHelper::fromObject($inputData);
		}
		elseif (is_string($inputData))
		{
			$inputData = trim($inputData);
			$parsedData = null;

			// We try to transform it into JSON
			if ($dataJson = @json_decode($inputData, true))
			{
				if (json_last_error() == JSON_ERROR_NONE)
				{
					$parsedData = (array) $dataJson;
				}
			}

			// We try to transform it into XML
			if (is_null($parsedData) && $xml = @simplexml_load_string($inputData))
			{
				$json = json_encode((array) $xml);
				$parsedData = json_decode($json, true);
			}

			// We try to transform it into Array
			if (is_null($parsedData) && !empty($inputData) && !is_array($inputData))
			{
				parse_str($inputData, $parsedData);
			}

			$inputData = $parsedData;
		}
		else
		{
			$inputData = $input->post->getArray();
		}

		$filter = JFilterInput::getInstance(array(), array(), 1, 1);

		// Filter data with JInput default filter in blacklist mode
		$postedData = new JInput($inputData, array('filter' => $filter));

		if (version_compare(JVERSION, '3') >= 0)
		{
			return $postedData->getArray(array(), null, 'HTML');
		}
		elseif ($inputData)
		{
			return $postedData->getArray(array(), $inputData, 'HTML');
		}

		return array();
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
	 * Method to render the api call output.
	 *
	 * @return  string  Api call output
	 *
	 * @since   1.2
	 */
	public function render()
	{
		return '';
	}

	/**
	 * Returns header variables from globals
	 *
	 * @return  array
	 */
	public static function getHeaderVariablesFromGlobals()
	{
		$headers = array();

		foreach ($_SERVER as $key => $value)
		{
			if (strpos($key, 'HTTP_') === 0)
			{
				$headers[substr($key, 5)] = $value;
			}
			// CONTENT_* are not prefixed with HTTP_
			elseif (in_array($key, array('CONTENT_LENGTH', 'CONTENT_MD5', 'CONTENT_TYPE')))
			{
				$headers[$key] = $value;
			}
		}

		if (isset($_SERVER['PHP_AUTH_USER']))
		{
			$headers['PHP_AUTH_USER'] = $_SERVER['PHP_AUTH_USER'];
			$headers['PHP_AUTH_PW'] = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
		}
		else
		{
			/*
			 * php-cgi under Apache does not pass HTTP Basic user/pass to PHP by default
			 * For this workaround to work, add this line to your .htaccess file:
			 * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
			 *
			 * A sample .htaccess file:
			 * RewriteEngine On
			 * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
			 * RewriteCond %{REQUEST_FILENAME} !-f
			 * RewriteRule ^(.*)$ app.php [QSA,L]
			 */

			$authorizationHeader = null;

			if (isset($_SERVER['HTTP_AUTHORIZATION']))
			{
				$authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
			}
			elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
			{
				$authorizationHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
			}
			elseif (function_exists('apache_request_headers'))
			{
				$requestHeaders = (array) apache_request_headers();

				// Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
				$requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

				if (isset($requestHeaders['Authorization']))
				{
					$authorizationHeader = trim($requestHeaders['Authorization']);
				}
			}

			if (null !== $authorizationHeader)
			{
				$headers['AUTHORIZATION'] = $authorizationHeader;

				// Decode AUTHORIZATION header into PHP_AUTH_USER and PHP_AUTH_PW when authorization header is basic
				if (0 === stripos($authorizationHeader, 'basic'))
				{
					$exploded = explode(':', base64_decode(substr($authorizationHeader, 6)));

					if (count($exploded) == 2)
					{
						list($headers['PHP_AUTH_USER'], $headers['PHP_AUTH_PW']) = $exploded;
					}
				}
			}
		}

		// PHP_AUTH_USER/PHP_AUTH_PW
		if (isset($headers['PHP_AUTH_USER']))
		{
			$headers['AUTHORIZATION'] = 'Basic ' . base64_encode($headers['PHP_AUTH_USER'] . ':' . $headers['PHP_AUTH_PW']);
		}

		return $headers;
	}

	/**
	 * Method to clear any set response headers.
	 *
	 * @return  void
	 */
	public static function clearHeaders()
	{
		if (version_compare(JVERSION, '3') >= 0)
		{
			JFactory::getApplication()->clearHeaders();
		}
		else
		{
			JResponse::clearHeaders();
		}
	}

	/**
	 * Send the response headers.
	 *
	 * @return  void
	 */
	public static function sendHeaders()
	{
		if (version_compare(JVERSION, '3') >= 0)
		{
			JFactory::getApplication()->sendHeaders();
		}
		else
		{
			JResponse::sendHeaders();
		}
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
	 * @return  void
	 */
	public static function setHeader($name, $value, $replace = false)
	{
		if (version_compare(JVERSION, '3') >= 0)
		{
			JFactory::getApplication()->setHeader($name, $value, $replace);
		}
		else
		{
			JResponse::setHeader($name, $value, $replace);
		}
	}
}
