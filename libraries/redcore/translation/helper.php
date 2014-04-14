<?php
/**
 * @package     Redcore
 * @subpackage  Component
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * A Translation helper.
 *
 * @package     Redcore
 * @subpackage  Component
 * @since       1.0
 */
final class RTranslationHelper
{
	/**
	 * Defines if jQuery Migrate should be loaded in Frontend component/modules
	 *
	 * @var    bool
	 */
	public static $pluginParams = null;

	/**
	 * An array to hold tables from database
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $contentElements = array();

	/**
	 * An array to hold tables from database
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $installedTranslationTables = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		self::$pluginParams = new JRegistry;
	}

	/**
	 * Loading of related XML files
	 *
	 * @param   string  $extensionName  Extension name
	 *
	 * @return  array  List of objects
	 */
	public static function loadContentElements($extensionName = '')
	{
		jimport('joomla.filesystem.folder');
		$extensions = array();

		if (empty($extensionName))
		{
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator(JPATH_SITE . '/media/redcore/translations')
			);

			/** @var SplFileInfo $fileInfo */
			foreach ($iterator as $fileInfo)
			{
				if ($fileInfo->isDir())
				{
					$extensions[] = $fileInfo->getFilename();
				}
			}
		}
		else
		{
			$extensions[] = $extensionName;
		}

		foreach ($extensions as $extension)
		{
			$contentElementsXml = array();
			$contentElementsXmlRedcorePath = RTranslationContentElement::getContentElementFolderPath($extension, true);

			if (is_dir($contentElementsXmlRedcorePath))
			{
				$contentElementsXml = JFolder::files($contentElementsXmlRedcorePath, '.xml', true);
			}

			$contentElementsXmlExtensionPath = RTranslationContentElement::getContentElementFolderPath($extension);

			if (is_dir($contentElementsXmlExtensionPath))
			{
				$contentElementsXmlExtension = JFolder::files($contentElementsXmlExtensionPath, '.xml', true);

				if (!empty($contentElementsXmlExtension))
				{
					$contentElementsXml = array_merge($contentElementsXml, $contentElementsXmlExtension);
				}
			}

			if (!empty($contentElementsXml))
			{
				self::$contentElements[$extension] = array();

				foreach ($contentElementsXml as $contentElementXml)
				{
					$contentElement = new RTranslationContentElement($extension, $contentElementXml);
					self::$contentElements[$extension][$contentElement->table] = $contentElement;
				}
			}
		}
	}

	/**
	 * Loading of related XML files
	 *
	 * @param   string  $extensionName  Extension name
	 *
	 * @return  array  List of objects
	 */
	public static function getContentElements($extensionName = '')
	{
		if (empty(self::$contentElements))
		{
			self::loadContentElements($extensionName);
		}

		if (!empty(self::$contentElements[$extensionName]))
		{
			return self::$contentElements[$extensionName];
		}

		return null;
	}

	/**
	 * Loading of related XML files
	 *
	 * @param   string  $extensionName       Extension name
	 * @param   string  $contentElementsXml  XML File name
	 *
	 * @return  mixed  RTranslationContentElement if found or null
	 */
	public static function getContentElement($extensionName = '', $contentElementsXml = '')
	{
		$contentElements = self::getContentElements($extensionName);

		if (!empty($contentElements))
		{
			foreach ($contentElements as $contentElement)
			{
				if ($contentElement->contentElementXml == $contentElementsXml)
				{
					return $contentElement;
				}
			}
		}

		return null;
	}

	/**
	 * Get list of all translation tables with columns
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function getInstalledTranslationTables()
	{
		if (!isset(self::$installedTranslationTables))
		{
			$db = JFactory::getDbo();

			// We do not want to translate this value
			$db->translate = false;

			$component = JComponentHelper::getComponent('com_redcore');

			// We put translation check back on
			$db->translate = true;
			self::$installedTranslationTables = (array) $component->params->get('translations', array());
		}

		return self::$installedTranslationTables;
	}

	/**
	 * Set a value to translation table list
	 *
	 * @param   string  $table    Table name
	 * @param   string  $columns  Columns which are set in the table
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function setInstalledTranslationTables($table, $columns)
	{
		if (empty($columns))
		{
			unset(self::$installedTranslationTables[$table]);
		}
		else
		{
			self::$installedTranslationTables[$table] = $columns;
		}
	}

	/**
	 * Checks if tables inside query have translatable tables and fields and fetch appropriate
	 * value from translations table
	 *
	 * @param   string  $sql     SQL query
	 * @param   string  $prefix  Table prefix
	 *
	 * @return  string  Parsed query with added table joins and fields if found
	 */
	public static function parseSelectQuery($sql = '', $prefix = '')
	{
		if (empty($sql))
		{
			return $sql;
		}
		$sqlOriginal = $sql;
		try
		{
			$db = JFactory::getDbo();
			$sqlParser = new RDatabaseSqlparserSqlparser($sql);
			$parsedSql = $sqlParser->parsed;
			$translationTables = self::getInstalledTranslationTables();
			$translationTablesList = array();
			foreach($translationTables as $tableKey => $tableValue)
			{
				$translationTablesList[$db->qn($tableKey)] = $tableKey;
			}

			$foundTables = array();
			if (!empty($parsedSql))
			{
				// Replace all Tables and keys
				foreach ($parsedSql as $groupKey => $parsedGroup)
				{
					if (!empty($parsedGroup))
					{
						$filteredGroup = array();
						foreach($parsedGroup as $tagKey => $tagValue)
						{
							$filteredGroup[] = $tagValue;
							if ($tagValue['expr_type'] == 'table'
								&& (!empty($translationTablesList[$tagValue['table']]) || !empty($translationTablesList[$db->qn($tagValue['table'])])))
							{
								$newTagValue = $tagValue;
								$tableName = !empty($translationTablesList[$tagValue['table']]) ? $translationTablesList[$tagValue['table']] : $translationTablesList[$db->qn($tagValue['table'])];
								$newTagValue['originalTableName'] = $tableName;
								$newTagValue['table'] = RTranslationTable::getTranslationsTableName($tableName, '');
								$newTagValue['join_type'] = 'LEFT';
								$newTagValue['ref_type'] = 'ON';

								$alias = $tableName;
								if (!empty($newTagValue['alias']['name']))
								{
									$alias = $newTagValue['alias']['name'];
								}

								$newTagValue['alias'] = array(
									'as' => true,
									'name' => self::getUniqueAlias($foundTables, $newTagValue['table']),
									'orginalName' => $alias,
									'base_expr' => ''
								);

								$refClause = self::createParserJoinOperand($newTagValue['alias']['name'], 'id', '=', $newTagValue['alias']['orginalName']);
								$newTagValue['base_expr'] = $newTagValue['table'] . ' ON ' . $newTagValue['table'] . '.id = ' . $tagValue['table'] . '.id AND ' . $newTagValue['table'] . '.language = ' . $db->q(JFactory::getLanguage()->getTag());

								$newTagValue['ref_clause'] = $refClause;

								$foundTables[$newTagValue['alias']['name']] = $newTagValue;

								$filteredGroup[] = $newTagValue;
							}
						}

						$parsedSql[$groupKey] = $filteredGroup;
					}
				}

				$parsedSqlColumns = $parsedSql;

				// Replace all fields
				if (!empty($foundTables))
				{
					// Prepare field replacement
					$columns = array();
					foreach($foundTables as $foundTable)
					{
						// Get all columns from that table
						$tableColumns = $translationTables[$foundTable['originalTableName']];
						if (!empty($tableColumns))
						{
							$tableColumns = explode(',', $tableColumns);
							$columns['*']['base_expr'] = '';
							$columns['*']['table'] = $foundTable;
							foreach($tableColumns as $tableColumn)
							{
								$columns[$db->qn($tableColumn)]['base_expr'] = 'COALESCE(' . $db->qn($foundTable['alias']['name']) . '.' . $tableColumn . ',' . $db->qn($foundTable['originalTableName']) . '.' . $tableColumn . ')';
								$columns[$db->qn($tableColumn)]['table'] = $foundTable;

								if (!empty($columns['*']['base_expr']))
								{
									$columns['*']['base_expr'] .= ',';
								}

								$columns['*']['base_expr'] .= $columns[$db->qn($tableColumn)]['base_expr'] . ' AS ' . $db->qn($tableColumn);
							}
						}
					}

					foreach ($parsedSqlColumns as $groupColumnsKey => $parsedColumnGroup)
					{
						if (!empty($parsedColumnGroup))
						{
							$filteredGroup = array();

							foreach($parsedColumnGroup as $tagKey => $tagColumnsValue)
							{
								if ($tagColumnsValue['expr_type'] == 'colref' && (!empty($columns[$tagColumnsValue['base_expr']]) || !empty($columns[$db->qn($tagColumnsValue['base_expr'])])))
								{
									$column = !empty($columns[$tagColumnsValue['base_expr']]) ? $columns[$tagColumnsValue['base_expr']] : $columns[$db->qn($tagColumnsValue['base_expr'])];

									if ($groupColumnsKey == 'ORDER' || $groupColumnsKey == 'WHERE')
									{
										if ($groupColumnsKey == 'WHERE' && ($tagColumnsValue['base_expr'] == 'id' || $tagColumnsValue['base_expr'] == $db->qn('id')))
										{
											$tagColumnsValue['base_expr'] = self::breakColumnAndReplace($tagColumnsValue['base_expr'], $column['table']['originalTableName']);
										}
										else
										{
											$tagColumnsValue['base_expr'] = self::breakColumnAndReplace($tagColumnsValue['base_expr'], $column['table']['alias']['name']);
										}
									}
									else
									{
										if (empty($tagColumnsValue['alias']) && $tagColumnsValue['base_expr'] != '*')
										{
											$tagColumnsValue['alias'] = $tagColumnsValue['base_expr'];
										}

										$tagColumnsValue['base_expr'] = $column['base_expr'];
									}
								}

								$filteredGroup[] = $tagColumnsValue;
							}

							$parsedSqlColumns[$groupColumnsKey] = $filteredGroup;
						}
					}
				}
			}

			$sqlCreator = new RDatabaseSqlparserSqlcreator($parsedSqlColumns);

			return $sqlCreator->created;
		}
		catch (Exception $e)
		{

			return $sqlOriginal;
		}
	}

	/**
	 * Creates unique Alias name not used in existing query
	 *
	 * @param   string  $foundTables        Currently used tables in the query
	 * @param   string  $originalTableName  Original table name which we use for creating alias
	 * @param   int     $counter            Auto increasing number if we already have alias with the same name
	 *
	 * @return  string  Parsed query with added table joins and fields if found
	 */
	public static function getUniqueAlias($foundTables, $originalTableName, $counter = 0)
	{
		$string = str_replace('#__', '', $originalTableName);
		$string .= (string) $counter;

		if (!empty($foundTables[$string]))
		{
			$counter++;

			return self::getUniqueAlias($foundTables, $originalTableName, $counter);
		}

		return $string;
	}

	/**
	 * Breaks column name and replaces alias with the new one
	 *
	 * @param   string  $column       Column Name with or without prefix
	 * @param   string  $replaceWith  Alias name to replace current one
	 *
	 * @return  string  Parsed query with added table joins and fields if found
	 */
	public static function breakColumnAndReplace($column, $replaceWith)
	{
		$column = explode('.', $column);

		if (!empty($column))
		{
			if (count($column) == 1)
			{
				$column[1] = $column[0];
			}

			$column[0] = $replaceWith;
		}

		return implode('.', $column);
	}

	/**
	 * Creates array in sql Parser format, this function adds language filter as well
	 *
	 * @param   string  $newTable  Table alias of new table
	 * @param   string  $column1   Primary key for join
	 * @param   string  $operator  Operator of joining tables
	 * @param   string  $oldTable  Alias of original table
	 *
	 * @return  string  Parsed query with added table joins and fields if found
	 */
	public static function createParserJoinOperand($newTable, $column1, $operator, $oldTable)
	{
		$db = JFactory::getDbo();

		$refClause = array(
			0 => array(
				'expr_type' => 'colref',
				'base_expr' => $db->qn($newTable) . '.' . $column1,
				'sub_tree' => false
			),
			1 => array(
				'expr_type' => 'operator',
				'base_expr' => $operator,
				'sub_tree' => false
			),
			2 => array(
				'expr_type' => 'colref',
				'base_expr' => $oldTable . '.' . $column1,
				'sub_tree' => false
			),
			3 => array(
				'expr_type' => 'operator',
				'base_expr' => 'AND',
				'sub_tree' => false
			),
			4 => array(
				'expr_type' => 'colref',
				'base_expr' => $db->qn($newTable) . '.language',
				'sub_tree' => false
			),
			5 => array(
				'expr_type' => 'operator',
				'base_expr' => '=',
				'sub_tree' => false
			),
			6 => array(
				'expr_type' => 'colref',
				'base_expr' => $db->q(JFactory::getLanguage()->getTag()),
				'sub_tree' => false
			),
		);

		return $refClause;
	}
}
