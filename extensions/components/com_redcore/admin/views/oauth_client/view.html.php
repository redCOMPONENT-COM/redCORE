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
 * OAuth Client View
 *
 * @package     Redcore.Admin
 * @subpackage  Views
 * @since       1.2
 */
class RedcoreViewOauth_Client extends RedcoreHelpersView
{
	/**
	 * @var JForm
	 */
	protected $form;

	/**
	 * @var object
	 */
	protected $item;

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
		if (RBootstrap::getConfig('enable_oauth2_server', 0) == 0)
		{
			JFactory::getApplication()->enqueueMessage(
				JText::sprintf(
					'COM_REDCORE_OAUTH_CLIENTS_PLUGIN_LABEL_WARNING',
					'<a href="index.php?option=com_redcore&view=config&layout=edit&component=com_redcore">'
					. JText::_('COM_REDCORE_CONFIGURE')
					. '</a>'
				),
				'error');
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
		return JText::_('COM_REDCORE_OAUTH_CLIENT_FORM_TITLE');
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return RToolbar
	 */
	public function getToolbar()
	{
		$group = new RToolbarButtonGroup;
		$user = JFactory::getUser();

		if ($user->authorise('core.admin', 'com_redcore'))
		{
			$save = RToolbarBuilder::createSaveButton('oauth_client.apply');
			$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('oauth_client.save');
			$saveAndNew = RToolbarBuilder::createSaveAndNewButton('oauth_client.save2new');

			$group->addButton($save)
				->addButton($saveAndClose)
				->addButton($saveAndNew);
		}

		if (empty($this->item->id))
		{
			$cancel = RToolbarBuilder::createCancelButton('oauth_client.cancel');
		}

		else
		{
			$cancel = RToolbarBuilder::createCloseButton('oauth_client.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
