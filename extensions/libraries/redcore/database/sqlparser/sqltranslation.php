<?php
/**
 * @package     Redcore
 * @subpackage  Database
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
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
	 * The options.
	 *
	 * @var  array
	 */
	private static $options = array();

	/**
	 * The options.
	 *
	 * @var  array
	 */
	private static $parsedQueries = array();

	/**
	 * Checks if tables inside query have translatable tables and fields and fetch appropriate
	 * value from translations table
	 *
	 * @param   string $sql               SQL query
	 * @param   string $prefix            Table prefix
	 * @param   string $language          Language tag you want to fetch translation from
	 * @param   array  $translationTables List of translation tables
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
			$hashedQueryKey = md5($sql . $language);

			if (isset(self::$parsedQueries[$hashedQueryKey]))
			{
				return self::$parsedQueries[$hashedQueryKey];
			}

			$db                                   = JFactory::getDbo();
			self::$parsedQueries[$hashedQueryKey] = null;
			$sqlParser                            = new RDatabaseSqlparserSqlparser($sql);
			$parsedSql                            = $sqlParser->parsed;

			if (!empty($parsedSql))
			{
				$foundTables      = array();
				$originalTables   = array();
				$parsedSqlColumns = null;
				$subQueryFound    = false;
				$parsedSql        = self::parseTableReplacements($parsedSql, $translationTables, $foundTables, $originalTables, $language, $subQueryFound);

				if (empty($foundTables) && !$subQueryFound)
				{
					// We did not find any table to translate
					return null;
				}

				// Prepare field replacement
				$columns          = array();
				$columnFound      = false;
				$parsedSqlColumns = $parsedSql;

				// Prepare column replacements
				foreach ($foundTables as $foundTable)
				{
					// Get all columns from that table
					$tableColumns         = (array) $translationTables[$foundTable['originalTableName']]->columns;
					$originalTableColumns = RTranslationTable::getTableColumns($foundTable['originalTableName']);

					if (!empty($tableColumns))
					{
						$selectAllOriginalColumn = $foundTable['alias']['originalName'] . '.*';
						$columnAll               = array();
						$columnAll['table']      = $foundTable;
						$columnAll['columnName'] = $selectAllOriginalColumn;
						$columnAll['base_expr']  = self::addBaseColumns($originalTableColumns, $tableColumns, $foundTable['alias']['originalName']);

						foreach ($tableColumns as $tableColumn)
						{
							$column        = array();
							$fallbackValue = $foundTable['alias']['originalName'] . '.' . $tableColumn;

							// Check to see if fallback option is turned on, if it is not then we set empty string as value
							if (!self::getOption('translationFallback', true))
							{
								// Additionally we check for columns that must have a fallback (ex. state, publish, etc.)
								if (empty($translationTables[$foundTable['originalTableName']]->fallbackColumns)
									|| !in_array($tableColumn, $translationTables[$foundTable['originalTableName']]->fallbackColumns))
								{
									$fallbackValue = $db->q('');
								}
							}

							// If column is primary key we do not need to translate it
							if (in_array($tableColumn, $translationTables[$foundTable['originalTableName']]->primaryKeys))
							{
								$column['base_expr'] = $foundTable['alias']['originalName'] . '.' . $tableColumn;
							}
							else
							{
								$column['base_expr'] = ''
									. 'COALESCE('
									. $foundTable['alias']['name'] . '.' . $tableColumn
									. ',' . $fallbackValue
									. ')';
							}

							$column['alias']      = $db->qn($tableColumn);
							$column['table']      = $foundTable;
							$column['columnName'] = $tableColumn;

							$columns[] = $column;

							if (!empty($columnAll['base_expr']))
							{
								$columnAll['base_expr'] .= ',';
							}

							$columnAll['base_expr'] .= $column['base_expr'] . ' AS ' . $db->qn($tableColumn);
						}

						$columns[] = $columnAll;
					}
				}

				if (!empty($foundTables))
				{
					$parsedSqlColumns = self::parseColumnReplacements($parsedSqlColumns, $columns, $translationTables, $columnFound);
				}

				// We are only returning parsed SQL if we found at least one column in translation table
				if ($columnFound || $subQueryFound)
				{
					$sqlCreator                           = new RDatabaseSqlparserSqlcreator($parsedSqlColumns);
					self::$parsedQueries[$hashedQueryKey] = $sqlCreator->created;

					return self::$parsedQueries[$hashedQueryKey];
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
	 * @param   string $sql    SQL query
	 * @param   string $prefix Table prefix
	 *
	 * @return  mixed  Parsed query with added table joins and fields if found
	 */
	public static function buildTranslationQuery($sql = '', $prefix = '')
	{
		$db = JFactory::getDbo();

		$selectedLanguage = !empty($db->forceLanguageTranslation) ? $db->forceLanguageTranslation : JFactory::getLanguage()->getTag();
		$queryArray       = explode(',', (string) $sql);
		$hashValues       = array();

		// Replace long amount of numeric values if found. They do not have any affects for translation parser
		if (count($queryArray) > 50)
		{
			$index   = 0;
			$numbers = array($index => array());

			foreach ($queryArray as $key => $value)
			{
				if (is_numeric(trim($value)))
				{
					if (!array_key_exists($index, $numbers))
					{
						$numbers[$index] = array();
					}

					$numbers[$index][$key] = $value;
					unset($queryArray[$key]);
				}
				else
				{
					if (!empty($numbers[$index]))
					{
						$index++;
					}
				}
			}

			foreach ($numbers as $index => $values)
			{
				if (count($values) < 10)
				{
					foreach ($values as $key => $value)
					{
						$queryArray[$key] = $value;
					}
				}
				else
				{
					$firstKey               = key($values);
					$hashValue              = $db->q(md5(json_encode($values)));
					$queryArray[$firstKey]  = $hashValue;
					$hashValues[$hashValue] = $values;
				}
			}

			unset($numbers);
			ksort($queryArray);
			$sql = implode(',', $queryArray);
		}

		if (!empty($db->parseTablesBefore))
		{
			foreach ($db->parseTablesBefore as $tableGroup)
			{
				$sql = self::parseSelectQuery($sql, $prefix, $tableGroup->language, $tableGroup->translationTables);
			}
		}

		// If we have a SELECT in the query then we check the reset of the params
		$validSelect = (!empty($sql) && stristr(mb_strtolower($sql), 'select'));

		// If the language is the default, there is no reason to translate
		$isDefaultLanguage = (RTranslationHelper::getSiteLanguage() == $selectedLanguage) && !self::getOption('forceTranslateDefault', false);

		// If this is the admin, but no an API request we shouldn't translate
		$isAdmin = RTranslationHelper::isAdmin();

		/**
		 * Basic check for translations, translation will not be inserted if:
		 * If we do not have SELECT anywhere in query
		 * If current language is site default language
		 * If we are in administration
		 */
		if (!$validSelect
			|| $isDefaultLanguage
			|| ($isAdmin && !self::getOption('translateInAdmin', false)))
		{
			if (empty($db->parseTablesBefore) && empty($db->parseTablesAfter))
			{
				return null;
			}
		}

		$translationTables = RTranslationTable::getInstalledTranslationTables();
		$translationTables = RTranslationHelper::removeFromEditForm($translationTables);
		$sql               = self::parseSelectQuery($sql, $prefix, $selectedLanguage, $translationTables);

		if (!empty($db->parseTablesAfter))
		{
			foreach ($db->parseTablesAfter as $tableGroup)
			{
				$sql = self::parseSelectQuery($sql, $prefix, $tableGroup->language, $tableGroup->translationTables);
			}
		}

		// Turn back real long amount of numeric values
		foreach ($hashValues as $hash => $values)
		{
			$sql = str_replace($hash, implode(',', $values), $sql);
		}

		return $sql;
	}

	/**
	 * Recursive method which go through every array and joins table if we have found the match
	 *
	 * @param   array $parsedSqlColumns  Parsed SQL in array format
	 * @param   array $columns           Found replacement tables
	 * @param   array $translationTables List of translation tables
	 * @param   bool  &$columnFound      Found at least one column from original table
	 * @param   bool  $addAlias          Should we add alias after column name
	 *
	 * @return  array  Parsed query with added table joins if found
	 */
	public static function parseColumnReplacements($parsedSqlColumns, $columns, $translationTables, &$columnFound, $addAlias = true)
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
							$column = self::getNameIfIncluded($tagColumnsValue['base_expr'], '', $columns, false, $groupColumnsKey);

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
											false,
											$groupColumnsKey
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

								if (in_array($groupColumnsKey, array('FROM')))
								{
									if (empty($primaryKey) && $groupColumnsKey != 'FROM')
									{
										$tagColumnsValue['base_expr'] = self::breakColumnAndReplace($tagColumnsValue['base_expr'], $column['table']['alias']['name']);
									}
									else
									{
										$tagColumnsValue['base_expr'] = self::breakColumnAndReplace($tagColumnsValue['base_expr'], $column['table']['alias']['originalName']);
									}
								}
								else
								{
									if (in_array($groupColumnsKey, array('ORDER', 'WHERE', 'GROUP')) && !empty($primaryKey))
									{
										$tagColumnsValue['base_expr'] = self::breakColumnAndReplace($tagColumnsValue['base_expr'], $column['table']['alias']['originalName']);
									}
									else
									{
										$tagColumnsValue['base_expr'] = $column['base_expr'];

										if ($addAlias
											&& !empty($column['alias'])
											&& !in_array($groupColumnsKey, array('ORDER', 'WHERE', 'GROUP')))
										{
											$alias = $column['alias'];

											if (!empty($tagColumnsValue['alias']['name']))
											{
												$alias = $tagColumnsValue['alias']['name'];
											}

											$alias                    = $db->qn(self::cleanEscaping($alias));
											$tagColumnsValue['alias'] = array(
												'as'        => true,
												'name'      => $alias,
												'base_expr' => 'as ' . $alias
											);
										}
									}
								}
							}
						}
						elseif (!empty($tagColumnsValue['sub_tree']))
						{
							if (!empty($tagColumnsValue['expr_type']) && $tagColumnsValue['expr_type'] == 'subquery')
							{
								// SubQuery is already parsed so we do not need to parse columns again
							}
							elseif (!empty($tagColumnsValue['expr_type']) && in_array($tagColumnsValue['expr_type'], array('expression')))
							{
								foreach ($tagColumnsValue['sub_tree'] as $subKey => $subTree)
								{
									if (!empty($tagColumnsValue['sub_tree'][$subKey]['sub_tree']))
									{
										$tagColumnsValue['sub_tree'][$subKey]['sub_tree'] = self::parseColumnReplacements(
											array($groupColumnsKey => $tagColumnsValue['sub_tree'][$subKey]['sub_tree']),
											$columns,
											$translationTables,
											$columnFound,
											false
										);
										$tagColumnsValue['sub_tree'][$subKey]['sub_tree'] = $tagColumnsValue['sub_tree'][$subKey]['sub_tree'][$groupColumnsKey];
									}
								}
							}
							elseif (is_array($tagColumnsValue['sub_tree']))
							{
								// We will not replace some aggregate functions columns
								if (!(in_array($tagColumnsValue['expr_type'], array('aggregate_function')) && strtolower($tagColumnsValue['base_expr']) == 'count'))
								{
									$keys = array_keys($tagColumnsValue['sub_tree']);

									if (!is_numeric($keys[0]))
									{
										$tagColumnsValue['sub_tree'] = self::parseColumnReplacements(
											$tagColumnsValue['sub_tree'],
											$columns,
											$translationTables,
											$columnFound,
											false
										);
									}
									else
									{
										$tagColumnsValue['sub_tree'] = self::parseColumnReplacements(
											array($groupColumnsKey => $tagColumnsValue['sub_tree']),
											$columns,
											$translationTables,
											$columnFound,
											false
										);
										$tagColumnsValue['sub_tree'] = $tagColumnsValue['sub_tree'][$groupColumnsKey];
									}
								}
							}
						}
						elseif (!empty($tagColumnsValue['ref_clause']))
						{
							if (is_array($tagColumnsValue['ref_clause']))
							{
								$keys = array_keys($tagColumnsValue['ref_clause']);

								if (!is_numeric($keys[0]))
								{
									$tagColumnsValue['ref_clause'] = self::parseColumnReplacements(
										$tagColumnsValue['ref_clause'],
										$columns,
										$translationTables,
										$columnFound,
										false
									);
								}
								else
								{
									$tagColumnsValue['ref_clause'] = self::parseColumnReplacements(
										array($groupColumnsKey => $tagColumnsValue['ref_clause']),
										$columns,
										$translationTables,
										$columnFound,
										false
									);
									$tagColumnsValue['ref_clause'] = $tagColumnsValue['ref_clause'][$groupColumnsKey];
								}
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
	 * @param   array  $parsedSql         Parsed SQL in array format
	 * @param   array  $translationTables List of translation tables
	 * @param   array  &$foundTables      Found replacement tables
	 * @param   array  &$originalTables   Found original tables used for creating unique alias
	 * @param   string $language          Language tag you want to fetch translation from
	 * @param   string &$subQueryFound    If sub query is found then we must parse end sql
	 *
	 * @return  array  Parsed query with added table joins if found
	 */
	public static function parseTableReplacements($parsedSql, $translationTables, &$foundTables, &$originalTables, $language, &$subQueryFound)
	{
		if (!empty($parsedSql) && is_array($parsedSql))
		{
			// Replace all Tables and keys
			foreach ($parsedSql as $groupKey => $parsedGroup)
			{
				if (!empty($parsedGroup))
				{
					$filteredGroup            = array();
					$filteredGroupEndPosition = array();

					foreach ($parsedGroup as $tagKey => $tagValue)
					{
						$tableName   = null;
						$newTagValue = null;

						if (!empty($tagValue['expr_type']) && $tagValue['expr_type'] == 'table' && !empty($tagValue['table']))
						{
							$tableName = self::getNameIfIncluded(
								$tagValue['table'],
								!empty($tagValue['alias']['name']) ? $tagValue['alias']['name'] : '',
								$translationTables,
								true,
								$groupKey
							);

							if (!empty($tableName))
							{
								$newTagValue                      = $tagValue;
								$newTagValue['originalTableName'] = $tableName;
								$newTagValue['table']             = RTranslationTable::getTranslationsTableName($tableName, '');
								$newTagValue['join_type']         = 'LEFT';
								$newTagValue['ref_type']          = 'ON';
								$alias                            = self::getUniqueAlias($tableName, $originalTables);

								if (!empty($newTagValue['alias']['name']))
								{
									$alias = $newTagValue['alias']['name'];
								}

								$tagValue['alias'] = array(
									'as'        => true,
									'name'      => $alias,
									'base_expr' => ''
								);

								$newTagValue['alias'] = array(
									'as'           => true,
									'name'         => self::getUniqueAlias($newTagValue['table'], $foundTables),
									'originalName' => $alias,
									'base_expr'    => ''
								);

								$refClause                                             = self::createParserJoinOperand(
									$newTagValue['alias']['name'],
									'=',
									$newTagValue['alias']['originalName'],
									$translationTables[$tableName],
									$language
								);
								$newTagValue['ref_clause']                             = $refClause;
								$newTagValue['index_hints']                            = false;
								$foundTables[]                                         = $newTagValue;
								$originalTables[$newTagValue['alias']['originalName']] = isset($originalTables[$newTagValue['alias']['originalName']]) ?
									$originalTables[$newTagValue['alias']['originalName']]++ : 1;
							}
						}
						// There is an issue in sql parser for UNION and UNION ALL, this is a solution for it
						elseif (!empty($tagValue['union_tree']) && is_array($tagValue['union_tree']) && in_array('UNION', $tagValue['union_tree']))
						{
							$subQueryFound = true;
							$unionTree     = array();

							foreach ($tagValue['union_tree'] as $union)
							{
								$union = trim($union);

								if (!empty($union) && strtoupper($union) != 'UNION')
								{
									$parsedSubQuery = self::buildTranslationQuery(self::removeParenthesisFromStart($union));
									$unionTree[]    = !empty($parsedSubQuery) ? '(' . $parsedSubQuery . ')' : $union;
								}
							}

							$tagValue['base_expr'] = '(' . implode(' UNION ', $unionTree) . ')';
							$tagValue['expr_type'] = 'const';

							if (!empty($tagValue['sub_tree']))
							{
								unset($tagValue['sub_tree']);
							}

							if (!empty($tagValue['join_type']))
							{
								unset($tagValue['join_type']);
							}
						}
						// Other types of expressions
						elseif (!empty($tagValue['sub_tree']))
						{
							if (!empty($tagValue['expr_type']) && $tagValue['expr_type'] == 'subquery')
							{
								$parsedSubQuery = self::buildTranslationQuery($tagValue['base_expr']);

								if (!empty($parsedSubQuery))
								{
									$sqlParser             = new RDatabaseSqlparserSqlparser($parsedSubQuery);
									$tagValue['sub_tree']  = $sqlParser->parsed;
									$tagValue['base_expr'] = $parsedSubQuery;
									$subQueryFound         = true;
								}
							}
							elseif (!empty($tagValue['expr_type']) && in_array($tagValue['expr_type'], array('expression')))
							{
								foreach ($tagValue['sub_tree'] as $subKey => $subTree)
								{
									// In case we have a Subquery directly under the expression we handle it separately
									if (!empty($subTree['expr_type']) && $subTree['expr_type'] == 'subquery')
									{
										// We need to remove brackets from the query or else the query will not be parsed properly
										$sqlBracketLess = self::removeParenthesisFromStart($subTree['base_expr']);
										$parsedSubQuery = self::buildTranslationQuery($sqlBracketLess);

										if (!empty($parsedSubQuery))
										{
											$sqlParser                                  = new RDatabaseSqlparserSqlparser($parsedSubQuery);
											$tagValue['sub_tree'][$subKey]['sub_tree']  = $sqlParser->parsed;
											$tagValue['sub_tree'][$subKey]['base_expr'] = '(' . $parsedSubQuery . ')';
											$subQueryFound                              = true;
										}
									}
									elseif (!empty($tagValue['sub_tree'][$subKey]['sub_tree']))
									{
										$tagValue['sub_tree'][$subKey]['sub_tree'] = self::parseTableReplacements(
											$tagValue['sub_tree'][$subKey]['sub_tree'],
											$translationTables,
											$foundTables,
											$originalTables,
											$language,
											$subQueryFound
										);
									}
								}
							}
							else
							{
								$tagValue['sub_tree'] = self::parseTableReplacements(
									$tagValue['sub_tree'],
									$translationTables,
									$foundTables,
									$originalTables,
									$language,
									$subQueryFound
								);
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
	 * @param   string $originalTableName Original table name which we use for creating alias
	 * @param   array  $foundTables       Currently used tables in the query
	 * @param   int    $counter           Auto increasing number if we already have alias with the same name
	 *
	 * @return  string  Parsed query with added table joins and fields if found
	 */
	public static function getUniqueAlias($originalTableName, $foundTables = array(), $counter = 0)
	{
		$string = str_replace('#__', '', $originalTableName);
		$string .= '_' . substr(RFilesystemFile::getUniqueName($counter), 0, 4);

		foreach ($foundTables as $foundTable)
		{
			if (isset($foundTable['alias']['name']) && $foundTable['alias']['name'] == $string)
			{
				$counter++;

				return self::getUniqueAlias($originalTableName, $foundTables, $counter);
			}
		}

		return $string;
	}

	/**
	 * Breaks column name and replaces alias with the new one
	 *
	 * @param   string $column      Column Name with or without prefix
	 * @param   string $replaceWith Alias name to replace current one
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
	 * @param   string $newTable    Table alias of new table
	 * @param   string $operator    Operator of joining tables
	 * @param   string $oldTable    Alias of original table
	 * @param   object $tableObject Alias of original table
	 * @param   string $language    Language tag you want to fetch translation from
	 *
	 * @return  string  Parsed query with added table joins and fields if found
	 */
	public static function createParserJoinOperand($newTable, $operator, $oldTable, $tableObject, $language)
	{
		$db        = JFactory::getDbo();
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
				$leftSide  = $join['left'];
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
	 * @param   string $left               Table alias of new table
	 * @param   string $operator           Operator of joining tables
	 * @param   string $right              Alias of original table
	 * @param   object $aliasLeft          Alias of original table
	 * @param   string $aliasRight         Language tag you want to fetch translation from
	 * @param   string $expressionOperator Language tag you want to fetch translation from
	 *
	 * @return  array  table join param
	 */
	public static function createTableJoinParam($left, $operator = '=', $right = '', $aliasLeft = null, $aliasRight = null, $expressionOperator = 'AND')
	{
		return array(
			'left'               => $left,
			'operator'           => $operator,
			'right'              => $right,
			'aliasLeft'          => $aliasLeft,
			'aliasRight'         => $aliasRight,
			'expressionOperator' => $expressionOperator,
		);
	}

	/**
	 * Create Table Join Parameter
	 *
	 * @param   array  $originalTableColumns Table alias of new table
	 * @param   array  $tableColumns         Operator of joining tables
	 * @param   string $alias                Original table alias
	 *
	 * @return  array  table join param
	 */
	public static function addBaseColumns($originalTableColumns, $tableColumns, $alias)
	{
		$columns = array();

		foreach ($originalTableColumns as $key => $value)
		{
			if (!in_array($key, $tableColumns))
			{
				$columns[] = $alias . '.' . $key;
			}
		}

		return implode(',', $columns);
	}

	/**
	 * Creates array in sql Parser format, this function adds language filter as well
	 *
	 * @param   string $exprType Expression type
	 * @param   string $baseExpr Base expression
	 * @param   bool   $subTree  Sub Tree
	 *
	 * @return  array  Parser Element in array format
	 */
	public static function createParserElement($exprType, $baseExpr, $subTree = false)
	{
		$element = array(
			'expr_type' => $exprType,
			'base_expr' => $baseExpr,
			'sub_tree'  => $subTree
		);

		return $element;
	}

	/**
	 * Check for different types of field usage in field list and returns name with alias if present
	 *
	 * @param   string $field      Field name this can be with or without quotes
	 * @param   string $tableAlias Table alias | optional
	 * @param   array  $fieldList  List of fields to check against
	 * @param   bool   $isTable    If we are checking against table string
	 * @param   string $groupName  Group name
	 *
	 * @return  mixed  Returns List item if Field name is included in field list
	 */
	public static function getNameIfIncluded($field, $tableAlias = '', $fieldList = array(), $isTable = false, $groupName = 'SELECT')
	{
		// No fields to search for
		if (empty($fieldList) || empty($field) || self::skipTranslationColumn($groupName, $field))
		{
			return '';
		}

		$field      = self::cleanEscaping($field);
		$fieldParts = explode('.', $field);
		$alias      = '';

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
						if (!empty($fieldFromList->tableAliasesToParse)
							&& !empty($tableAlias)
							&& !in_array(self::cleanEscaping($tableAlias), $fieldFromList->tableAliasesToParse))
						{
							continue 2;
						}

						return $fieldFromListQuotes;
				}
			}
			elseif ($tableAlias == '')
			{
				switch (self::cleanEscaping($fieldFromList['columnName']))
				{
					case $field:
					case self::cleanEscaping($fieldFromList['table']['alias']['originalName'] . '.' . $field):
						// If this is different table we do not check columns
						if (!empty($alias) && $alias != self::cleanEscaping($fieldFromList['table']['alias']['originalName']))
						{
							continue 2;
						}

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
	 * @param   string $sql Sql to check against
	 *
	 * @return  string  Returns true if Field name is included in field list
	 */
	public static function cleanEscaping($sql)
	{
		return str_replace('`', '', trim($sql));
	}

	/**
	 * Check for enclosing brackets and remove it
	 *
	 * @param   string $sql Sql to check against
	 *
	 * @return  string  Returns sql query without enclosing brackets
	 */
	public static function removeParenthesisFromStart($sql)
	{
		$parenthesisRemoved = 0;

		$trim = trim($sql);

		if ($trim !== "" && $trim[0] === "(")
		{
			// Remove only one parenthesis pair now!
			$parenthesisRemoved++;
			$trim[0] = " ";
			$trim    = trim($trim);
		}

		$parenthesis = $parenthesisRemoved;
		$i           = 0;
		$string      = 0;

		while ($i < strlen($trim))
		{
			if ($trim[$i] === "\\")
			{
				// An escape character, the next character is irrelevant
				$i += 2;
				continue;
			}

			if ($trim[$i] === "'" || $trim[$i] === '"')
			{
				$string++;
			}

			if (($string % 2 === 0) && ($trim[$i] === "("))
			{
				$parenthesis++;
			}

			if (($string % 2 === 0) && ($trim[$i] === ")"))
			{
				if ($parenthesis == $parenthesisRemoved)
				{
					$trim[$i] = " ";
					$parenthesisRemoved--;
				}

				$parenthesis--;
			}

			$i++;
		}

		return trim($trim);
	}

	/**
	 * Check for database escape and remove it
	 *
	 * @param   string $groupName Group name
	 * @param   string $field     Field name this can be with or without quotes
	 *
	 * @return  bool  Returns true if Field name is included in field list
	 */
	public static function skipTranslationColumn($groupName, $field)
	{
		$db = JFactory::getDbo();

		if (!empty($db->skipColumns))
		{
			foreach ($db->skipColumns as $skipGroupName => $skipColumns)
			{
				if (!empty($skipColumns))
				{
					foreach ($skipColumns as $column)
					{
						if ($groupName == $skipGroupName && self::cleanEscaping($field) == $column)
						{
							return true;
						}
					}
				}
			}
		}

		return false;
	}

	/**
	 * Set a translation option value.
	 *
	 * @param   string $key The key
	 * @param   mixed  $val The default value
	 *
	 * @return  null
	 */
	public static function setOption($key, $val)
	{
		self::$options[$key] = $val;
	}

	/**
	 * Get a translation option value.
	 *
	 * @param   string $key     The key
	 * @param   mixed  $default The default value
	 *
	 * @return  mixed  The value or the default value
	 */
	public static function getOption($key, $default = null)
	{
		if (isset(self::$options[$key]))
		{
			return self::$options[$key];
		}

		return $default;
	}

	/**
	 * Set a translation option fallback value.
	 *
	 * @param   bool $enable Enable or disable translation fallback feature
	 *
	 * @return  null
	 */
	public static function setTranslationFallback($enable = true)
	{
		self::setOption('translationFallback', $enable);
	}

	/**
	 * Set a translation option force translate default value.
	 *
	 * @param   bool $enable Enable or disable force translate default language feature
	 *
	 * @return  null
	 */
	public static function setForceTranslateDefaultLanguage($enable = false)
	{
		self::setOption('forceTranslateDefault', $enable);
	}

	/**
	 * Set a translate data in Admin value.
	 *
	 * @param   bool $enable Enable or disable translation fallback feature
	 *
	 * @return  null
	 */
	public static function setTranslationInAdmin($enable = false)
	{
		self::setOption('translateInAdmin', $enable);
	}
}
