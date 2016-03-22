<?php
/**
 * @package     Redcore
 * @subpackage  Toolbar
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Represents a group of buttons.
 *
 * @package     Redcore
 * @subpackage  Toolbar
 * @since       1.0
 */
class RToolbarButtonGroup
{
	/**
	 * Determinate if button group should be shown as a menu.
	 *
	 * @var  bool
	 */
	protected $menu = false;

	/**
	 * Used for button group title for a drop down.
	 *
	 * @var  string
	 */
	protected $title = '';

	/**
	 * Determinate icon class for drop down button.
	 *
	 * @var  string
	 */
	protected $iconClass = '';

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
	 * @param   string  $class      The css class attribute.
	 * @param   bool    $isMenu     Display btn group as a menu?
	 * @param   string  $iconClass  Collapse button icon class.
	 * @param   string  $title      Collapse button title.
	 */
	public function __construct($class = '', $isMenu = false, $iconClass = '', $title = '')
	{
		$this->class     = $class;
		$this->menu      = $isMenu;
		$this->iconClass = $iconClass;
		$this->title     = $title;
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

	/**
	 * Check if the group is empty.
	 *
	 * @return  boolean  True if empty, false otherwise
	 */
	public function isEmpty()
	{
		return 0 === count($this->buttons);
	}

	/**
	 * Get the icon group css class attribute.
	 *
	 * @return  string  The css class attribute.
	 */
	public function getIconClass()
	{
		return $this->iconClass;
	}

	/**
	 * Get the group title.
	 *
	 * @return  string  Group title.
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Checks if group is a menu with options.
	 *
	 * @return  bool
	 */
	public function isMenu()
	{
		return $this->menu;
	}
}
