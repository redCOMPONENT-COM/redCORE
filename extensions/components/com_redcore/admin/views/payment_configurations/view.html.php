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
 * Payment Configurations View
 *
 * @package     Redcore.Admin
 * @subpackage  Views
 * @since       1.2
 */
class RedcoreViewPayment_Configurations extends RedcoreHelpersView
{
	/**
	 * @var  array
	 */
	protected $items;

	/**
	 * @var  object
	 */
	protected $state;

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
	 * Display method
	 *
	 * @param   string  $tpl  The template name
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel();

		$this->items = $model->getItems();
		$this->state = $model->getState();
		$this->pagination = $model->getPagination();
		$this->activeFilters = $model->getActiveFilters();
		$this->filterForm = $model->getForm();

		parent::display($tpl);
	}

	/**
	 * Get the view title.
	 *
	 * @return  string  The view title.
	 */
	public function getTitle()
	{
		if ($this->getLayout() == 'test')
		{
			return JText::sprintf('COM_REDCORE_PAYMENT_CONFIGURATION_TEST_TITLE', JFactory::getApplication()->input->getString('payment_name'));
		}

		return JText::_('COM_REDCORE_PAYMENT_CONFIGURATION_LIST_TITLE');
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		if ($this->getLayout() == 'test')
		{
			return null;
		}

		$canDo = $this->getActions();
		$user = JFactory::getUser();

		$firstGroup = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;
		$thirdGroup = new RToolbarButtonGroup;

		if ($user->authorise('core.admin', 'com_redcore'))
		{
			// Edit
			if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_redcore', 'core.create'))) > 0)
			{
				$new = new RToolbarButtonStandard(
					'COM_REDCORE_PAYMENT_CONFIGURATION_NEW_CONFIGURATION', 'payment_configuration.add', 'btn-success', 'icon-file-text', '', false
				);
				$firstGroup->addButton($new);
			}

			if ($canDo->get('core.edit'))
			{
				$edit = RToolbarBuilder::createEditButton('payment_configuration.edit');
				$firstGroup->addButton($edit);
			}

			$new = new RToolbarButtonStandard(
				'COM_REDCORE_PAYMENT_CONFIGURATION_TEST_CONFIGURATION', 'payment_configurations.test', '', 'icon-file-text', '', false
			);
			$secondGroup->addButton($new);

			// Delete / Trash
			if ($canDo->get('core.delete'))
			{
				$delete = RToolbarBuilder::createDeleteButton('payment_configurations.delete');
				$thirdGroup->addButton($delete);
			}
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)
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
