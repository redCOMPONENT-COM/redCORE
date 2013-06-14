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
	protected $buttons = array();

	/**
	 * A css class attribute for the group.
	 *
	 * @var  string
	 */
	protected $class;

	/**
	 * Constructor.
	 *
	 * @param   string  $class  The css class attribute.
	 */
	public function __construct($class = '')
	{
		$this->class = $class;
	}

	/**
	 * Get the group css class attribute.
	 *
	 * @return  string  The css class attribute.
	 */
	public function getClass()
	{
		return $this->class;
	}

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
