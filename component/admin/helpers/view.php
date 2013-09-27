<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Base View
 *
 * @package     Redcore.Backend
 * @subpackage  Views
 * @since       1.0
 */
abstract class RedcoreHelpersView extends RView
{
	/**
	 * The component title to display in the topbar layout (if using it).
	 * It can be html.
	 *
	 * @var  string
	 */
	protected $componentTitle = 'red<strong>CORE</strong>';

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
	protected $sidebarLayout = 'sidebar';

	/**
	 * Do we have to display a topbar ?
	 *
	 * @var  boolean
	 */
	protected $displayTopBar = true;

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
	protected $displayTopBarInnerLayout = true;

	/**
	 * The topbar inner layout name to display.
	 *
	 * @var  boolean
	 */
	protected $topBarInnerLayout = 'topnav';
}
