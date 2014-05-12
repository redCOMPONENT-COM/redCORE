<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
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
		$this->setRedirect(
			$this->getRedirectToListRoute('&layout=manage')
		);
	}
}
