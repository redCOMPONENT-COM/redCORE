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
 * Represents a group of buttons.
 *
 * @package     RedRad
 * @subpackage  Toolbar
 * @since       1.0
 */
class RToolbarButtonGroup
{
	/**
	 * The buttons in the group.
	 *
	 * @var  RToolbarButton[]
	 */
	protected $buttons;

	/**
	 * Add a button to the group.
	 *
	 * @param   RToolbarButton  $button  The button to add.
	 *
	 * @return  RToolbarButtonGroup  This method is chainable.
	 */
	public function addButton(RToolbarButton $button)
	{
		$this->buttons[] = $button;

		return $this;
	}

	/**
	 * Get the buttons in the group.
	 *
	 * @return  RToolbarButton[]
	 */
	public function getButtons()
	{
		return $this->buttons;
	}
}
