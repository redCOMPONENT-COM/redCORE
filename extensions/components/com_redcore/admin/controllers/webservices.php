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
 * Webservices Controller
 *
 * @package     Redcore.Backend
 * @subpackage  Controllers
 * @since       1.0
 */
class RedcoreControllerWebservices extends RControllerAdmin
{
	/**
	 * Download Xml.
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
				/** @var RedcoreTableWebservice $table */
				$table    = RTable::getAdminInstance('Webservice', array(), 'com_redcore');
				$fileName = '';

				if ($table && $table->load($cid[0]))
				{
					$path           = !empty($table->path) ? '/' . $table->path : '';
					$fileName       = $table->client . '.' . $table->name . '.' . $table->version . '.xml';
					$webservicePath = RApiHalHelper::getWebservicesPath() . $path . '/' . $fileName;

					if (is_file($webservicePath))
					{
						$content = @file_get_contents($webservicePath);
					}
				}
			}
			// Download package of XML files in a ZIP
			else
			{
				/** @var RedcoreTableWebservice $table */
				$table       = RTable::getAdminInstance('Webservice', array(), 'com_redcore');
				$app         = JFactory::getApplication();
				$contentType = 'application/zip';
				$fileName    = 'webservices_' . (date('Y-m-d')) . '.zip';

				$files = array();

				foreach ($cid as $id)
				{
					$table->reset();

					if ($table->load($id))
					{
						$path               = !empty($table->path) ? '/' . $table->path : '';
						$webserviceFileName = $table->client . '.' . $table->name . '.' . $table->version . '.xml';
						$webservicePath     = RApiHalHelper::getWebservicesPath() . $path . '/' . $webserviceFileName;

						if (is_file($webservicePath))
						{
							$content = @file_get_contents($webservicePath);
							$files[] = array(
								'name' => $webserviceFileName,
								'data' => $content,
								'time' => time()
							);
						}
					}
				}

				$uniqueFile = uniqid('webservice_files_');
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

						return false;
					}
				}

				if (!$packager = JArchive::getAdapter('zip'))
				{
					$this->setError(JText::_('COM_REDCORE_WEBSERVICES_ERR_ZIP_ADAPTER_FAILURE'));

					return false;
				}
				elseif (!$packager->create($zipFile, $files))
				{
					$this->setError(JText::_('COM_REDCORE_WEBSERVICES_ERR_ZIP_CREATE_FAILURE'));

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
	 * Method to install Webservice.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function installWebservice()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$app   = JFactory::getApplication();
		$model = $this->getModel('webservices');

		$webservice = $app->input->getString('webservice');
		$version    = $app->input->getString('version');
		$folder     = $app->input->getString('folder');
		$client     = $app->input->getString('client');

		if ($webservice == 'all')
		{
			$this->batchWebservices('install');
		}
		else
		{
			if ($model->installWebservice($client, $webservice, $version, $folder))
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_WEBSERVICES_WEBSERVICE_INSTALLED'), 'message');
			}
		}

		$this->redirectAfterAction();
	}

	/**
	 * Method to delete Content Element file.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function deleteWebservice()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$app   = JFactory::getApplication();
		$model = $this->getModel('webservices');

		$webservice = $app->input->getString('webservice');
		$version    = $app->input->getString('version');
		$folder     = $app->input->getString('folder');
		$client     = $app->input->getString('client');

		if ($webservice == 'all')
		{
			$this->batchWebservices('delete');
		}
		else
		{
			$model->deleteWebservice($client, $webservice, $version, $folder);
		}

		$this->redirectAfterAction();
	}

	/**
	 * Method to upload Content Element file.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function uploadWebservice()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$app   = JFactory::getApplication();
		$files = $app->input->files->get('redcoreWebservice', array(), 'array');

		if (!empty($files))
		{
			$uploadedFiles = RApiHalHelper::uploadWebservice($files);

			if (!empty($uploadedFiles))
			{
				$app->enqueueMessage(JText::_('COM_REDCORE_WEBSERVICES_UPLOAD_SUCCESS'));
			}
		}
		else
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_WEBSERVICES_UPLOAD_FILE_NOT_FOUND'), 'warning');
		}

		$this->redirectAfterAction();
	}

	/**
	 * Preforms Batch action against all Webservices
	 *
	 * @param   string  $action  Action to preform
	 *
	 * @return  boolean  Returns true if Action was successful
	 */
	public function batchWebservices($action = '')
	{
		$webservices = RApiHalHelper::getWebservices();

		if (!empty($webservices))
		{
			$model                = $this->getModel('webservices');
			$installedWebservices = RApiHalHelper::getInstalledWebservices();

			foreach ($webservices as $webserviceNames)
			{
				foreach ($webserviceNames as $webserviceVersions)
				{
					foreach ($webserviceVersions as $webservice)
					{
						$client  = RApiHalHelper::getWebserviceClient($webservice);
						$path    = $webservice->webservicePath;
						$name    = (string) $webservice->config->name;
						$version = (string) $webservice->config->version;

						// If it is already install then we skip it
						if (!empty($installedWebservices[$client][$name][$version]))
						{
							continue;
						}

						switch ($action)
						{
							case 'install':
								$model->installWebservice($client, $name, $version, $path);
								break;
							case 'delete':
								$model->deleteWebservice($client, $name, $version, $path);
								break;
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * Method to redirect after action.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function redirectAfterAction()
	{
		if ($returnUrl = $this->input->get('return', '', 'Base64'))
		{
			$this->setRedirect(JRoute::_(base64_decode($returnUrl), false));
		}
		else
		{
			parent::display();
		}
	}

	/**
	 * Method to publish a list of items
	 *
	 * @return  void
	 */
	public function copy()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to copy from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		if (empty($cid))
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel('Webservices');

			// Make sure the item ids are integers
			ArrayHelper::toInteger($cid);

			// Copy the items.
			if ($model->copy($cid))
			{
				$ntext = $this->text_prefix . '_N_ITEMS_COPIED';
				$this->setMessage(JText::plural($ntext, count($cid)));
			}
			else
			{
				$this->setMessage($model->getError(), 'error');
			}
		}

		// Set redirect
		$this->setRedirect($this->getRedirectToListRoute());
	}
}
