<?php
/**
 * @package     Redcore.Site
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2013 Roberto Segura López. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Frontend front controller of redCORE
 *
 * @package     Recore.Site
 * @subpackage  Controllers
 * @since       1.0
 */
class RedcoreController extends JControllerLegacy
{
	/**
	 * Typical view method for MVC based architecture
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy  A JControllerLegacy object to support chaining.
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$input = JFactory::getApplication()->input;

		// Get the requested view;
		$view = $input->get('view');

		// Call parent behavior
		return parent::display($cachable, $urlparams);
	}
}
