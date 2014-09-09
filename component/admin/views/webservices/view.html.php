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
	 * @var  string
	 */
	public $webservice;

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
		$this->missingWebservices = null;

		$this->return = base64_encode('index.php?option=com_redcore&view=webservices&webservice=');

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
	 * @return  RToolbar

	public function getToolbar()
	{
		$firstGroup = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;

		$delete = RToolbarBuilder::createDeleteButton('webservices.delete');
		$firstGroup->addButton($delete);

		// Manage
		$manage = RToolbarBuilder::createStandardButton(
			'webservices.manageWebservice',
			JText::_('COM_REDCORE_WEBSERVICES_MANAGE_CONTENT_ELEMENTS'),
			'btn btn-primary',
			'icon-globe',
			false
		);
		$secondGroup->addButton($manage);

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)
			->addGroup($secondGroup);

		return $toolbar;
	}*/
}
