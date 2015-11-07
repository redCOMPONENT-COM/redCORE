<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Redcore
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * System plugin for redCORE
 *
 * @package     Joomla.Plugin
 * @subpackage  System
 * @since       1.0
 */
class PlgSystemRedcore extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'group', 'params', 'language'
	 *                             (this list is not meant to be comprehensive).
	 *
	 * @since   1.5
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		$redcoreLoader = JPATH_LIBRARIES . '/redcore/bootstrap.php';

		if (file_exists($redcoreLoader))
		{
			require_once $redcoreLoader;

			// Sets plugin parameters for further use
			RBootstrap::$config = $this->params;

			// Sets initalization variables for frontend in Bootstrap class, according to plugin parameters
			RBootstrap::$loadFrontendCSS = $this->params->get('frontend_css', false);
			RBootstrap::$loadFrontendjQuery = $this->params->get('frontend_jquery', true);
			RBootstrap::$loadFrontendjQueryMigrate = $this->params->get('frontend_jquery_migrate', true);
			RBootstrap::$disableFrontendMootools = $this->params->get('frontend_disable_mootools', false);

			if (!$this->isInstaller())
			{
				RBootstrap::bootstrap(false);
			}
		}
	}

	/**
	 * Method to register custom library.
	 *
	 * @return  void
	 */
	public function onAfterInitialise()
	{
		if (defined('REDCORE_LIBRARY_LOADED'))
		{
			$apiName = JFactory::getApplication()->input->getString('api');

			if ($this->isApiEnabled($apiName))
			{
				$input = JFactory::getApplication()->input;

				if (!empty($apiName))
				{
					try
					{
						// We will disable all error messaging from PHP from the output
						error_reporting(0);
						ini_set('display_errors', 0);
						JError::setErrorHandling(E_ERROR, 'message');
						JFactory::getApplication()->clearHeaders();
						$webserviceClient = $input->get->getString('webserviceClient', '');
						$optionName = $input->get->getString('option', '');
						$optionName = strpos($optionName, 'com_') === 0 ? substr($optionName, 4) : $optionName;
						$viewName = $input->getString('view', '');
						$version = $input->getString('webserviceVersion', '');
						$token = $input->getString(RBootstrap::getConfig('oauth2_token_param_name', 'access_token'), '');
						$apiName = ucfirst($apiName);
						$method = strtoupper($input->getMethod());
						$task = RApiHalHelper::getTask();
						$data = RApi::getPostedData();
						$dataGet = $input->get->getArray();

						if (empty($webserviceClient))
						{
							$webserviceClient = JFactory::getApplication()->isAdmin() ? 'administrator' : 'site';
						}

						$options = array(
							'api'               => $apiName,
							'optionName'        => $optionName,
							'viewName'          => $viewName,
							'webserviceVersion' => $version,
							'webserviceClient'  => $webserviceClient,
							'method'            => $method,
							'task'              => $task,
							'data'              => $data,
							'dataGet'           => $dataGet,
							'accessToken'       => $token,
							'format'            => $input->getString('format', $this->params->get('webservices_default_format', 'json')),
							'id'                => $input->getString('id', ''),
							'absoluteHrefs'     => $input->get->getBool('absoluteHrefs', true),
						);

						// Create instance of Api and fill all required options
						$api = RApi::getInstance($options);

						// Run the api task
						$api->execute();

						// Display output
						$api->render();
					}
					catch (Exception $e)
					{
						$code = $e->getCode() > 0 ? $e->getCode() : 500;

						if (strtolower($apiName) == 'soap')
						{
							// We must have status of 200 for SOAP communication even if it is fault
							$message = RApiSoapHelper::createSoapFaultResponse($e->getMessage());
							header("Content-Type: soap+xml");
							header("Content-length: " . strlen($message));
							header("Status: 200");
							echo $message;
						}
						else
						{
							// Set the server response code.
							header('Status: ' . $code, true, $code);

							// Check for defined constants
							if (!defined('JSON_UNESCAPED_SLASHES'))
							{
								define('JSON_UNESCAPED_SLASHES', 64);
							}

							// An exception has been caught, echo the message and exit.
							echo json_encode(array('message' => $e->getMessage(), 'code' => $e->getCode(), 'type' => get_class($e)), JSON_UNESCAPED_SLASHES);
						}
					}

					JFactory::getApplication()->close();
				}
			}
		}
	}

	/**
	 * After route.
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	public function onAfterRoute()
	{
		if (defined('REDCORE_LIBRARY_LOADED'))
		{
			if (RTranslationHelper::getSiteLanguage() != JFactory::getLanguage()->getTag())
			{
				// Reset menus because they are loaded before any other module
				RMenu::resetJoomlaMenuItems();
			}
		}
	}

	/**
	 * This event is triggered after pushing the document buffers into the template placeholders,
	 * retrieving data from the document and pushing it into the into the JResponse buffer.
	 * http://docs.joomla.org/Plugin/Events/System
	 *
	 * @return boolean
	 */
	public function onAfterRender()
	{
		if (!$this->isRedcoreComponent() || !$this->disableMootools())
		{
			return true;
		}

		// Get the generated content
		$body = JResponse::getBody();

		// Remove JCaption JS calls
		$pattern     = "/(new JCaption\()(.*)(\);)/isU";
		$replacement = '';
		$body        = preg_replace($pattern, $replacement, $body);

		// Null window.addEvent( calls
		$pattern = "/(window.addEvent\()(.*)(,)/isU";
		$body    = preg_replace($pattern, 'do_nothing(', $body);
		JResponse::setBody($body);

		return true;
	}

	/**
	 * This event is triggered before the framework creates the Head section of the Document.
	 *
	 * @return  void
	 *
	 * @todo    Find a cleaner way to prioritise assets
	 */
	public function onBeforeCompileHead()
	{
		if (!$this->isRedcoreComponent())
		{
			return;
		}

		$doc = JFactory::getDocument();
		$isAdmin = JFactory::getApplication()->isAdmin();

		RHtmlMedia::loadFrameworkJs();

		if ($doc->_scripts)
		{
			$template = JFactory::getApplication()->getTemplate();

			// Remove Mootools if asked by view, or if it's a site view and it has been asked via plugin parameters
			if ($this->disableMootools() || (!$isAdmin && RBootstrap::$disableFrontendMootools))
			{
				$doc->addScriptDeclaration("function do_nothing() { return; }");
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/mootools-core.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/mootools-more.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/caption.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/modal.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/mootools.js']);
				unset($doc->_scripts[JURI::root(true) . '/plugins/system/mtupgrade/mootools.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/mootools-core-uncompressed.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/caption-uncompressed.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/modal-uncompressed.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/mootools-more-uncompressed.js']);

				if ($doc->_styleSheets)
				{
					unset($doc->_styleSheets[JURI::root(true) . '/media/system/css/modal.css']);
				}

				if (!$isAdmin)
				{
					unset($doc->_scripts[JURI::root(true) . '/media/system/js/core.js']);
					unset($doc->_scripts[JURI::root(true) . '/media/system/js/core-uncompressed.js']);
				}
			}

			// Remove jQuery in administration, or if it's frontend site and it has been asked via plugin parameters
			if ($isAdmin || (!$isAdmin && RBootstrap::$loadFrontendjQuery))
			{
				unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.min.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-noconflict.js']);

				$jQueryChosen = false;

				if (isset($doc->_scripts[JURI::root(true) . '/media/jui/js/chosen.jquery.js'])
					|| isset($doc->_scripts[JURI::root(true) . '/media/jui/js/chosen.jquery.min.js']))
				{
					$jQueryChosen = true;
					unset($doc->_scripts[JURI::root(true) . '/media/jui/js/chosen.jquery.js']);
					unset($doc->_scripts[JURI::root(true) . '/media/jui/js/chosen.jquery.min.js']);
					unset($doc->_styleSheets[JURI::root(true) . '/media/jui/css/chosen.css']);
					unset($doc->_styleSheets[JURI::root(true) . '/media/jui/css/chosen.min.css']);
				}

				// Template specific overrides for jQuery files (valid in Joomla 3.x)
				unset($doc->_scripts[JURI::root(true) . '/templates/' . $template . '/js/jui/jquery.min.js']);
				unset($doc->_scripts[JURI::root(true) . '/templates/' . $template . '/js/jui/jquery.js']);
				unset($doc->_scripts[JURI::root(true) . '/templates/' . $template . '/js/jui/jquery-noconflict.js']);

				if (isset($doc->_scripts[JURI::root(true) . '/templates/' . $template . '/js/jui/chosen.jquery.js'])
					|| isset($doc->_scripts[JURI::root(true) . '/templates/' . $template . '/js/jui/chosen.jquery.min.js']))
				{
					$jQueryChosen = true;
					unset($doc->_scripts[JURI::root(true) . '/templates/' . $template . '/js/jui/chosen.jquery.js']);
					unset($doc->_scripts[JURI::root(true) . '/templates/' . $template . '/js/jui/chosen.jquery.min.js']);
					unset($doc->_styleSheets[JURI::root(true) . '/templates/' . $template . '/css/jui/chosen.css']);
					unset($doc->_styleSheets[JURI::root(true) . '/templates/' . $template . '/css/jui/chosen.min.css']);
				}

				// Enables chosen when it was removed
				if ($jQueryChosen)
				{
					RHtml::_('rjquery.chosen', 'select');
				}
			}

			// Remove jQuery Migrate in administration, or if it's frontend site and it has been asked via plugin parameters
			if ($isAdmin || (!$isAdmin && RBootstrap::$loadFrontendjQueryMigrate))
			{
				unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-migrate.min.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-migrate.js']);

				// Template specific overrides for jQuery files (valid in Joomla 3.x)
				unset($doc->_scripts[JURI::root(true) . '/templates/' . $template . '/js/jui/jquery-migrate.min.js']);
				unset($doc->_scripts[JURI::root(true) . '/templates/' . $template . '/js/jui/jquery-migrate.js']);
			}

			// Remove Bootstrap in administration, or if it's frontend site and it has been asked via plugin parameters
			if ($isAdmin || (!$isAdmin && RBootstrap::$loadFrontendCSS))
			{
				unset($doc->_scripts[JURI::root(true) . '/media/jui/js/bootstrap.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/jui/js/bootstrap.min.js']);

				// Template specific overrides for jQuery files (valid in Joomla 3.x)
				unset($doc->_scripts[JURI::root(true) . '/templates/' . $template . '/js/jui/bootstrap.js']);
				unset($doc->_scripts[JURI::root(true) . '/templates/' . $template . '/js/jui/bootstrap.min.js']);
			}
		}
	}

	/**
	 * This event is triggered immediately before pushing the document buffers into the template placeholders,
	 * retrieving data from the document and pushing it into the into the JResponse buffer.
	 * http://docs.joomla.org/Plugin/Events/System
	 *
	 * @return  void
	 */
	public function onBeforeRender()
	{
		if (!$this->isRedcoreComponent())
		{
			return;
		}
	}

	/**
	 * Check is is a redCORE view
	 *
	 * @return  boolean
	 */
	private function isRedcoreComponent()
	{
		return defined('REDCORE_BOOTSTRAPPED');
	}

	/**
	 * Check is is a redCORE view
	 *
	 * @return  boolean
	 */
	private function isInstaller()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		return $app->isAdmin() && $input->getString('option') == 'com_installer' && $input->get('task') == 'install.install';
	}

	/**
	 * Check if the view asked to disable mootools
	 *
	 * @return  boolean
	 */
	private function disableMootools()
	{
		$app = JFactory::getApplication();

		$disable = $app->input->get('disable_mootools', false);

		if (!$disable)
		{
			$disable = RHtmlMedia::isMootoolsDisabled();
		}

		return $disable;
	}

	/**
	 * Checks if given api name is currently install and enabled on this server
	 *
	 * @param   string  $apiName  Api name
	 *
	 * @return bool
	 */
	private function isApiEnabled($apiName)
	{
		$apiName = strtolower($apiName);

		return ($this->params->get('enable_webservices', 0) == 1 && $apiName == 'hal')
		|| ($this->params->get('enable_oauth2_server', 0) == 1 && $apiName == 'oauth2')
		|| ($this->params->get('enable_soap', 0) == 1 && $apiName == 'soap')
		|| ($this->params->get('enable_payment', 1) == 1 && $apiName == 'payment');
	}
}
