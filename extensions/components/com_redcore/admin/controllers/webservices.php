<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

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
	 * Method to install Webservice.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function installWebservice()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$app = JFactory::getApplication();
		$model = $this->getModel('webservices');

		$webservice = $app->input->getString('webservice');
		$version = $app->input->getString('version');
		$folder = $app->input->getString('folder');
		$client = $app->input->getString('client');

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
		$version = $app->input->getString('version');
		$folder = $app->input->getString('folder');
		$client = $app->input->getString('client');

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
		$files  = $app->input->files->get('redcoreWebservice', array(), 'array');

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
			$model = $this->getModel('webservices');
			$installedWebservices = RApiHalHelper::getInstalledWebservices();

			foreach ($webservices as $webserviceNames)
			{
				foreach ($webserviceNames as $webserviceVersions)
				{
					foreach ($webserviceVersions as $webservice)
					{
						$client = RApiHalHelper::getWebserviceClient($webservice);
						$path = $webservice->webservicePath;
						$name = (string) $webservice->config->name;
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
}
