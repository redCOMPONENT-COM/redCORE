<?php
/**
 * @package     Redcore
 * @subpackage  Toolbar
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Represents a toolbar.
 *
 * @package     Redcore
 * @subpackage  Toolbar
 * @since       1.0
 */
class RToolbar
{
	/**
	 * The buttons in the group.
	 *
	 * @var  RToolbarButtonGroup[]
	 */
	protected $groups = array();

	/**
	 * A css class attribute for the toolbar.
	 *
	 * @var  string
	 */
	protected $class;

	/**
	 * Constructor.
	 *
	 * @param   string  $class  The css class attribute.
	 */
	public function __construct($class = 'toolbar')
	{
		$this->class = $class;
	}

	/**
	 * Get the toolbar css class attribute.
	 *
	 * @return  string  The css class attribute.
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 * Add a button group to the toolbar.
	 *
	 * @param   RToolbarButtonGroup  $group  The group to add.
	 *
	 * @return  RToolbar  This method is chainable.
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
		return RLayoutHelper::render('toolbar.toolbar', array('toolbar' => $this));
	}

	/**
	 * Check if the toolbar is empty.
	 *
	 * @return  boolean  True if the toolbar is empty, false otherwise.
	 */
	public function isEmpty()
	{
		foreach ($this->groups as $group)
		{
			if (!$group->isEmpty())
			{
				return false;
			}
		}

		return true;
	}
}
