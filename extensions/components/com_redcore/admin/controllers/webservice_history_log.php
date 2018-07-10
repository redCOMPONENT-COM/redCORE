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
 * Webservice History Log Controller
 *
 * @package     Redcore.Backend
 * @subpackage  Controllers
 * @since       1.4
 */
class RedcoreControllerWebservice_History_Log extends RControllerForm
{
	/**
	 * Method to publish a list of items
	 *
	 * @return  void
	 */
	public function getFileData()
	{
		// Check for request forgeries
		JSession::checkToken('get') or die(JText::_('JINVALID_TOKEN'));

		// Get items to copy from the request.
		$id    = JFactory::getApplication()->input->get('id', 0, 'int');
		$table = RTable::getAdminInstance('Webservice_History_Log');
		$table->load($id);

		echo file_get_contents(JPATH_ROOT . '/' . $table->file_name);

		JFactory::getApplication()->close();
	}
}
