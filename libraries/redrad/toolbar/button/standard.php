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
 * Represents a standard button.
 *
 * @package     RedRad
 * @subpackage  Toolbar
 * @since       1.0
 */
class RToolbarButtonStandard extends RToolbarButton
{
	/**
	 * The button task.
	 *
	 * @var  string
	 */
	protected $task;

	/**
	 * Is this applying on a list ?
	 *
	 * @var  boolean
	 */
	protected $list;

	/**
	 * Constructor.
	 *
	 * @param   string   $text  The button text.
	 * @param   string   $icon  The icon class.
	 * @param   string   $task  The button task.
	 * @param   boolean  $list  Is the button applying on a list ?
	 */
	public function __construct($text = '', $icon = '', $task = '', $list = true)
	{
		parent::__construct($text, $icon);

		$this->task = $task;
	}

	/**
	 * Get the button task.
	 *
	 * @return  string  The task.
	 */
	public function getTask()
	{
		return $this->task;
	}

	/**
	 * Check if the button applies on a list.
	 *
	 * @return  boolean  True if applying on a list, false otherwise.
	 */
	public function isList()
	{
		return $this->list;
	}

	/**
	 * Render the button.
	 *
	 * @return  string  The rendered button.
	 */
	public function render()
	{
		return RLayoutHelper::render('toolbar.button.standard', array('button' => $this));
	}
}
