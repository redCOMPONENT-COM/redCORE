<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

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
		if (empty(self::$instances[$signature]))
		{
			// Derive the class name from the driver.
			$class = 'RApi' . ucfirst(strtolower($options['api'])) . ucfirst(strtolower($options['api']));

			// If the class still doesn't exist we have nothing left to do but throw an exception.
			if (!class_exists($class))
			{
				throw new RuntimeException(sprintf('Unable to load Api: %s', $options['api']));
			}

			// Create our new RApi connector based on the options given.
			try
			{
				$instance = new $class($options);
			}
			catch (RuntimeException $e)
			{
				throw new RuntimeException(sprintf('Unable to connect to the Api: %s', $e->getMessage()));
			}

			// Set the new connector to the global instances based on signature.
			self::$instances[$signature] = $instance;
		}

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
		$this->setApi($this->options->get('api', 'hal'));

		// Init Environment
		$this->setApiOperation();
	}

	/**
	 * Set Method for Api
	 *
	 * @param   string  $operation  Operation name
	 *
	 * @return  RApi
	 *
	 * @since   1.2
	 */
	public function setApiOperation($operation = '')
	{
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
}
