<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2018 redCOMPONENT.com. All rights reserved.
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
class RedcoreViewWebservice_History_Logs extends RedcoreHelpersView
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
		$this->items         = $model->getItems();

		$this->return = base64_encode('index.php?option=com_redcore&view=webservice_history_logs');

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

		// Check if option is enabled
		if (RBootstrap::getConfig('enable_webservice_history_log', 1) == 0)
		{
			JFactory::getApplication()->enqueueMessage(
				JText::sprintf(
					'COM_REDCORE_WEBSERVICES_HISTORY_LOGS_PLUGIN_LABEL_WARNING',
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
		return JText::_('COM_REDCORE_WEBSERVICE_HISTORY_LOGS_TITLE');
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
		$user        = JFactory::getUser();

		if ($user->authorise('core.admin', 'com_redcore'))
		{
			$button = RToolbarBuilder::createStandardButton(
				'webservice_history_logs.downloadResponseData',
				'COM_REDCORE_WEBSERVICE_HISTORY_LOGS_DOWNLOAD_RESPONSE_DATA',
				'btn-default',
				'icon-download'
			);
			$group->addButton($button);

			$delete = RToolbarBuilder::createDeleteButton('webservice_history_logs.delete');
			$secondGroup->addButton($delete);
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($group)
			->addGroup($secondGroup);

		return $toolbar;
	}
}
