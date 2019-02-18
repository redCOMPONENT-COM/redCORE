<?php
/**
 * @package     Redcore
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Field to load a list of database tables
 *
 * @package     Redcore
 * @subpackage  Field
 * @since       1.4
 */
class JFormFieldTablelist extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.4
	 */
	public $type = 'Tablelist';

	/**
	 * Cached array of the items.
	 *
	 * @var    array
	 * @since  1.4
	 */
	protected static $cache = array();

	/**
	 * Method to get the options to populate to populate list
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.4
	 */
	protected function getOptions()
	{
		// Accepted modifiers
		$hash = md5($this->element);

		if (!isset(static::$cache[$hash]))
		{
			static::$cache[$hash] = parent::getOptions();

			$db = JFactory::getDbo();
			$tables = $db->getTableList();
			$tablePrefix = $db->getPrefix();
			$options = array();

			if (!empty($tables))
			{
				foreach ($tables as $i => $table)
				{
					// Make sure we get the right tables based on prefix
					if (stripos($table, $tablePrefix) !== 0)
					{
						continue;
					}

					$table = substr($table, strlen($tablePrefix));
					$options[] = JHtml::_('select.option', $table, $table);
				}

				static::$cache[$hash] = array_merge(static::$cache[$hash], $options);
			}
		}

		return static::$cache[$hash];
	}
}
