<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Webservice History Logs Controller
 *
 * @package     Redcore.Backend
 * @subpackage  Controllers
 * @since       1.0
 */
class RedcoreControllerWebservice_History_Logs extends RControllerAdmin
{
	/**
	 * Download Response Data.
	 *
	 * @return  mixed  True if successful, false otherwise.
	 */
	public function downloadResponseData()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			ArrayHelper::toInteger($cid);

			if (count($cid) > 1 && !extension_loaded('zlib'))
			{
				$cid = array($cid[0]);
			}

			$contentType = 'text/plain';
			$content     = '';

			// Download single PHP file
			if (count($cid) == 1)
			{
				/** @var RedcoreTableWebservice $table */
				$table    = RTable::getAdminInstance('Webservice_History_Log', array(), 'com_redcore');
				$fileName = '';

				if ($table && $table->load($cid[0]))
				{
					$path = JPATH_ROOT . '/' . $table->file_name;

					if (is_file($path))
					{
						$fileName = substr(basename($path), 0, -3) . 'txt';
						$content  = file_get_contents($path);
						$content  = substr($content, 33);
					}
				}
			}
			// Download package of PHP files in a ZIP
			else
			{
				/** @var RedcoreTableWebservice_History_Log $table */
				$table       = RTable::getAdminInstance('Webservice_History_Log', array(), 'com_redcore');
				$app         = JFactory::getApplication();
				$contentType = 'application/zip';
				$fileName    = 'webservice_history_logs_' . (date('Y_m_d_H_i_s')) . '.zip';

				$files = array();

				foreach ($cid as $id)
				{
					$table->reset();

					if ($table->load($id))
					{
						$path = JPATH_ROOT . '/' . $table->file_name;

						if (is_file($path))
						{
							$content = file_get_contents($path);
							$content = substr($content, 33);
							$files[] = array(
								'name' => substr(basename($path), 0, -3) . 'txt',
								'data' => $content,
								'time' => time()
							);
						}
					}
				}

				$uniqueFile = uniqid('webservice_history_log_files_');
				$zipFile    = $app->get('tmp_path') . '/' . $uniqueFile . '.zip';

				// Run the packager
				jimport('joomla.filesystem.folder');
				jimport('joomla.filesystem.file');
				$delete = JFolder::files($app->get('tmp_path') . '/', $uniqueFile, false, true);

				if (!empty($delete))
				{
					if (!JFile::delete($delete))
					{
						// JFile::delete throws an error
						$this->setError(JText::_('COM_REDCORE_WEBSERVICES_ERR_ZIP_DELETE_FAILURE'));
						$this->setRedirect($this->getRedirectToListRoute());

						return false;
					}
				}

				$packager = JArchive::getAdapter('zip');

				if (!$packager)
				{
					$this->setError(JText::_('COM_REDCORE_WEBSERVICES_ERR_ZIP_ADAPTER_FAILURE'));
					$this->setRedirect($this->getRedirectToListRoute());

					return false;
				}
				elseif (!$packager->create($zipFile, $files))
				{
					$this->setError(JText::_('COM_REDCORE_WEBSERVICES_ERR_ZIP_CREATE_FAILURE'));
					$this->setRedirect($this->getRedirectToListRoute());

					return false;
				}

				$content = file_get_contents($zipFile);
			}

			if ($content)
			{
				// Send the headers
				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Cache-Control: private", false);
				header("Content-type: " . $contentType . "; charset=UTF-8");
				header("Content-Disposition: attachment; filename=\"" . $fileName . "\";");

				// Send the file
				echo $content;

				JFactory::getApplication()->close();
			}
		}

		// Set redirect
		$this->setRedirect($this->getRedirectToListRoute());
	}
}
