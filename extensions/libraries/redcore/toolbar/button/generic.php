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
 * Represents a generic button.
 *
 * @package     Redcore
 * @subpackage  Toolbar
 * @since       1.0
 */
class RToolbarButtonGeneric extends RToolbarButton
{
	/**
	 * @var  string
	 */
	public $type = null;

	/**
	 * @var  string
	 */
	public $model = '';

	/**
	 * Constructor.
	 *
	 * @param   string  $type  Button layout form
	 */
	public function __construct($type)
	{
		$this->type = $type;
	}

	/**
	 * Render the button.
	 *
	 * @param   boolean  $isOption  Is menu option?
	 *
	 * @return  string  The rendered button.
	 */
	public function render($isOption = false)
	{
		return RLayoutHelper::render(
			$this->type,
			array(
				'button' => $this,
				'isOption' => $isOption
			)
		);
	}
}
