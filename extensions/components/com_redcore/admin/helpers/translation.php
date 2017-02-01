<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Base View
 *
 * @package     Redcore.Backend
 * @subpackage  Helper
 * @since       1.0
 */
abstract class RedcoreHelpersTranslation extends JObject
{
	/**
	 * Gets translation column types
	 *
	 * @return  array
	 */
	public static function getTranslationColumnTypes()
	{
		return array(
			array('value' => RTranslationTable::COLUMN_PRIMARY, 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_TYPE_PRIMARY')),
			array('value' => RTranslationTable::COLUMN_READONLY, 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_TYPE_READ_ONLY')),
			array('value' => RTranslationTable::COLUMN_TRANSLATE, 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_TYPE_TRANSLATE')),
		);
	}

	/**
	 * Gets translation column value types
	 *
	 * @return  array
	 */
	public static function getTranslationValueTypes()
	{
		return array(
			array('value' => 'text', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_VALUE_TYPE_TEXT')),
			array('value' => 'titletext', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_VALUE_TYPE_TEXT_TITLE')),
			array('value' => 'htmltext', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_VALUE_TYPE_HTML_EDITOR')),
			array('value' => 'referenceid', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_VALUE_TYPE_PRIMARY_KEY')),
			array('value' => 'hiddentext', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_VALUE_TYPE_HIDDEN_TEXT')),
			array('value' => 'params', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_VALUE_TYPE_PARAMETERS')),
			array('value' => 'state', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_VALUE_TYPE_STATE')),
			array('value' => 'textarea', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_VALUE_TYPE_TEXT_AREA')),
			array('value' => 'images', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_VALUE_TYPE_IMAGES')),
			array('value' => 'readonlytext', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_VALUE_READ_ONLY')),
		);
	}

	/**
	 * Gets translation column filter types
	 *
	 * @return  array
	 */
	public static function getTranslationFilterTypes()
	{
		return array(
			array('value' => 'UINT', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_FILTER_TYPE_UINT')),
			array('value' => 'FLOAT', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_FILTER_TYPE_FLOAT')),
			array('value' => 'BOOLEAN', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_FILTER_TYPE_BOOLEAN')),
			array('value' => 'WORD', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_FILTER_TYPE_WORD')),
			array('value' => 'ALNUM', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_FILTER_TYPE_ALNUM')),
			array('value' => 'CMD', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_FILTER_TYPE_CMD')),
			array('value' => 'BASE64', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_FILTER_TYPE_BASE64')),
			array('value' => 'STRING', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_FILTER_TYPE_STRING')),
			array('value' => 'HTML', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_FILTER_TYPE_HTML')),
			array('value' => 'ARRAY', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_FILTER_TYPE_ARRAY')),
			array('value' => 'PATH', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_FILTER_TYPE_PATH')),
			array('value' => 'TRIM', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_FILTER_TYPE_TRIM')),
			array('value' => 'RAW', 'text' => JText::_('COM_REDCORE_TRANSLATION_COLUMN_FILTER_TYPE_RAW')),
		);
	}

	/**
	 * Gets table row count
	 *
	 * @param   string  $table  Table object
	 *
	 * @return  array  Calculations
	 */
	public static function getTableRowCount($table)
	{
		$db	= JFactory::getDbo();
		$rowCount = array(
			'original_rows' => 0,
			'translation_rows' => array()
		);

		if (!RTranslationTable::getTableColumns($table->name))
		{
			return $rowCount;
		}

		try
		{
			$query = $db->getQuery(true);

			// Original rows
			$query->select('count(*) AS original_rows')
				->from($db->qn($table->name, 'o'));

			if (!empty($table->filter_query))
			{
				$query->where((string) $table->filter_query);
			}

			$rowCount['original_rows'] = $db->setQuery($query)->loadResult();
		}
		catch (RuntimeException $e)
		{
			JLog::add(JText::_('COM_REDCORE_TRANSLATION_TABLE_DONT_EXIST') . ' ' . $table->name, JLog::ERROR, 'jerror');
		}

		try
		{
			$query = $db->getQuery(true);

			// Original rows
			$query->select('count(*) AS translation_rows, t.rctranslations_language')
				->from($db->qn(RTranslationTable::getTranslationsTableName($table->name), 't'))
				->group('t.rctranslations_language');

			$leftJoinOn = array();

			$primaryKeys = explode(',', $table->primary_columns);

			if ($primaryKeys)
			{
				foreach ($primaryKeys as $primaryKey)
				{
					$leftJoinOn[] = 'o.' . $primaryKey . ' = t.' . $primaryKey;
					$query->where($db->qn('o.' . $primaryKey) . ' IS NOT NULL');
				}

				$leftJoinOn = implode(' AND ', $leftJoinOn);

				$query->leftJoin(
					$db->qn($table->name, 'o') . (!empty($leftJoinOn) ? ' ON ' . $leftJoinOn . '' : '')
				);
			}

			$rowCount['translation_rows'] = $db->setQuery($query)->loadObjectList('rctranslations_language');
		}
		catch (RuntimeException $e)
		{
			JLog::add(
				JText::_('COM_REDCORE_TRANSLATION_TABLE_DONT_EXIST') . ' ' . RTranslationTable::getTranslationsTableName($table->name), JLog::ERROR, 'jerror'
			);
		}

		return $rowCount;
	}
}
