<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
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
		$model = $this->getModel();
		$app = JFactory::getApplication();
		$this->contentElementName   = RedcoreHelpersTranslation::getCurrentContentElement();
		$this->componentName        = $app->input->get->get('component', $model->getState('filter.component', ''));

		$this->activeFilters = $model->getActiveFilters();
		$this->state = $model->getState();
		$this->filterForm = $model->getForm();
		$this->pagination = $model->getPagination();

		if (!empty($this->contentElementName))
		{
			$this->translationTable = RedcoreHelpersTranslation::getTranslationTable();
			$this->contentElement = RTranslationHelper::getContentElement($this->translationTable->option, $this->translationTable->xml);
			$this->items = $model->getItems();
			$this->filterForm->removeField('component', 'filter');
		}
		else
		{
			/** @var RedcoreModelConfig $modelConfig */
			$modelConfig = RModelAdmin::getInstance('Config', 'RedcoreModel', array('ignore_request' => true));

			if (!empty($this->componentName))
			{
				$this->component = $modelConfig->getComponent($this->componentName);
			}

			$this->contentElements = $modelConfig->loadContentElements($this->componentName);
			$this->missingContentElements = $modelConfig->loadMissingContentElements($this->componentName, $this->contentElements);

			$this->return = base64_encode('index.php?option=com_redcore&view=translations&contentelement=&component=' . $this->componentName);
			$layout = 'manage';
			$this->setLayout($layout);
			$app->input->set('layout', $layout);
			$this->filterForm->removeField('language', 'filter');
			$this->filterForm->removeField('search_translations', 'filter');
			$this->filterForm->removeField('translations_limit', 'list');
			$this->filterForm->removeField('contentelement', 'filter');
		}

		// Check if option is enabled
		if (RTranslationHelper::$pluginParams->get('enable_translations', 0) == 0)
		{
			JFactory::getApplication()->enqueueMessage(
				JText::sprintf(
					'COM_REDCORE_CONFIG_TRANSLATIONS_PLUGIN_LABEL_WARNING',
					'<a href="index.php?option=com_plugins&view=plugins&filter_search=redcore">' . JText::_('COM_REDCORE_CONFIGURE') . '</a>'
				),
				'error');
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
		if (!empty($this->contentElement))
		{
			return JText::_('COM_REDCORE_TRANSLATIONS') . ' ' . JText::_($this->contentElement->name);
		}
		else
		{
			return JText::_('COM_REDCORE_TRANSLATIONS_MANAGE_CONTENT_ELEMENTS')
				. (!empty($this->componentName) ? ' : ' . JText::_($this->componentName) : '');
		}
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

		if (!empty($this->contentElement))
		{
			$delete = RToolbarBuilder::createDeleteButton('translations.delete');
			$firstGroup->addButton($delete);

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
