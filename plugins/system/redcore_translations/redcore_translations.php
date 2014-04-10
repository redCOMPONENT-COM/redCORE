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
 * System plugin for redCORE Translations
 *
 * @package     Joomla.Plugin
 * @subpackage  System
 * @since       1.0
 */
class PlgSystemRedcore_translations extends JPlugin
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

			// Sets plugin parameters in RTranslationHelper class
			RTranslationHelper::$pluginParams = $this->params;
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
		if (!$this->isRedcoreComponent())
		{
			return true;
		}

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
}
