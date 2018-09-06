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
 * Webservice History Log View
 *
 * @package     Redcore.Admin
 * @subpackage  Views
 * @since       1.2
 */
class RedcoreViewWebservice_History_Log extends RedcoreHelpersView
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
	 * Display method
	 *
	 * @param   string  $tpl  The template name
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->form	= $this->get('Form');
		$this->item	= $this->get('Item');

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
				'error'
			);
		}

		// Check if option is enabled
		if (RBootstrap::getConfig('enable_webservice_history_log', 0) == 0)
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
	 * @return string  The view title.
	 */
	public function getTitle()
	{
		return JText::_('COM_REDCORE_WEBSERVICE_HISTORY_LOG_TITLE');
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return RToolbar
	 */
	public function getToolbar()
	{
		$group = new RToolbarButtonGroup;

		if (empty($this->item->id))
		{
			$cancel = RToolbarBuilder::createCancelButton('webservice_history_log.cancel');
		}

		else
		{
			$cancel = RToolbarBuilder::createCloseButton('webservice_history_log.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
