<?php
/**
 * @package     Redcore
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

if (version_compare(JVERSION, '3.0', 'lt'))
{
	/**
	 * redCORE Model Admin
	 *
	 * @package     Redcore
	 * @subpackage  Model
	 * @since       1.0
	 */
	class RModelAdmin extends RModelAdminBase
	{
		/**
		 * Prepare and sanitise the table data prior to saving.
		 *
		 * @param   JTable  &$table  A reference to a JTable object.
		 *
		 * @return  void
		 */
		protected function prepareTable(&$table)
		{
			$now = JDate::getInstance();
			$nowFormatted = $now->toSql();
			$userId = JFactory::getUser()->id;

			$table->bind(
				array(
					'modified_by' => $userId,
					'modified_date' => $nowFormatted,
					'modified_time' => $nowFormatted
				)
			);

			if (property_exists($table, 'created_by')
				&& (is_null($table->created_by) || empty($table->created_by)))
			{
				$table->bind(
					array(
						'created_by' => $userId,
						'created_date' => $nowFormatted,
						'created_time' => $nowFormatted
					)
				);
			}
		}
	}
}

else
{
	/**
	 * redCORE Model Admin
	 *
	 * @package     Redcore
	 * @subpackage  Model
	 * @since       1.0
	 */
	class RModelAdmin extends RModelAdminBase
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
			$now = JDate::getInstance();
			$nowFormatted = $now->toSql();
			$userId = JFactory::getUser()->id;

			$table->bind(
				array(
					'modified_by' => $userId,
					'modified_date' => $nowFormatted,
					'modified_time' => $nowFormatted
				)
			);

			if (property_exists($table, 'created_by')
				&& (is_null($table->created_by) || empty($table->created_by)))
			{
				$table->bind(
					array(
						'created_by' => $userId,
						'created_date' => $nowFormatted,
						'created_time' => $nowFormatted
					)
				);
			}
		}
	}
}
