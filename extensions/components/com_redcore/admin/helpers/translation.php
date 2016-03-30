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
	 * Gets translation table object
	 *
	 * @param   string  $default  Default Content Element Name
	 *
	 * @return  object  Translation Table object
	 */
	public static function getTranslationTable($default = '')
	{
		$contentElement = self::getCurrentContentElement();

		$translationTables = RTranslationHelper::getInstalledTranslationTables();

		return !empty($translationTables['#__' . $contentElement]) ? $translationTables['#__' . $contentElement] : null;
	}

	/**
	 * Gets content element name from request
	 *
	 * @param   string  $default  Default Content Element Name
	 *
	 * @return  string  Content element name
	 */
	public static function getCurrentContentElement($default = '')
	{
		$app = JFactory::getApplication();

		$filter = $app->getUserStateFromRequest('com_redcore.translations.translations.filter', 'filter', array(), 'array');

		$contentElement = $app->input->get->get('contentelement', null);

		if ($contentElement === null && !empty($filter['contentelement']))
		{
			$contentElement = $filter['contentelement'];
		}

		if (JFactory::getApplication()->input->get('view') == 'translation' || $contentElement === null)
		{
			$contentElement = $app->input->getString('contentelement', $default);
		}

		return $contentElement;
	}

	/**
	 * Gets translation item status
	 *
	 * @param   object  $item     Translate item object
	 * @param   array   $columns  List of columns used in translation
	 *
	 * @return  string  Translation Item status
	 */
	public static function getTranslationItemStatus($item, $columns)
	{
		if (empty($item->rctranslations_language))
		{
			return array('badge' => 'label label-danger', 'status' => 'JNONE');
		}
		elseif ($item->rctranslations_state != 1)
		{
			return array('badge' => 'label label-danger', 'status' => 'JUNPUBLISHED');
		}
		else
		{
			$originalValues = new JRegistry;

			if (is_array($item->rctranslations_originals))
			{
				$originalValues->loadArray($item->rctranslations_originals);
			}
			else
			{
				$originalValues->loadString((string) $item->rctranslations_originals);
			}

			$translationStatus = array('badge' => 'label label-success', 'status' => 'COM_REDCORE_TRANSLATIONS_STATUS_TRANSLATED');

			foreach ($columns as $column)
			{
				if (md5($item->$column) != $originalValues->get($column))
				{
					$translationStatus = array('badge' => 'label label-warning', 'status' => 'COM_REDCORE_TRANSLATIONS_STATUS_CHANGED');
					break;
				}
			}

			return $translationStatus;
		}
	}

	/**
	 * Gets translation item id and returns it
	 *
	 * @param   int     $itemid    Item id
	 * @param   string  $langCode  Language code
	 * @param   string  $pk        Primary key name
	 *
	 * @return  int     Translations item id
	 */
	public static function getTranslationItemId($itemid, $langCode, $pk)
	{
		$table = self::getTranslationTable();

		$ids = explode('###', $itemid);

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		->select('rctranslations_id')
		->from($db->qn(RTranslationTable::getTranslationsTableName($table->table, '')))
		->where('rctranslations_language=' . $db->q($langCode));

		foreach ($pk as $key => $primaryKey)
		{
			$query->where($db->qn($primaryKey) . ' = ' . $db->q($ids[$key]));
		}

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Checks if an array of data has any data
	 *
	 * @param   array    $data      Array of data to be checked
	 * @param   array    $excludes  Array of keys to be excluded from validation
	 *
	 * @return  boolean  True if the array contains data
	 */
	public static function validateEmptyTranslationData($data, $excludes = null)
	{
		// Remove excluded keys from array
		foreach ($excludes as $exclude)
		{
			unset($data[$exclude]);
		}

		// Check if arrays within the array has any data, and remove them if they don't
		foreach ($data as $key => $value)
		{
			if (is_array($value))
			{
				if (array_filter($value))
				{
					return true;
				}
				else
				{
					unset($data[$key]);
				}
			}
		}

		// Check if the rest of the keys in the array are empty
		if (array_filter($data))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
