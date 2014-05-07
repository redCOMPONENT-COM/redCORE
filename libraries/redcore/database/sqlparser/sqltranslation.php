<?php
/**
 * @package     Redcore
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Sql Translation class enables table and fields replacement methods
 *
 * @package     Redcore
 * @subpackage  Database
 * @since       1.0
 */
class RDatabaseSqlparserSqltranslation extends RTranslationHelper
{
	/**
	 * Checks if tables inside query have translatable tables and fields and fetch appropriate
	 * value from translations table
	 *
	 * @param   string  $sql                SQL query
	 * @param   string  $prefix             Table prefix
	 * @param   string  $language           Language tag you want to fetch translation from
	 * @param   array   $translationTables  List of translation tables
	 *
	 * @return  mixed  Parsed query with added table joins and fields if found
	 */
	public static function parseSelectQuery($sql = '', $prefix = '', $language = 'en-GB', $translationTables = array())
	{
		if (empty($translationTables))
		{
			// We do not have any translation table to check
			return null;
		}

		try
		{
			$db = JFactory::getDbo();
			$sqlParser = new RDatabaseSqlparserSqlparser($sql);
			$parsedSql = $sqlParser->parsed;

			if (!empty($parsedSql))
			{
				$foundTables = array();
				$originalTables = array();
				$parsedSqlColumns = null;
				$parsedSql = self::parseTableReplacements($parsedSql, $translationTables, $foundTables, $originalTables, $language);

				if (empty($foundTables))
				{
					// We did not find any table to translate
					return null;
				}

				// Prepare field replacement
				$columns = array();
				$columnFound = false;
				$parsedSqlColumns = $parsedSql;

				// Prepare column replacements
				foreach ($foundTables as $foundTable)
				{
					// Get all columns from that table
					$tableColumns = (array) $translationTables[$foundTable['originalTableName']]->columns;

					if (!empty($tableColumns))
					{
						$selectAllOriginalColumn = $foundTable['alias']['originalName'] . '.*';
						$columns[$selectAllOriginalColumn]['base_expr'] = $selectAllOriginalColumn;
						$columns[$selectAllOriginalColumn]['table'] = $foundTable;

						foreach ($tableColumns as $tableColumn)
						{
							$columns[$db->qn($tableColumn)]['base_expr'] = ''
								. 'COALESCE('
								. $foundTable['alias']['name']
								. '.' . $tableColumn
								. ',' . $foundTable['alias']['originalName']
								. '.' . $tableColumn
								. ')';
							$columns[$db->qn($tableColumn)]['alias'] = $db->qn($tableColumn);
							$columns[$db->qn($tableColumn)]['table'] = $foundTable;

							if (!empty($columns[$selectAllOriginalColumn]['base_expr']))
							{
								$columns[$selectAllOriginalColumn]['base_expr'] .= ',';
							}

							$columns[$selectAllOriginalColumn]['base_expr'] .= $columns[$db->qn($tableColumn)]['base_expr'] . ' AS ' . $db->qn($tableColumn);
						}
					}
				}

				$parsedSqlColumns = self::parseColumnReplacements($parsedSqlColumns, $columns, $translationTables, $columnFound);

				// We are only returning parsed SQL if we found at least one column in translation table
				if ($columnFound)
				{
					$sqlCreator = new RDatabaseSqlparserSqlcreator($parsedSqlColumns);

					return $sqlCreator->created;
				}
			}
		}
		catch (Exception $e)
		{
			return null;
		}

		return null;
	}

	/**
	 * Checks if this query is qualified for translation and parses query
	 *
	 * @param   string  $sql     SQL query
	 * @param   string  $prefix  Table prefix
	 *
	 * @return  mixed  Parsed query with added table joins and fields if found
	 */
	public static function buildTranslationQuery($sql = '', $prefix = '')
	{
		/**
		 * Basic check for translations, translation will not be inserted if:
		 * If we do not have SELECT anywhere in query
		 * If current language is site default language
		 * If we are in administration
		 */
		if (empty($sql)
			|| JFactory::getApplication()->isAdmin()
			|| !stristr(mb_strtolower($sql), 'select')
			|| RTranslationHelper::getSiteLanguage() == JFactory::getLanguage()->getTag())
		{
			return null;
		}

		$translationTables = RTranslationHelper::getInstalledTranslationTables();
		$translationTables = RTranslationHelper::removeFromEditForm($translationTables);

		return self::parseSelectQuery($sql, $prefix, JFactory::getLanguage()->getTag(), $translationTables);
	}

	/**
	 * Recursive method which go through every array and joins table if we have found the match
	 *
	 * @param   array  $parsedSqlColumns   Parsed SQL in array format
	 * @param   array  $columns            Found replacement tables
	 * @param   array  $translationTables  List of translation tables
	 * @param   bool   &$columnFound       Found at least one column from original table
	 *
	 * @return  array  Parsed query with added table joins if found
	 */
	public static function parseColumnReplacements($parsedSqlColumns, $columns, $translationTables, &$columnFound)
	{
		if (!empty($parsedSqlColumns) && is_array($parsedSqlColumns))
		{
			$db = JFactory::getDbo();

			// Replace all Tables and keys
			foreach ($parsedSqlColumns as $groupColumnsKey => $parsedColumnGroup)
			{
				if (!empty($parsedColumnGroup))
				{
					$filteredGroup = array();

					foreach ($parsedColumnGroup as $tagKey => $tagColumnsValue)
					{
						$column = null;

						if (!empty($tagColumnsValue['expr_type']) && $tagColumnsValue['expr_type'] == 'colref')
						{
							$column = self::getNameIfIncluded($tagColumnsValue['base_expr'], '', $columns, false);

							if (!empty($column))
							{
								$primaryKey = '';

								if (!empty($translationTables[$column['table']['originalTableName']]->primaryKeys))
								{
									foreach ($translationTables[$column['table']['originalTableName']]->primaryKeys as $primaryKeyValue)
									{
										$primaryKey = self::getNameIfIncluded(
											$primaryKeyValue,
											$column['table']['alias']['originalName'],
											array($tagColumnsValue['base_expr']),
											false
										);

										if (empty($primaryKey))
										{
											break;
										}
									}
								}

								// This is primary key so if only this is used in query then we do not need to parse it
								if (empty($primaryKey))
								{
									$columnFound = true;
								}

								if ($groupColumnsKey == 'ORDER' || $groupColumnsKey == 'WHERE' || $groupColumnsKey == 'GROUP')
								{
									if (!empty($primaryKey) || $groupColumnsKey != 'WHERE')//
									{
										$tagColumnsValue['base_expr'] = self::breakColumnAndReplace($tagColumnsValue['base_expr'], $column['table']['alias']['originalName']);
									}
									else
									{
										$tagColumnsValue['base_expr'] = self::breakColumnAndReplace($tagColumnsValue['base_expr'], $column['table']['alias']['name']);
									}
								}
								else
								{
									$tagColumnsValue['base_expr'] = $column['base_expr'];

									if (!empty($column['alias']) && empty($translationTables[$column['table']['originalTableName']]->tableJoinEndPosition))
									{
										$alias = $column['alias'];

										if (!empty($tagColumnsValue['alias']['name']))
										{
											$alias = $tagColumnsValue['alias']['name'];
										}

										$alias = $db->qn(self::cleanEscaping($alias));
										$tagColumnsValue['alias'] = array(
											'as' => true,
											'name' => $alias,
											'base_expr' => 'as ' . $alias
										);
									}
								}
							}
						}
						elseif (!empty($tagColumnsValue['sub_tree']))
						{
							if (!empty($tagColumnsValue['expr_type']) && in_array($tagColumnsValue['expr_type'], array('expression')))
							{
								foreach ($tagColumnsValue['sub_tree'] as $subKey => $subTree)
								{
									if (!empty($tagColumnsValue['sub_tree'][$subKey]['sub_tree']))
									{
										$tagColumnsValue['sub_tree'][$subKey]['sub_tree'] = self::parseColumnReplacements(
											array($groupColumnsKey => $tagColumnsValue['sub_tree'][$subKey]['sub_tree']),
											$columns,
											$translationTables,
											$columnFound
										);
										$tagColumnsValue['sub_tree'][$subKey]['sub_tree'] = $tagColumnsValue['sub_tree'][$subKey]['sub_tree'][$groupColumnsKey];
									}
								}
							}
							else
							{
								$tagColumnsValue['sub_tree'] = self::parseColumnReplacements(
									array($groupColumnsKey => $tagColumnsValue['sub_tree']),
									$columns,
									$translationTables,
									$columnFound
								);
								$tagColumnsValue['sub_tree'] = $tagColumnsValue['sub_tree'][$groupColumnsKey];
							}
						}

						if (!is_numeric($tagKey))
						{
							$filteredGroup[$tagKey] = $tagColumnsValue;
						}
						else
						{
							$filteredGroup[] = $tagColumnsValue;
						}
					}

					$parsedSqlColumns[$groupColumnsKey] = $filteredGroup;
				}
			}
		}

		return $parsedSqlColumns;
	}

	/**
	 * Recursive method which go through every array and joins table if we have found the match
	 *
	 * @param   array   $parsedSql          Parsed SQL in array format
	 * @param   array   $translationTables  List of translation tables
	 * @param   array   &$foundTables       Found replacement tables
	 * @param   array   &$originalTables    Found original tables used for creating unique alias
	 * @param   string  $language           Language tag you want to fetch translation from
	 *
	 * @return  array  Parsed query with added table joins if found
	 */
	public static function parseTableReplacements($parsedSql, $translationTables, &$foundTables, &$originalTables, $language)
	{
		if (!empty($parsedSql) && is_array($parsedSql))
		{
			// Replace all Tables and keys
			foreach ($parsedSql as $groupKey => $parsedGroup)
			{
				if (!empty($parsedGroup))
				{
					$filteredGroup = array();
					$filteredGroupEndPosition = array();

					foreach ($parsedGroup as $tagKey => $tagValue)
					{
						$tableName = null;
						$newTagValue = null;

						if (!empty($tagValue['expr_type']) && $tagValue['expr_type'] == 'table' && !empty($tagValue['table']))
						{
							$tableName = self::getNameIfIncluded($tagValue['table'], '', $translationTables, true);

							if (!empty($tableName))
							{
								$newTagValue = $tagValue;
								$newTagValue['originalTableName'] = $tableName;
								$newTagValue['table'] = RTranslationTable::getTranslationsTableName($tableName, '');
								$newTagValue['join_type'] = 'LEFT';
								$newTagValue['ref_type'] = 'ON';
								$alias = self::getUniqueAlias($tableName, $originalTables);

								if (!empty($newTagValue['alias']['name']))
								{
									$alias = $newTagValue['alias']['name'];
								}

								$tagValue['alias'] = array(
									'as' => true,
									'name' => $alias,
									'base_expr' => ''
								);

								$newTagValue['alias'] = array(
									'as' => true,
									'name' => self::getUniqueAlias($newTagValue['table'], $foundTables),
									'originalName' => $alias,
									'base_expr' => ''
								);

								$refClause = self::createParserJoinOperand(
									$newTagValue['alias']['name'],
									'=',
									$newTagValue['alias']['originalName'],
									$translationTables[$tableName],
									$language
								);
								$newTagValue['ref_clause'] = $refClause;
								$foundTables[$newTagValue['alias']['name']] = $newTagValue;
								$originalTables[$newTagValue['alias']['originalName']] = 1;
							}
						}
						elseif (!empty($tagValue['sub_tree']))
						{
							if (!empty($tagValue['expr_type']) && $tagValue['expr_type'] == 'expression')
							{
								foreach ($tagValue['sub_tree'] as $subKey => $subTree)
								{
									if (!empty($tagValue['sub_tree'][$subKey]['sub_tree']))
									{
										$tagValue['sub_tree'][$subKey]['sub_tree'] = self::parseTableReplacements(
											$tagValue['sub_tree'][$subKey]['sub_tree'],
											$translationTables,
											$foundTables,
											$originalTables,
											$language
										);
									}
								}
							}
							else
							{
								$tagValue['sub_tree'] = self::parseTableReplacements($tagValue['sub_tree'], $translationTables, $foundTables, $originalTables, $language);
							}
						}

						if (!is_numeric($tagKey))
						{
							$filteredGroup[$tagKey] = $tagValue;
						}
						else
						{
							$filteredGroup[] = $tagValue;
						}

						if (!empty($newTagValue))
						{
							if (!empty($translationTables[$tableName]->tableJoinEndPosition))
							{
								$filteredGroupEndPosition[] = $newTagValue;
							}
							else
							{
								$filteredGroup[] = $newTagValue;
							}
						}
					}

					foreach ($filteredGroupEndPosition as $table)
					{
						$filteredGroup[] = $table;
					}

					$parsedSql[$groupKey] = $filteredGroup;
				}
			}
		}

		return $parsedSql;
	}

	/**
	 * Creates unique Alias name not used in existing query
	 *
	 * @param   string  $originalTableName  Original table name which we use for creating alias
	 * @param   array   $foundTables        Currently used tables in the query
	 * @param   int     $counter            Auto increasing number if we already have alias with the same name
	 *
	 * @return  string  Parsed query with added table joins and fields if found
	 */
	public static function getUniqueAlias($originalTableName, $foundTables = array(), $counter = 0)
	{
		$string = str_replace('#__', '', $originalTableName);
		$string .= '_' . substr(RFilesystemFile::getUniqueName($counter), 0, 4);

		if (!empty($foundTables[$string]))
		{
			$counter++;

			return self::getUniqueAlias($originalTableName, $foundTables, $counter);
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

			if (empty($replaceWith))
			{
				return $column[1];
			}

			$column[0] = $replaceWith;
		}

		return implode('.', $column);
	}

	/**
	 * Creates array in sql Parser format, this function adds language filter as well
	 *
	 * @param   string  $newTable     Table alias of new table
	 * @param   string  $operator     Operator of joining tables
	 * @param   string  $oldTable     Alias of original table
	 * @param   object  $tableObject  Alias of original table
	 * @param   string  $language     Language tag you want to fetch translation from
	 *
	 * @return  string  Parsed query with added table joins and fields if found
	 */
	public static function createParserJoinOperand($newTable, $operator, $oldTable, $tableObject, $language)
	{
		$db = JFactory::getDbo();
		$refClause = array();

		if (!empty($tableObject->primaryKeys))
		{
			foreach ($tableObject->primaryKeys as $primaryKey)
			{
				$refClause[] = self::createParserElement('colref', $db->qn($newTable) . '.' . $primaryKey);
				$refClause[] = self::createParserElement('operator', $operator);
				$refClause[] = self::createParserElement('colref', $db->qn(self::cleanEscaping($oldTable)) . '.' . $primaryKey);

				$refClause[] = self::createParserElement('operator', 'AND');
			}
		}

		$refClause[] = self::createParserElement('colref', $db->qn($newTable) . '.rctranslations_language');
		$refClause[] = self::createParserElement('operator', '=');
		$refClause[] = self::createParserElement('colref', $db->q($language));

		$refClause[] = self::createParserElement('operator', 'AND');

		$refClause[] = self::createParserElement('colref', $db->qn($newTable) . '.rctranslations_state');
		$refClause[] = self::createParserElement('operator', '=');
		$refClause[] = self::createParserElement('colref', $db->q('1'));

		if (!empty($tableObject->tableJoinParams))
		{
			foreach ($tableObject->tableJoinParams as $join)
			{
				$leftSide = $join['left'];
				$rightSide = $join['right'];

				// Add alias if needed to the left side
				if (!empty($join['aliasLeft']))
				{
					$leftSide = ($join['aliasLeft'] == 'original' ? $db->qn(self::cleanEscaping($oldTable)) : $db->qn($newTable)) . '.' . $leftSide;
				}

				// Add alias if needed to the right side
				if (!empty($join['aliasRight']))
				{
					$rightSide = ($join['aliasRight'] == 'original' ? $db->qn(self::cleanEscaping($oldTable)) : $db->qn($newTable)) . '.' . $rightSide;
				}

				$refClause[] = self::createParserElement('operator', $join['expressionOperator']);
				$refClause[] = self::createParserElement('colref', $leftSide);
				$refClause[] = self::createParserElement('operator', $join['operator']);
				$refClause[] = self::createParserElement('colref', $rightSide);
			}
		}

		return $refClause;
	}

	/**
	 * Create Table Join Parameter
	 *
	 * @param   string  $left                Table alias of new table
	 * @param   string  $operator            Operator of joining tables
	 * @param   string  $right               Alias of original table
	 * @param   object  $aliasLeft           Alias of original table
	 * @param   string  $aliasRight          Language tag you want to fetch translation from
	 * @param   string  $expressionOperator  Language tag you want to fetch translation from
	 *
	 * @return  array  table join param
	 */
	public static function createTableJoinParam($left, $operator = '=', $right = '', $aliasLeft = null, $aliasRight = null, $expressionOperator = 'AND')
	{
		return array(
			'left' => $left,
			'operator' => $operator,
			'right' => $right,
			'aliasLeft' => $aliasLeft,
			'aliasRight' => $aliasRight,
			'expressionOperator' => $expressionOperator,
		);
	}

	/**
	 * Creates array in sql Parser format, this function adds language filter as well
	 *
	 * @param   string  $exprType  Expression type
	 * @param   string  $baseExpr  Base expression
	 * @param   bool    $subTree   Sub Tree
	 *
	 * @return  array  Parser Element in array format
	 */
	public static function createParserElement($exprType, $baseExpr, $subTree = false)
	{
		$element = array(
			'expr_type' => $exprType,
			'base_expr' => $baseExpr,
			'sub_tree' => $subTree
		);

		return $element;
	}

	/**
	 * Check for different types of field usage in field list and returns name with alias if present
	 *
	 * @param   string  $field       Field name this can be with or without quotes
	 * @param   string  $tableAlias  Table alias | optional
	 * @param   array   $fieldList   List of fields to check against
	 * @param   bool    $isTable     If we are checking against table string
	 *
	 * @return  mixed  Returns List item if Field name is included in field list
	 */
	public static function getNameIfIncluded($field, $tableAlias = '', $fieldList = array(), $isTable = false)
	{
		// No fields to search for
		if (empty($fieldList) || empty($field))
		{
			return '';
		}

		$field = self::cleanEscaping($field);
		$fieldParts = explode('.', $field);
		$alias = '';

		if (count($fieldParts) > 1)
		{
			$alias = $fieldParts[0];
			$field = $fieldParts[1];
		}

		// Check for field inclusion with various cases
		foreach ($fieldList as $fieldFromListQuotes => $fieldFromList)
		{
			if ($isTable)
			{
				switch (self::cleanEscaping($fieldFromListQuotes))
				{
					case $field:
						return $fieldFromListQuotes;
				}
			}
			elseif ($tableAlias == '')
			{
				// If this is different table we do not check columns
				if (!empty($alias) && $alias != self::cleanEscaping($fieldFromList['table']['alias']['originalName']))
				{
					continue;
				}

				switch (self::cleanEscaping($fieldFromListQuotes))
				{
					case $field:
					case self::cleanEscaping($fieldFromList['table']['alias']['originalName'] . '.' . $field):
						return $fieldFromList;
				}
			}
			else
			{
				switch (self::cleanEscaping($fieldFromList))
				{
					case $field:
					case self::cleanEscaping($tableAlias . '.' . $field):
						return $fieldFromList;
				}
			}
		}

		return '';
	}

	/**
	 * Check for database escape and remove it
	 *
	 * @param   string  $sql  Sql to check against
	 *
	 * @return  string  Returns true if Field name is included in field list
	 */
	public static function cleanEscaping($sql)
	{
		return str_replace('`', '', trim($sql));
	}
}
