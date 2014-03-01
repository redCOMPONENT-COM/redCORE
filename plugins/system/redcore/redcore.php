<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Redcore
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
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
	 * Method to register custom library.
	 *
	 * @return  void
	 */
	public function onAfterInitialise()
	{
		$redcoreLoader = JPATH_LIBRARIES . '/redcore/bootstrap.php';

		if (file_exists($redcoreLoader))
		{
			require_once $redcoreLoader;

			// Sets initalization variables for frontend in Bootstrap class, according to plugin parameters
			RBootstrap::$loadFrontendjQuery = $this->params->get('frontend_jquery', true);
			RBootstrap::$loadFrontendjQueryMigrate = $this->params->get('frontend_jquery_migrate', true);
			RBootstrap::$loadFrontendBootstrap = $this->params->get('frontend_bootstrap', true);
			RBootstrap::$disableFrontendMootools = $this->params->get('frontend_disable_mootools', false);
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

		RHtml::_('rbootstrap.framework');

		if ($doc->_scripts)
		{
			$template = JFactory::getApplication()->getTemplate();

			// Remove Mootools if asked by view, or if it's a site view and it has been asked via plugin parameters
			if ($this->disableMootools() || (!$isAdmin && RBootstrap::$disableFrontendMootools))
			{
				$doc->addScriptDeclaration("function do_nothing() { return; }");
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/mootools-core.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/mootools-more.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/core.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/caption.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/modal.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/mootools.js']);
				unset($doc->_scripts[JURI::root(true) . '/plugins/system/mtupgrade/mootools.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/mootools-core-uncompressed.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/core-uncompressed.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/caption-uncompressed.js']);

				if ($doc->_styleSheets)
				{
					unset($doc->_styleSheets[JURI::root(true) . '/media/system/css/modal.css']);
				}
			}

			// Remove jQuery in administration, or if it's frontend site and it has been asked via plugin parameters
			if ($isAdmin || (!$isAdmin && RBootstrap::$loadFrontendjQuery))
			{
				unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.min.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-noconflict.js']);

				// Template specific overrides for jQuery files (valid in Joomla 3.x)
				unset($doc->_scripts[JURI::root(true) . '/templates/' . $template . '/js/jui/jquery.min.js']);
				unset($doc->_scripts[JURI::root(true) . '/templates/' . $template . '/js/jui/jquery.js']);
				unset($doc->_scripts[JURI::root(true) . '/templates/' . $template . '/js/jui/jquery-noconflict.js']);
			}

			// Remove jQuery Migrate in administration, or if it's frontend site and it has been asked via plugin parameters
			if ($isAdmin || (!$isAdmin && RBootstrap::$loadFrontendjQuery))
			{
				unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-migrate.min.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-migrate.js']);

				// Template specific overrides for jQuery files (valid in Joomla 3.x)
				unset($doc->_scripts[JURI::root(true) . '/templates/' . $template . '/js/jui/jquery-migrate.min.js']);
				unset($doc->_scripts[JURI::root(true) . '/templates/' . $template . '/js/jui/jquery-migrate.js']);
			}

			// Remove Bootstrap in administration, or if it's frontend site and it has been asked via plugin parameters
			if ($isAdmin || (!$isAdmin && RBootstrap::$loadFrontendBootstrap))
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
	 * Check if the view asked to disable mootools
	 *
	 * @return  boolean
	 */
	private function disableMootools()
	{
		$app = JFactory::getApplication();

		return $app->input->get('disable_mootools', false);
	}
}
