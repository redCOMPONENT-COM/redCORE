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
	public $translationTableName;

	/**
	 * Display method
	 *
	 * @param   string  $tpl  The template name
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$model                      = $this->getModel();
		$this->state                = $model->getState();
		$this->translationTableName = $model->getState('filter.translationTableName', '');
		$this->activeFilters        = $model->getActiveFilters();
		$this->filterForm           = $model->getForm();
		$this->pagination           = $model->getPagination();

		if (!empty($this->translationTableName))
		{
			$this->items                             = $model->getItems();
			$this->translationTable                  = RTranslationTable::setTranslationTableWithColumn($this->translationTableName);
			$this->translationTable->readonlyColumns = array();

			foreach ($this->translationTable->allColumns as $column)
			{
				if ($column['column_type'] == RTranslationTable::COLUMN_READONLY)
				{
					$this->translationTable->readonlyColumns[] = $column['name'];
				}
			}
		}

		// Check if option is enabled
		if (RBootstrap::getConfig('enable_translations', 0) == 0)
		{
			JFactory::getApplication()->enqueueMessage(
				JText::sprintf(
					'COM_REDCORE_TRANSLATION_TABLE_PLUGIN_LABEL_WARNING',
					'<a href="index.php?option=com_redcore&view=config&layout=edit&component=com_redcore">'
					. JText::_('COM_REDCORE_CONFIGURE')
					. '</a>'
				),
				'error'
			);
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
		$title = JText::_('COM_REDCORE_TRANSLATIONS');

		if (empty($this->translationTableName))
		{
			return $title;
		}

		$languages = version_compare(JVERSION, '3.7', '<') ? JFactory::getLanguage()->getKnownLanguages() : JLanguageHelper::getKnownLanguages();
		$rowCount  = RedcoreHelpersTranslation::getTableRowCount($this->translationTable);
		$title    .= ' ' . $this->translationTableName;
		$title    .= ' <small><small>( '
			. JText::_('COM_REDCORE_TRANSLATION_TABLE_ORIGINAL_TABLE_ROWS')
			. ' <span class="label label-primary">'
			. (isset($rowCount['original_rows']) ? $rowCount['original_rows'] : 0)
			. '</span>';

		foreach ($languages as $languageKey => $language)
		{
			$title .= ' ' . $languageKey
				. ' <span class="label label-primary">'
				. (isset($rowCount['translation_rows'][$languageKey]) ? $rowCount['translation_rows'][$languageKey]->translation_rows : 0)
				. '</span>';
		}

		return $title . ')</small></small>';
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$firstGroup  = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;

		$delete = RToolbarBuilder::createDeleteButton('translations.delete');
		$firstGroup->addButton($delete);

		// Manage
		$manage = RToolbarBuilder::createLinkButton(
			'index.php?option=com_redcore&view=translation_tables',
			JText::_('COM_REDCORE_TRANSLATIONS_MANAGE_CONTENT_ELEMENTS'),
			'icon-globe',
			'btn btn-primary'
		);
		$secondGroup->addButton($manage);

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)
			->addGroup($secondGroup);

		return $toolbar;
	}
}
