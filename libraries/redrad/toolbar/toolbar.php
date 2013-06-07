<?php
/**
 * @package     RedRad
 * @subpackage  Toolbar
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_REDRAD') or die;

/**
 * Represents a toolbar.
 *
 * @package     RedRad
 * @subpackage  Toolbar
 * @since       1.0
 */
class RToolbar
{
	/**
	 * The buttons in the group.
	 *
	 * @var  RToolbarButton[]
	 */
	protected $groups;

	/**
	 * Add a button group to the toolbar.
	 *
	 * @param   RToolbarButtonGroup  $group  The group to add.
	 *
	 * @return  RToolbar  Tis method is chainable.
	 */
	public function addGroup(RToolbarButtonGroup $group)
	{
		$this->groups[] = $group;

		return $this;
	}

	/**
	 * Get the groups forming the toolbar.
	 *
	 * @return  RToolbarButtonGroup[]
	 */
	public function getGroups()
	{
		return $this->groups;
	}

	/**
	 * Render the toolbar.
	 *
	 * @return  string  The rendered toolbar.
	 */
	public function render()
	{
		return RLayoutHelper::render('toolbar', array('toolbar' => $this));
	}
}
