<?php
/**
 * @package     Redcore
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

if (version_compare(JVERSION, '3.0', 'lt'))
{
	JLoader::import('joomla.application.component.model');

	/**
	 * redCORE Controller Admin
	 *
	 * @package     Redcore
	 * @subpackage  Controller
	 * @since       1.0
	 */
	class RControllerAdmin extends RControllerAdminBase
	{
		/**
		 * We need to redeclare the method as JModelLegacy was not existing before 3.0.
		 *
		 * @param   JModel   $model  The data model object.
		 * @param   integer  $id     The validated data.
		 *
		 * @return  void
		 */
		protected function postDeleteHook(JModel $model, $id = null)
		{
		}
	}
}

else
{
	/**
	 * redCORE Controller Admin
	 *
	 * @package     Redcore
	 * @subpackage  Controller
	 * @since       1.0
	 */
	class RControllerAdmin extends RControllerAdminBase
	{
	}
}
