<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * OAuth Client View
 *
 * @package     Redcore.Admin
 * @subpackage  Views
 * @since       1.2
 */
class RedcoreViewWebservice extends RedcoreHelpersView
{
	/**
	 * @var JForm
	 */
	public $form;

	/**
	 * @var object
	 */
	public $item;

	/**
	 * @var array
	 */
	public $fields;

	/**
	 * @var array
	 */
	public $resources;

	/**
	 * @var array
	 */
	public $formData;

	/**
	 * Display method
	 *
	 * @param   string  $tpl  The template name
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel('webservice');
		$this->form	= $this->get('Form');
		$this->item	= $this->get('Item');
		$this->fields = $model->fields;
		$this->resources = $model->resources;
		$this->formData = $model->formData;

		// Check if option is enabled
		if (RBootstrap::getConfig('enable_webservices', 0) == 0)
		{
			JFactory::getApplication()->enqueueMessage(
				JText::sprintf(
					'COM_REDCORE_WEBSERVICES_PLUGIN_LABEL_WARNING',
					'<a href="index.php?option=com_redcore&view=config&layout=edit&component=com_redcore">'
					. JText::_('COM_REDCORE_CONFIGURE')
					. '</a>'
				),
				'error');
		}

		parent::display($tpl);
	}

	/**
	 * Get the view title.
	 *
	 * @return string  The view title.
	 */
	public function getTitle()
	{
		return JText::_('COM_REDCORE_WEBSERVICE_TITLE');
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return RToolbar
	 */
	public function getToolbar()
	{
		$group = new RToolbarButtonGroup;
		$user = JFactory::getUser();

		if ($user->authorise('core.admin', 'com_redcore'))
		{
			$save = RToolbarBuilder::createSaveButton('webservice.apply');
			$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('webservice.save');
			$saveAndNew = RToolbarBuilder::createSaveAndNewButton('webservice.save2new');

			$group->addButton($save)
				->addButton($saveAndClose)
				->addButton($saveAndNew);
		}

		if (empty($this->item->id))
		{
			$cancel = RToolbarBuilder::createCancelButton('webservice.cancel');
		}

		else
		{
			$cancel = RToolbarBuilder::createCloseButton('webservice.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
