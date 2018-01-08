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
 * Translation Tables Controller
 *
 * @package     Redcore.Backend
 * @subpackage  Controllers
 * @since       1.0
 */
class RedcoreControllerTranslation_Tables extends RControllerAdmin
{
	/**
	 * Method to install Content Element.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function redirectAfterAction()
	{
		if ($returnUrl = $this->input->get('return', '', 'Base64'))
		{
			$this->setRedirect(JRoute::_(base64_decode($returnUrl), false));
		}
	}

	/**
	 * Method to install Content Element Xml.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function installFromXml()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$app = JFactory::getApplication();

		$option  = $app->input->getString('component');
		$xmlFile = $app->input->getString('contentElement');

		if ($xmlFile == 'all')
		{
			if (RTranslationTable::batchContentElements($option, 'install', $notifications = true))
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_TRANSLATION_TABLE_CONTENT_ELEMENT_INSTALLED'), 'message');
			}
			else
			{
				$this->setMessage(JText::_('COM_REDCORE_TRANSLATION_TABLE_UNABLE_TO_INSTALL_XML'), 'error');
			}
		}
		else
		{
			if (RTranslationTable::installContentElement($option, $xmlFile, false, $notifications = true))
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_TRANSLATION_TABLE_CONTENT_ELEMENT_INSTALLED'), 'message');
			}
			else
			{
				$this->setMessage(JText::_('COM_REDCORE_TRANSLATION_TABLE_UNABLE_TO_INSTALL_XML'), 'error');
			}
		}

		// Set redirect
		$this->setRedirect($this->getRedirectToListRoute());
	}

	/**
	 * Method to download Xml.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function downloadXml()
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

			$contentType = 'application/xml';
			$content     = '';

			// Download single XML file
			if (count($cid) == 1)
			{
				$table    = RTranslationTable::getTranslationTableById($cid[0]);
				$fileName = (str_replace('#__', '', $table->name) . '_' . $table->version) . '.xml';

				if (!empty($table->xml_path) && is_file(JPATH_SITE . $table->xml_path))
				{
					$content = @file_get_contents(JPATH_SITE . $table->xml_path);
				}
			}
			// Download package of XML files in a ZIP
			else
			{
				$app         = JFactory::getApplication();
				$contentType = 'application/zip';
				$fileName    = 'translation_tables_' . (date('Y-m-d')) . '.zip';
				$files       = array();

				foreach ($cid as $id)
				{
					$table = RTranslationTable::getTranslationTableById($id);

					if (!empty($table->xml_path) && is_file(JPATH_SITE . $table->xml_path))
					{
						$content = @file_get_contents(JPATH_SITE . $table->xml_path);

						$files[] = array(
							'name' => (str_replace('#__', '', $table->name) . '_' . $table->version) . '.xml',
							'data' => $content,
							'time' => time()
						);
					}
				}

				$uniqueFile = uniqid('translation_content_files_');
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
						$this->setError(JText::_('COM_REDCORE_TRANSLATION_TABLE_ERR_ZIP_DELETE_FAILURE'));

						return false;
					}
				}

				if (!$packager = JArchive::getAdapter('zip'))
				{
					$this->setError(JText::_('COM_REDCORE_TRANSLATION_TABLE_ERR_ZIP_ADAPTER_FAILURE'));

					return false;
				}
				elseif (!$packager->create($zipFile, $files))
				{
					$this->setError(JText::_('COM_REDCORE_TRANSLATION_TABLE_ERR_ZIP_CREATE_FAILURE'));

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

	/**
	 * Method to update from Content Element Xml.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function updateFromXml()
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
			$success = true;

			foreach ($cid as $id)
			{
				$table = RTranslationTable::getTranslationTableById($id);

				if ($table)
				{
					if (!RTranslationTable::installContentElement($table->extension_name, $table->xml_path, $fullPath = true, $notifications = true))
					{
						$this->setMessage(JText::sprintf('COM_REDCORE_TRANSLATION_TABLE_UNABLE_TO_UPDATE_TABLE', $table->title, $table->xml_path), 'error');
						$success = false;

						break;
					}
				}
			}

			// Truncate the items.
			if ($success)
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_UPDATED', count($cid)));
			}
		}

		// Set redirect
		$this->setRedirect($this->getRedirectToListRoute());
	}

	/**
	 * Method to purge Content Element Table.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function purgeTable()
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

			// Truncate the items.
			if (RTranslationTable::purgeTables($cid))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_PURGED', count($cid)));
			}
			else
			{
				$this->setMessage(JText::_('COM_REDCORE_TRANSLATION_TABLE_UNABLE_TO_PURGE_TABLE'), 'error');
			}
		}

		// Set redirect
		$this->setRedirect($this->getRedirectToListRoute());
	}

	/**
	 * Method to delete Content Element Xml file.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function deleteXmlFile()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$app = JFactory::getApplication();

		$option  = $app->input->getString('component');
		$xmlFile = $app->input->getString('contentElement');

		if ($xmlFile == 'all')
		{
			if (RTranslationTable::batchContentElements($option, 'delete'))
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_TRANSLATION_TABLE_CONTENT_ELEMENT_DELETED'), 'message');
			}
			else
			{
				$this->setMessage(JText::_('COM_REDCORE_TRANSLATION_TABLE_UNABLE_TO_DELETE_XML'), 'error');
			}
		}
		else
		{
			if (RTranslationTable::deleteContentElement($option, $xmlFile))
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_TRANSLATION_TABLE_CONTENT_ELEMENT_DELETED'), 'message');
			}
			else
			{
				$this->setMessage(JText::_('COM_REDCORE_TRANSLATION_TABLE_UNABLE_TO_DELETE_XML'), 'error');
			}
		}

		// Set redirect
		$this->setRedirect($this->getRedirectToListRoute());
	}

	/**
	 * Method to upload Content Element file.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function uploadXmlFile()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$app   = JFactory::getApplication();
		$files = $app->input->files->get('redcoreContentElement', array(), 'array');

		if (!empty($files))
		{
			$uploadedFiles = RTranslationTable::uploadContentElement($files);

			if (!empty($uploadedFiles))
			{
				$app->enqueueMessage(JText::_('COM_REDCORE_TRANSLATION_TABLE_UPLOAD_SUCCESS'));
			}
		}
		else
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_TRANSLATION_TABLE_UPLOAD_FILE_NOT_FOUND'), 'warning');
		}

		// Set redirect
		$this->setRedirect($this->getRedirectToListRoute());
	}
}
