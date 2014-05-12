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
 * Translations View
 *
 * @package     Redcore.Admin
 * @subpackage  Views
 * @since       1.0
 */
class RedcoreViewTranslations extends RedcoreHelpersView
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
	 * @var  object
	 */
	public $translationTable;

	/**
	 * @var  string
	 */
	public $contentElement;

	/**
	 * Display method
	 *
	 * @param   string  $tpl  The template name
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel('Translations');
		$this->contentElement = JFactory::getApplication()->input->getString('contentelement', '');
		$this->translationTable = RedcoreHelpersTranslation::getTranslationTable();

		if (!empty($this->contentElement))
		{
			$this->items = $model->getItems();
			$this->state = $model->getState();
			$this->pagination = $model->getPagination();
			$this->filterForm = $model->getForm();
			$this->activeFilters = $model->getActiveFilters();
		}
		else
		{

		}

		parent::display($tpl);
	}

	/**
	 * Get the view title.
	 *
	 * @return  string  The view title.
	 */
	public function getTitle()
	{
		return JText::_('COM_REDCORE_TRANSLATIONS');
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$firstGroup = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;

		//$edit = RToolbarBuilder::createEditButton('translation.edit');
		//$firstGroup->addButton($edit);

		if (!empty($this->contentElement))
		{
			// Manage
			$manage = RToolbarBuilder::createStandardButton(
				'translations.manageContentElement',
				JText::_('COM_REDCORE_TRANSLATIONS_MANAGE_CONTENT_ELEMENTS'),
				'btn btn-primary',
				'icon-globe',
				false
			);
			$secondGroup->addButton($manage);
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)
			->addGroup($secondGroup);

		return $toolbar;
	}
}
