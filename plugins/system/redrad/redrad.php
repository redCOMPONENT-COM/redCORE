<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Redrad
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * System plugin for redRAD
 *
 * @package     Joomla.Plugin
 * @subpackage  System
 * @since       1.0
 */
class PlgSystemRedRad extends JPlugin
{
	/**
     * Method to register custom library.
     *
     * @return  void
     */
	public function onAfterInitialise()
	{
		$redradLoader = JPATH_LIBRARIES . '/redrad/bootstrap.php';

		if (file_exists($redradLoader))
		{
			require_once $redradLoader;

			// For Joomla! 2.5 compatibility we add some core functions
			if (version_compare(JVERSION, '3.0', '<'))
			{
				JLoader::registerPrefix('J',  JPATH_LIBRARIES . '/redrad/joomla');
			}
		}

		// Unload any previous bootstrap
		$doc = JFactory::getDocument();
	}

	/**
	 * This event is triggered immediately before pushing the document buffers into the template placeholders,
	 * retrieving data from the document and pushing it into the into the JResponse buffer.
	 * http://docs.joomla.org/Plugin/Events/System
	 *
	 * @return boolean
	 */
	function onBeforeRender()
	{
		$doc = JFactory::getDocument();
		$app = JFactory::getApplication();

		$isRedrad = $app->input->get('redrad', false);

		if ($isRedrad)
		{
			if ($doc->_scripts)
			{
				foreach ($doc->_scripts as $script => $value)
				{
					if (substr_count($script, 'media/jui/js/bootstrap.min.js')
						|| substr_count($script, 'media/jui/js/bootstrap.js')
						|| substr_count($script, 'template.js'))
					{
						unset($doc->_scripts[$script]);
					}
				}
			}

			if ($doc->_styleSheets)
			{
				// Disable any bootstrap CSS
				foreach ($doc->_styleSheets as $style => $value)
				{
					if (substr_count($style, 'media/jui/css/bootstrap.min.css')
						|| substr_count($style, 'media/jui/css/bootstrap.css')
						|| substr_count($style, 'template.css'))
					{
						unset($doc->_styleSheets[$style]);
					}
				}
			}
		}
	}
}
