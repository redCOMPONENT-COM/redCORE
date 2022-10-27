<?php
/**
 * @package     Redcore
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2008 - 2021 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

JLoader::import('joomla.application.component.controlleradmin');

use Joomla\Utilities\ArrayHelper;

/**
 * Base Controller Admin class.
 *
 * @package     Redcore
 * @subpackage  Controller
 * @since       1.0.0
 */
abstract class RControllerAdminBase extends JControllerAdmin
{
	/**
	 * The method => state map.
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $states = array(
		'publish' => 1,
		'unpublish' => 0,
		'archive' => 2,
		'trash' => -2,
		'report' => -3
	);

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 * @since   1.0.0
	 * @throws  \Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// J2.5 compatibility
		if (empty($this->input))
		{
			$this->input = JFactory::getApplication()->input;
		}
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  \RModel|false  The model.
	 * @since   1.0.0
	 */
	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		$class = get_class($this);

		if (empty($name))
		{
			$name = strstr($class, 'Controller');
			$name = str_replace('Controller', '', $name);
			$name = RInflector::singularize($name);
		}

		if (empty($prefix))
		{
			$prefix = strstr($class, 'Controller', true) . 'Model';
		}

		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Validate request and cid parameter
	 *
	 * @return   array|false
	 * @since    2.1.0
	 * @throws   \Exception
	 */
	protected function validateRequestCids()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');

			return false;
		}

		return $cid;
	}

	/**
	 * Removes an item.
	 *
	 * @return  void
	 * @since   1.0.0
	 * @throws  \Exception
	 */
	public function delete()
	{
		$cid = $this->validateRequestCids();

		if (!$cid)
		{
			return;
		}

		// Get the model.
		/** @var \RModelList $model */
		$model = $this->getModel();

		// Make sure the item ids are integers
		jimport('joomla.utilities.arrayhelper');
		ArrayHelper::toInteger($cid);

		// Remove the items.
		if ($model->delete($cid))
		{
			$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
		}
		else
		{
			$this->setMessage($model->getError(), 'error');
		}

		// Invoke the postDelete method to allow for the child class to access the model.
		$this->postDeleteHook($model, $cid);

		// Set redirect
		$this->setRedirect($this->getRedirectToListRoute());
	}

	/**
	 * Method to publish a list of items
	 *
	 * @return  void
	 * @since   1.0.0
	 * @throws  \Exception
	 */
	public function publish()
	{
		$cid = $this->validateRequestCids();

		if (!$cid)
		{
			return;
		}

		$value = ArrayHelper::getValue($this->states, $this->getTask(), 0, 'int');

		// Get the model.
		/** @var \RModelList $model */
		$model = $this->getModel();

		// Make sure the item ids are integers
		ArrayHelper::toInteger($cid);

		// Publish the items.
		try
		{
			if ($model->publish($cid, $value))
			{
				switch ($this->getTask())
				{
					case 'publish':
						$ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
						break;

					case 'unpublish':
						$ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
						break;

					case 'archive':
						$ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
						break;

					case 'trash':
						$ntext = $this->text_prefix . '_N_ITEMS_TRASHED';
						break;

					case 'report':
						$ntext = $this->text_prefix . '_N_ITEMS_REPORTED';
						break;
				}

				$this->setMessage(JText::plural($ntext, count($cid)));
			}
			else
			{
				$this->setMessage($model->getError(), 'error');
			}
		}
		catch (Exception $e)
		{
			$this->setMessage(JText::_('JLIB_DATABASE_ERROR_ANCESTOR_NODES_LOWER_STATE'), 'error');
		}

		$extension    = $this->input->get('extension');
		$extensionURL = ($extension) ? '&extension=' . $extension : '';

		// Set redirect
		$this->setRedirect($this->getRedirectToListRoute($extensionURL));
	}

	/**
	 * Changes the order of one or more records.
	 *
	 * @return  boolean  True on success
	 * @since   1.0.0
	 * @throws  \Exception
	 */
	public function reorder()
	{
		$cid = $this->validateRequestCids();

		if (!$cid)
		{
			return false;
		}

		$inc = ($this->getTask() == 'orderup') ? -1 : 1;

		/** @var \RModelAdmin $model */
		$model  = $this->getModel();
		$return = $model->reorder($cid, $inc);

		if ($return === false)
		{
			// Reorder failed.
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());

			// Set redirect
			$this->setRedirect($this->getRedirectToListRoute(), $message, 'error');

			return false;
		}

		else
		{
			// Reorder succeeded.
			$message = JText::_('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED');

			// Set redirect
			$this->setRedirect($this->getRedirectToListRoute(), $message);

			return true;
		}
	}

	/**
	 * Method to save the submitted ordering values for records.
	 *
	 * @return  boolean  True on success
	 * @since   1.0.0
	 * @throws  \Exception
	 */
	public function saveorder()
	{
		$cid = $this->validateRequestCids();

		if (!$cid)
		{
			return false;
		}

		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		ArrayHelper::toInteger($cid);
		ArrayHelper::toInteger($order);

		// Get the model
		/** @var \RModelAdmin $model */
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($cid, $order);

		if ($return === false)
		{
			// Reorder failed
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());

			// Set redirect
			$this->setRedirect($this->getRedirectToListRoute(), $message, 'error');

			return false;
		}

		else
		{
			// Reorder succeeded.
			$this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));

			// Set redirect
			$this->setRedirect($this->getRedirectToListRoute());

			return true;
		}
	}

	/**
	 * Get the JRoute object for a redirect to list.
	 *
	 * @param   string  $append  An optional string to append to the route
	 *
	 * @return  string
	 * @since   1.0.0
	 */
	protected function getRedirectToListRoute($append = null)
	{
		$returnUrl = $this->input->get('return', '', 'Base64');

		if ($returnUrl)
		{
			$returnUrl = base64_decode($returnUrl);

			return JRoute::_($returnUrl . $append, false);
		}
		else
		{
			return JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $append, false);
		}
	}

	/**
	 * Check in of one or more records.
	 *
	 * @return  boolean  True on success
	 * @since   1.0.0
	 * @throws  \Exception
	 */
	public function checkin()
	{
		$cid = $this->validateRequestCids();

		if (!$cid)
		{
			return false;
		}

		/** @var \RModelAdmin $model */
		$model  = $this->getModel();
		$return = $model->checkin($cid);

		if ($return === false)
		{
			// Checkin failed.
			$message = JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());

			// Set redirect
			$this->setRedirect($this->getRedirectToListRoute(), $message, 'error');

			return false;
		}
		else
		{
			// Checkin succeeded.
			$message = JText::plural($this->text_prefix . '_N_ITEMS_CHECKED_IN', count($cid));

			// Set redirect
			$this->setRedirect($this->getRedirectToListRoute(), $message);

			return true;
		}
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return	void
	 * @since   1.0.0
	 * @throws  \Exception
	 */
	public function saveOrderAjax()
	{
		$cid = $this->validateRequestCids();

		if (!$cid)
		{
			return;
		}

		// Get the input
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		ArrayHelper::toInteger($cid);
		ArrayHelper::toInteger($order);

		// Get the model
		/** @var \RModelAdmin $model */
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($cid, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
}
