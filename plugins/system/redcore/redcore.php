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
		if (!$this->isRedcoreComponent())
		{
			return;
		}

		$redcoreLoader = JPATH_LIBRARIES . '/redcore/bootstrap.php';

		if (file_exists($redcoreLoader))
		{
			require_once $redcoreLoader;

			// For Joomla! 2.5 compatibility we add some core functions
			if (version_compare(JVERSION, '3.0', '<'))
			{
				RLoader::registerPrefix('J',  JPATH_LIBRARIES . '/redcore/joomla', false, true);
			}
		}

		// Make available the fields
		JFormHelper::addFieldPath(JPATH_LIBRARIES . '/redcore/form/fields');

		// Make available the rules
		JFormHelper::addRulePath(JPATH_LIBRARIES . '/redcore/form/rules');
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

		$doc = JFactory::getDocument();

		RHtml::_('rbootstrap.framework');

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
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/mootools-core-uncompressed.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/core-uncompressed.js']);
				unset($doc->_scripts[JURI::root(true) . '/media/system/js/caption-uncompressed.js']);
			}

			// Remove jQuery
			unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.min.js']);
			unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery.js']);
			unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-migrate.min.js']);
			unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-migrate.js']);
			unset($doc->_scripts[JURI::root(true) . '/media/jui/js/jquery-noconflict.js']);

			// Remove bootstrap
			unset($doc->_scripts[JURI::root(true) . '/media/jui/js/bootstrap.js']);
			unset($doc->_scripts[JURI::root(true) . '/media/jui/js/bootstrap.min.js']);
		}

		if ($doc->_styleSheets)
		{
			// Disable mootools
			if ($this->disableMootools())
			{
				unset($doc->_styleSheets[JURI::root(true) . '/media/system/css/modal.css']);
			}
		}
	}

	/**
	 * Check is is a redCORE view
	 *
	 * @return  boolean
	 */
	private function isRedcoreComponent()
	{
		$app = JFactory::getApplication();

		// If the application is admin and the user logged out (this is not a redCORE component)
		if ($app->isAdmin() && JFactory::getUser()->guest)
		{
			return false;
		}

		// Check the manifest.
		$option = $app->input->getString('option');

		// Always enabled for redCORE component
		if ($option == 'com_redcore')
		{
			return true;
		}

		if (empty($option))
		{
			return false;
		}

		$componentName = substr($option, 4);
		$manifestFile = JPATH_ADMINISTRATOR . '/components/' . $option . '/' . $componentName . '.xml';

		if (file_exists($manifestFile))
		{
			$manifest = new SimpleXMLElement(file_get_contents($manifestFile));

			if ($manifest->xpath('//extension/redcore'))
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
