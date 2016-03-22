<?php
/**
 * @package     Redcore
 * @subpackage  Translation
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * A Translation Content helper.
 *
 * @package     Redcore
 * @subpackage  Translation
 * @since       1.0
 */
final class RTranslationContentHelper
{
	/**
	 * Method to process string for URL safe value of the form data.
	 *
	 * @param   array   $field             XML field which is being processed
	 * @param   string  &$fieldValue       Value of the field that needs to be processed
	 * @param   string  &$allValues        All values of the submitted form
	 * @param   object  $translationTable  Translation table object
	 *
	 * @return  void
	 */
	public static function filterTitle($field, &$fieldValue, &$allValues, $translationTable)
	{
		if (!empty($fieldValue))
		{
			$fieldValue = JFilterOutput::stringURLSafe($fieldValue);
		}
	}

	/**
	 * Method to process string for URL safe value of the form data.
	 *
	 * @param   array   $field             XML field which is being processed
	 * @param   string  &$fieldValue       Value of the field that needs to be processed
	 * @param   string  &$allValues        All values of the submitted form
	 * @param   object  $translationTable  Translation table object
	 *
	 * @return  void
	 */
	public static function saveUrlParams($field, &$fieldValue, &$allValues, $translationTable)
	{
		// Check for the extension specific 'request' entry.
		if (!empty($allValues['request']) && is_array($allValues['request']))
		{
			$args = array();
			parse_str(parse_url($fieldValue, PHP_URL_QUERY), $args);

			// Merge in the user supplied request arguments.
			$args = array_merge($args, $allValues['request']);
			$fieldValue = 'index.php?' . urldecode(http_build_query($args, '', '&'));
		}
	}

	/**
	 * Method to process string for URL safe value of the form data.
	 *
	 * @param   array   $field             XML field which is being processed
	 * @param   string  &$fieldValue       Value of the field that needs to be processed
	 * @param   string  &$allValues        All values of the submitted form
	 * @param   object  $translationTable  Translation table object
	 *
	 * @return  void
	 */
	public static function saveMenuPath($field, &$fieldValue, &$allValues, $translationTable)
	{
		// If there is no alias or path field, just return true.
		if (!array_key_exists('alias', $allValues) || !array_key_exists('path', $allValues))
		{
			return;
		}

		$translationTableName = RTranslationTable::getTranslationsTableName($translationTable->table, '');
		$originalTableName = $translationTable->table;

			// Get the aliases for the path from the node to the root node.
		$db	= JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('COALESCE(translation.alias, original.alias) AS alias')
			->where('n.lft BETWEEN original.lft AND original.rgt')
			->order('original.lft');
		$where = array();
		$where[] = 'translation.rctranslations_language = ' . $db->q($allValues['rctranslations_language']);

		foreach ($translationTable->primaryKeys as $primaryKey)
		{
			if (!empty($allValues[$primaryKey]))
			{
				$where[] = 'translation.' . $db->qn($primaryKey) . ' = original.' . $db->qn($primaryKey);
				$query->where('n.' . $db->qn($primaryKey) . ' = ' . $db->q($allValues[$primaryKey]));
			}
		}

		$query->from(
			$originalTableName . ' AS n, ' . $originalTableName . ' AS original LEFT JOIN ' . $translationTableName . ' AS translation ON '
			. implode(' AND ', $where)
		);

		$db->setQuery($query);
		$segments = $db->loadColumn();

		// Make sure to remove the root path if it exists in the list.
		if ($segments[0] == 'root')
		{
			array_shift($segments);
		}

		if (!empty($allValues['alias']))
		{
			$segments[count($segments) - 1] = JFilterOutput::stringURLSafe($allValues['alias']);
		}

		// Build the path.
		$fieldValue = trim(implode('/', $segments), ' /\\');
	}
}
