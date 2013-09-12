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
		$isAdmin = JFactory::getApplication()->isAdmin();

		if (!$isAdmin || ! $this->isRedradComponent())
		{
			return;
		}

		$redradLoader = JPATH_LIBRARIES . '/redrad/bootstrap.php';

		if (file_exists($redradLoader) && !class_exists('Inflector'))
		{
			require_once $redradLoader;

			// For Joomla! 2.5 compatibility we add some core functions
			if (version_compare(JVERSION, '3.0', '<'))
			{
				RLoader::registerPrefix('J',  JPATH_LIBRARIES . '/redrad/joomla', false, true);
			}
		}

		// Make available the fields
		JFormHelper::addFieldPath(JPATH_LIBRARIES . '/redrad/form/fields');

		// Make available the rules
		JFormHelper::addRulePath(JPATH_LIBRARIES . '/redrad/form/rules');
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
		if (!$this->isRedradComponent() || !$this->disableMootools())
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
		if (!$this->isRedradComponent())
		{
			return;
		}

		$doc = JFactory::getDocument();

		if (!empty($doc->_styleSheets))
		{
			foreach ($doc->_styleSheets as $stylesheet => $data)
			{
				// If component.css is included put it at the bottom of the list
				if (substr_count($stylesheet, 'component.css'))
				{
					unset($doc->_styleSheets[$stylesheet]);
					$doc->_styleSheets[$stylesheet] = $data;
				}
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
		if (!$this->isRedradComponent())
		{
			return;
		}

		$doc = JFactory::getDocument();

		// Base assets to load always with redRAD
		JHtml::_('rbootstrap.fontawesome');

		if ($doc->_scripts)
		{
			// Remove Mootools
			if ($this->disableMootools())
			{
				$doc->addScriptDeclaration("function do_nothing() { return; }");
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/mootools-core.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/mootools-more.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/core.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/caption.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/modal.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/mootools.js']);
				unset($doc->_scripts[JURI::root(true) . '/plugins/system/mtupgrade/mootools.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/mootools-core-uncompresed.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/core-uncompresed.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/caption-uncompresed.js']);
			}

			// Remove jQuery
			unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.min.js']);
			unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.js']);
			unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-noconflict.js']);

			// Remove bootstrap
			unset($doc->_scripts[JURI::root(true) . '/media/jui/js/bootstrap.min.js']);
			unset($doc->_scripts[JURI::root(true) . '/media/jui/js/bootstrap.js']);

			// Remove other JS
			foreach ($doc->_scripts as $script => $value)
			{
				if (substr_count($script, 'template.js'))
				{
					unset($doc->_scripts[$script]);
				}
			}
		}

		if ($doc->_styleSheets)
		{
			// Disable mootools
			if ($this->disableMootools())
			{
				unset($doc->_styleSheets[JURI::root(true) . '/media/system/css/modal.css']);
			}

			// Disable core bootstrap
			unset($doc->_styleSheets[JURI::root(true) . '/media/jui/css/bootstrap.min.css']);
			unset($doc->_styleSheets[JURI::root(true) . '/media/jui/css/bootstrap.css']);

			// Disable other CSS
			foreach ($doc->_styleSheets as $style => $value)
			{
				if (substr_count($style, 'template.css'))
				{
					unset($doc->_styleSheets[$style]);
				}
			}
		}
	}

	/**
	 * Check is is a redRAD view
	 *
	 * @return  boolean
	 */
	private function isRedradComponent()
	{
		// If the application is admin and the user logged out (this is not a redrad component)
		if (JFactory::getApplication()->isAdmin() && JFactory::getUser()->guest)
		{
			return false;
		}

		$option = isset($_GET['option']) ? $_GET['option'] : null;

		if ($option)
		{
			$comParams = JComponentHelper::getParams($option);

			if ($comParams->get('redrad', null))
			{
				return true;
			}
		}

		return false;
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
