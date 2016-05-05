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
 * Translation Tables View
 *
 * @package     Redcore.Admin
 * @subpackage  Views
 * @since       1.2
 */
class RedcoreViewTranslation_Tables extends RedcoreHelpersView
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
	 * @var  array
	 */
	public $xmlFiles;

	/**
	 * @var  int
	 */
	public $xmlFilesAvailable;

	/**
	 * Number of available xml files for install
	 *
	 * @var  int
	 */
	public $languages = array();

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

		$this->languages = $model->languages;
		$this->items = $model->getItems();
		$this->state = $model->getState();
		$this->pagination = $model->getPagination();
		$this->activeFilters = $model->getActiveFilters();
		$this->filterForm = $model->getForm();
		$this->xmlFiles = $model->getXmlFiles();
		$this->xmlFilesAvailable = $model->xmlFilesAvailable;

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
				'error');
		}

		if (!JPluginHelper::isEnabled('system', 'languagefilter'))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_TRANSLATIONS_LANGUAGE_FILTER') . ' ' . JText::_('JDISABLED'), 'warning');
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
		return JText::_('COM_REDCORE_TRANSLATION_TABLE_LIST_TITLE');
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$canDo = $this->getActions();
		$user = JFactory::getUser();

		$firstGroup = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;
		$group3 = new RToolbarButtonGroup;
		$group5 = new RToolbarButtonGroup('pull-right');
		$group4 = new RToolbarButtonGroup('pull-right');

		if ($user->authorise('core.admin', 'com_redcore'))
		{
			$button = RToolbarBuilder::createStandardButton(
				'translation_tables.downloadXml', 'COM_REDCORE_TRANSLATION_TABLE_DOWNLOAD_XML', 'btn-default', 'icon-download'
			);
			$group4->addButton($button);

			// Add / edit
			if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_redcore', 'core.create'))) > 0)
			{
				$new = RToolbarBuilder::createNewButton('translation_table.add');
				$firstGroup->addButton($new);

				$updateFromXml = RToolbarBuilder::createStandardButton(
					'translation_tables.updateFromXml', 'COM_REDCORE_TRANSLATION_TABLE_UPDATE_FROM_XML', 'btn-primary', 'icon-refresh'
				);
				$group5->addButton($updateFromXml);

				$uploadXml = new RToolbarButtonGeneric('translation.upload');
				$group4->addButton($uploadXml);
			}

			if ($canDo->get('core.edit'))
			{
				$edit = RToolbarBuilder::createEditButton('translation_table.edit');
				$firstGroup->addButton($edit);
			}

			// Delete / Trash
			if ($canDo->get('core.delete'))
			{
				$delete = RToolbarBuilder::createDeleteButton('translation_tables.delete');
				$secondGroup->addButton($delete);

				$purge = RToolbarBuilder::createStandardButton(
					'translation_tables.purgeTable', 'COM_REDCORE_TRANSLATION_TABLE_TRUNCATE_TRANSLATIONS', 'btn-danger', 'icon-trash'
				);
				$group3->addButton($purge);
			}
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)
			->addGroup($secondGroup)
			->addGroup($group3)
			->addGroup($group4)
			->addGroup($group5);

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
