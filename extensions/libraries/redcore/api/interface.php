<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * Interface to handle api calls
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.2
 */
interface RApiInterface
{
	/**
	 * Method to execute task.
	 *
	 * @return  RApi  The Api output result.
	 *
	 * @since   1.2
	 */
	public function execute();

	/**
	 * Method to render the output.
	 *
	 * @return  string  The Api output result.
	 *
	 * @since   1.2
	 */
	public function render();
}
