<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
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
class RedcoreControllerWebservices extends RControllerForm
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
		$app   = JFactory::getApplication();

		$webservice = $app->input->getString('webservice');
		$version = $app->input->getString('version');
		$folder = $app->input->getString('folder');
		$client = $app->input->getString('client');

		if ($webservice == 'all')
		{
			RApiHalHelper::batchWebservices('install');
		}
		else
		{
			// Load all XMLs before save to get Paths
			RApiHalHelper::getWebservices();
			RApiHalHelper::installWebservice($client, $webservice, $version, true, $folder);
		}

		$this->redirectAfterAction();
	}

	/**
	 * Method to uninstall Content Element.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function uninstallWebservice()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$app   = JFactory::getApplication();

		$webservice = $app->input->getString('webservice');
		$version = $app->input->getString('version');
		$folder = $app->input->getString('folder');
		$client = $app->input->getString('client');

		if ($webservice == 'all')
		{
			RApiHalHelper::batchWebservices('uninstall');
		}
		else
		{
			RApiHalHelper::uninstallWebservice($client, $webservice, $version, true, $folder);
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

		$webservice = $app->input->getString('webservice');
		$version = $app->input->getString('version');
		$folder = $app->input->getString('folder');
		$client = $app->input->getString('client');

		if ($webservice == 'all')
		{
			RApiHalHelper::batchWebservices('delete');
		}
		else
		{
			RApiHalHelper::deleteWebservice($client, $webservice, $version, true, $folder);
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
	 * Method to redirect after action.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 */
	public function redirectAfterAction()
	{
		if ($returnUrl = $this->input->get('return'))
		{
			$this->setRedirect(JRoute::_(base64_decode($returnUrl), false));
		}
		else
		{
			parent::edit();
		}
	}
}
