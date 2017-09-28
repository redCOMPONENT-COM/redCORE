<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

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
	 * @var  integer
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
		$this->state         = $model->getState();
		$this->filterForm    = $model->getForm();
		$this->pagination    = $model->getPagination();

		$this->items             = $model->getItems();
		$this->xmlFiles          = $model->getXmlFiles();
		$this->xmlFilesAvailable = $model->xmlFilesAvailable;

		$this->return = base64_encode('index.php?option=com_redcore&view=webservices');

		// Check if option is enabled
		if (RBootstrap::getConfig('enable_webservices', 0) == 0)
		{
			JFactory::getApplication()->enqueueMessage(
				JText::sprintf(
					'COM_REDCORE_WEBSERVICES_PLUGIN_LABEL_WARNING',
					'<a href="index.php?option=com_redcore&view=config&layout=edit&component=com_redcore&return=' . $this->return . '">'
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
		return JText::_('COM_REDCORE_WEBSERVICES_MANAGE');
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return RToolbar
	 */
	public function getToolbar()
	{
		$group       = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;
		$thirdGroup  = new RToolbarButtonGroup;
		$group4      = new RToolbarButtonGroup;
		$group5      = new RToolbarButtonGroup('pull-right');
		$user        = JFactory::getUser();

		if ($user->authorise('core.admin', 'com_redcore'))
		{
			$button = RToolbarBuilder::createStandardButton(
				'webservices.downloadXml', 'COM_REDCORE_TRANSLATION_TABLE_DOWNLOAD_XML', 'btn-default', 'icon-download'
			);
			$group5->addButton($button);

			$new = RToolbarBuilder::createNewButton('webservice.add');
			$group->addButton($new);

			$edit = RToolbarBuilder::createEditButton('webservice.edit');
			$group->addButton($edit);

			$publish   = RToolbarBuilder::createPublishButton('webservices.publish');
			$unPublish = RToolbarBuilder::createUnpublishButton('webservices.unpublish');

			$secondGroup->addButton($publish)
				->addButton($unPublish);

			$clone = RToolbarBuilder::createCopyButton('webservices.copy', 'btn-success');
			$thirdGroup->addButton($clone);

			$delete = RToolbarBuilder::createDeleteButton('webservices.delete');
			$group4->addButton($delete);
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($group)
			->addGroup($secondGroup)
			->addGroup($thirdGroup)
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
	 * @return  Registry
	 */
	public function getActions($section = 'component', $assetName = 'com_redcore')
	{
		$user    = JFactory::getUser();
		$result  = new Registry;
		$actions = JAccess::getActionsFromFile('com_redcore', $section) ?: array();

		foreach ($actions as $action)
		{
			$result->set($action->name,	$user->authorise($action->name, $assetName));
		}

		return $result;
	}
}
