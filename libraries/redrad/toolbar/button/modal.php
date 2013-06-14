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
 * Represents a modal button.
 *
 * @package     RedRad
 * @subpackage  Toolbar
 * @since       1.0
 */
class RToolbarButtonModal extends RToolbarButton
{
	/**
	 * The data-target attribute.
	 *
	 * @var  string
	 */
	protected $dataTarget;

	/**
	 * Constructor.
	 *
	 * @param   string  $text        The button text.
	 * @param   string  $dataTarget  The data-target attribute.
	 * @param   string  $class       The button class.
	 * @param   string  $iconClass   The icon class.
	 */
	public function __construct($text = '', $dataTarget = '', $class = '', $iconClass = '')
	{
		parent::__construct($text, $iconClass, $class);

		$this->dataTarget = $dataTarget;
	}

	/**
	 * Get the data target attribute.
	 *
	 * @return  string  The data-target attribute.
	 */
	public function getDataTarget()
	{
		return $this->dataTarget;
	}

	/**
	 * Render the button.
	 *
	 * @return  string  The rendered button.
	 */
	public function render()
	{
		return RLayoutHelper::render('toolbar.button.modal', array('button' => $this));
	}
}
