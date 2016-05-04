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
 * Dashboard View.
 *
 * @package     Redcore.Admin
 * @subpackage  Views
 * @since       1.0
 */
class RedcoreViewDashboard extends RedcoreHelpersView
{
	/**
	 * @var  array
	 */
	protected $components;

	/**
	 * Display method
	 *
	 * @param   string  $tpl  The template name
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->components = RedcoreHelpersView::getExtensionsRedcore(true);

		parent::display($tpl);
	}

	/**
	 * Get the view title.
	 *
	 * @return  string  The view title.
	 */
	public function getTitle()
	{
		return JText::_('COM_REDCORE_DASHBOARD');
	}
}
