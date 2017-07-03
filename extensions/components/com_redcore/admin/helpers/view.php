<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
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
abstract class RedcoreHelpersView extends RViewAdmin
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
	protected $displaySidebar = true;

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

	/**
	 * True to display "Version 1.0.x"
	 *
	 * @var  boolean
	 */
	protected $displayComponentVersion = true;

	/**
	 * Loaded redCore extensions
	 *
	 * @var  array
	 */
	public static $loadedRedcoreExtensions = array();

	/**
	 * Method to get all extensions that are using redCORE
	 *
	 * @param   bool  $includeRedcore  Include redcore as extension
	 *
	 * @return  array  Array of extensions
	 */
	public static function getExtensionsRedcore($includeRedcore = false)
	{
		if (empty(self::$loadedRedcoreExtensions))
		{
			/** @var RedcoreModelConfig $model */
			$model = RModelAdmin::getAdminInstance('Config', array(), 'com_redcore');

			self::$loadedRedcoreExtensions = RComponentHelper::getRedcoreComponents($includeRedcore);

			foreach (self::$loadedRedcoreExtensions as $componentKey => $componentName)
			{
				self::$loadedRedcoreExtensions[$componentKey] = $model->getComponent($componentName);
			}
		}

		return self::$loadedRedcoreExtensions;
	}
}
