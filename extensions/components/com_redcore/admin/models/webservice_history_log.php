<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');

/**
 * Webservice Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.4
 */
class RedcoreModelWebservice_History_Log extends RModelAdmin
{
	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  $pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   11.1
	 */
	public function delete(&$pks)
	{
		// Initialise variables.
		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			$table->reset();
			$table->load($pk);

			if (parent::delete($pk))
			{
				unlink(JPATH_ROOT . '/' . $table->file_name);
			}
		}

		return true;
	}
}
