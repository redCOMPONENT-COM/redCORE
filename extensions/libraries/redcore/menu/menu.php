<?php
/**
 * @package     Redcore
 * @subpackage  Menu
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

use Joomla\Registry\Registry;

/**
 * Represents a menu.
 * A menu is a set of trees.
 *
 * @package     Redcore
 * @subpackage  Menu
 * @since       1.0
 */
class RMenu
{
	/**
	 * The trees composing the menu.
	 *
	 * @var  RMenuTree[]
	 */
	protected $trees = array();

	/**
	 * Constructor.
	 *
	 * @param   RMenuTree[]  $trees  An array of trees.
	 */
	public function __construct(array $trees = array())
	{
		foreach ($trees as $tree)
		{
			$this->addTree($tree);
		}
	}

	/**
	 * Add a tree to the menu.
	 *
	 * @param   RMenuTree  $tree  The tree to add.
	 *
	 * @return  RMenu  This method is chainable.
	 */
	public function addTree(RMenuTree $tree)
	{
		$this->trees[$tree->getName()] = $tree;

		return $this;
	}

	/**
	 * Get the trees in the menu.
	 *
	 * @return  RMenuTree[]  An array of trees.
	 */
	public function getTrees()
	{
		return $this->trees;
	}

	/**
	 * Function for resetting menu items so they can be loaded with separate language aliases
	 *
	 * @param   string  $client  Application client (site or admin)
	 *
	 * @return	array  Language list
	 */
	public static function resetJoomlaMenuItems($client = 'site')
	{
		$menu = JFactory::getApplication($client)->getMenu();
		$menu->load();
		$menuItems = $menu->getMenu();

		foreach ($menuItems as $item)
		{
			if ($item->home)
			{
				$menu->setDefault($item->id, trim($item->language));
			}

			$item = $menu->getItem($item->id);

			// Decode the item params
			$result = new Registry;
			$result->loadString($item->params);
			$item->params = $result;
		}
	}
}
