<?php
/**
 * @package     RedRad
 * @subpackage  Toolbar
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDRAD') or die;

/**
 * A view that can be rendered in full screen.
 *
 * @package     RedRad
 * @subpackage  Toolbar
 * @since       1.0
 */
abstract class RView extends JViewLegacy
{
	/**
	 * The component title to display in the topbar layout (if using it).
	 * It can be html.
	 *
	 * @var string
	 */
	protected $componentTitle = '';

	/**
	 * Do we have to display a sidebar ?
	 *
	 * @var  boolean
	 */
	protected $displaySidebar = false;

	/**
	 * The sidebar layout name to display.
	 *
	 * @var  boolean
	 */
	protected $sidebarLayout = '';

	/**
	 * An array of data to pass to the sidebar layout.
	 * For example the active link.
	 *
	 * @var  array
	 */
	protected $sidebarData = array();

	/**
	 * Do we have to display a topbar ?
	 *
	 * @var  boolean
	 */
	protected $displayTopBar = false;

	/**
	 * The topbar layout name to display.
	 *
	 * @var  boolean
	 */
	protected $topBarLayout = 'topbar';

	/**
	 * Do we have to display a topbar inner layout ?
	 *
	 * @var  boolean
	 */
	protected $displayTopBarInnerLayout = false;

	/**
	 * The topbar inner layout name to display.
	 *
	 * @var  boolean
	 */
	protected $topBarInnerLayout = '';

	/**
	 * An array of data to pass to the topbar inner layout.
	 * For example the active link.
	 *
	 * @var  array
	 */
	protected $topBarInnerLayoutData = array();

	/**
	 * True to display the joomla menu.
	 *
	 * @var  boolean
	 */
	protected $displayJoomlaMenu = false;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$render = RLayoutHelper::render('component',
			array(
				'view' => $this,
				'tpl' => $tpl,
				'component_title' => $this->componentTitle,
				'sidebar_display' => $this->displaySidebar,
				'sidebar_layout' => $this->sidebarLayout,
				'sidebar_data' => $this->sidebarData,
				'topbar_display' => $this->displayTopBar,
				'topbar_layout' => $this->topBarLayout,
				'topbar_inner_layout_display' => $this->displayTopBarInnerLayout,
				'topbar_inner_layout' => $this->topBarInnerLayout,
				'topbar_inner_layout_data' => $this->topBarInnerLayoutData,
				'display_joomla_menu' => $this->displayJoomlaMenu,
			)
		);

		if ($render instanceof Exception)
		{
			return $render;
		}

		echo $render;

		return true;
	}

	/**
	 * Get the view title.
	 *
	 * @return  string  The view title.
	 */
	public function getTitle()
	{
		return '';
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
	}
}
