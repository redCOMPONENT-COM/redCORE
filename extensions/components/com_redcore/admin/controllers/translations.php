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
			$this->getRedirectToListRoute('&translationTableName=' . $append)
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
		if ($translationTableName = JFactory::getApplication()->input->get('translationTableName'))
		{
			$append = '&translationTableName=' . $translationTableName . $append;
		}

		return parent::getRedirectToListRoute($append);
	}
}
