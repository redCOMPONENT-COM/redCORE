<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Translations Controller
 *
 * @package     Redcore.Backend
 * @subpackage  Controllers
 * @since       1.0
 */
class RedcoreControllerTranslations extends RControllerAdmin
{
	/**
	 * Display is not supported by this controller.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy  A JControllerLegacy object to support chaining.
	 *
	 * @since   12.2
	 */
	public function display($cachable = false, $urlparams = array())
	{
		if ($this->input->get('component', '') == '')
		{
			$this->manageContentElement();
		}
		else
		{
			return parent::display($cachable, $urlparams);
		}
	}

	/**
	 * Displays Content Elements management screen
	 *
	 * @return  void
	 */
	public function manageContentElement()
	{
		$append = '';

		if ($component = $this->input->get('component'))
		{
			$append = '&component=' . $component;
		}

		$this->setRedirect(
			$this->getRedirectToListRoute('&contentelement=&layout=manage' . $append)
		);
	}

	/**
	 * Get the JRoute object for a redirect to list.
	 *
	 * @param   string  $append  An optional string to append to the route
	 *
	 * @return  JRoute  The JRoute object
	 */
	protected function getRedirectToListRoute($append = '')
	{
		// Setup redirect info.
		if ($contentElement = JFactory::getApplication()->input->get('contentelement'))
		{
			$append = '&contentelement=' . $contentElement . $append;
		}

		return parent::getRedirectToListRoute($append);
	}
}
