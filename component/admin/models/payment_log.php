<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Payment Log Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.5
 */
class RedcoreModelPayment_Log extends RModelAdmin
{
	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A reference to a JTable object.
	 *
	 * @return  void
	 */
	protected function prepareTable($table)
	{
		parent::prepareTable($table);

		$now = JDate::getInstance();
		$nowFormatted = $now->toSql();

		if ((is_null($table->created_date) || empty($table->created_date)))
		{
			$table->bind(
				array(
					'created_date' => $nowFormatted,
					'created_time' => $nowFormatted
				)
			);
		}
	}
}
