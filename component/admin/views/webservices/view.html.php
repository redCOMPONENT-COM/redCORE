<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Webservices View
 *
 * @package     Redcore.Admin
 * @subpackage  Views
 * @since       1.2
 */
class RedcoreViewWebservices extends RedcoreHelpersView
{
	/**
	 * @var  array
	 */
	public $items;

	/**
	 * @var  object
	 */
	public $state;

	/**
	 * @var  JPagination
	 */
	public $pagination;

	/**
	 * @var  JForm
	 */
	public $filterForm;

	/**
	 * @var array
	 */
	public $activeFilters;

	/**
	 * @var array
	 */
	public $stoolsOptions = array();

	/**
	 * @var  array
	 */
	public $xmlFiles;

	/**
	 * @var  int
	 */
	public $xmlFilesAvailable;

	/**
	 * Display method
	 *
	 * @param   string  $tpl  The template name
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel();

		$this->activeFilters = $model->getActiveFilters();
		$this->state = $model->getState();
		$this->filterForm = $model->getForm();
		$this->pagination = $model->getPagination();

		$this->items = $model->getItems();
		$this->xmlFiles = $model->getXmlFiles();
		$this->xmlFilesAvailable = $model->xmlFilesAvailable;

		$this->return = base64_encode('index.php?option=com_redcore&view=webservices');

		parent::display($tpl);
	}

	/**
	 * Get the view title.
	 *
	 * @return  string  The view title.
	 */
	public function getTitle()
	{
		return JText::_('COM_REDCORE_WEBSERVICES_MANAGE');
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return RToolbar
	 */
	public function getToolbar()
	{
		$canDo = $this->getActions();
		$group = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;
		$thirdGroup = new RToolbarButtonGroup;
		$user = JFactory::getUser();

		if ($user->authorise('core.admin', 'com_redcore'))
		{
			if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_redcore', 'core.create'))) > 0)
			{
				$new = RToolbarBuilder::createNewButton('webservice.add');
				$group->addButton($new);
			}

			if ($canDo->get('core.edit'))
			{
				$edit = RToolbarBuilder::createEditButton('webservice.edit');
				$group->addButton($edit);

				$publish = RToolbarBuilder::createPublishButton('webservices.publish');
				$unPublish = RToolbarBuilder::createUnpublishButton('webservices.unpublish');

				$secondGroup->addButton($publish)
					->addButton($unPublish);
			}

			if ($canDo->get('core.delete'))
			{
				$delete = RToolbarBuilder::createDeleteButton('webservices.delete');

				$thirdGroup->addButton($delete);
			}
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($group)
			->addGroup($secondGroup)
			->addGroup($thirdGroup);

		return $toolbar;
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string  $section    The section.
	 * @param   mixed   $assetName  The asset name.
	 *
	 * @return  JObject
	 */
	public function getActions($section = 'component', $assetName = 'com_redcore')
	{
		$user = JFactory::getUser();
		$result	= new JObject;
		$actions = JAccess::getActions('com_redcore', $section);

		foreach ($actions as $action)
		{
			$result->set($action->name,	$user->authorise($action->name, $assetName));
		}

		return $result;
	}
}
