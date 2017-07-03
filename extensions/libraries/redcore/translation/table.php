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
 * A Translation Table helper.
 *
 * @package     Redcore
 * @subpackage  Translation
 * @since       1.0
 */
final class RTranslationTable
{
	/**
	 * @const  string
	 */
	const COLUMN_TRANSLATE = 'translate';

	/**
	 * @const  string
	 */
	const COLUMN_PRIMARY = 'primary';

	/**
	 * @const  string
	 */
	const COLUMN_READONLY = 'readonly';

	/**
	 * An array to hold tables from database
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $installedTranslationTables = null;

	/**
	 * An array to hold columns from database
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $translationColumns = null;

	/**
	 * An array to hold tables from database
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $tableList = array();

	/**
	 * An array to hold tables columns from database
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $columnsList = array();

	/**
	 * An array to hold database engines from database
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $dbEngines = array();

	/**
	 * Prefix used to identify the tables
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $tablePrefix = '';

	/**
	 * Get Translations Table Columns Array
	 *
	 * @param   string  $originalTableName  Original table name
	 *
	 * @return  array  An array of table columns
	 */
	public static function getTranslationsTableColumns($originalTableName)
	{
		if (empty(self::$tablePrefix))
		{
			self::loadTables();
		}

		$tableName = self::getTranslationsTableName($originalTableName, self::$tablePrefix);

		if (in_array($tableName, self::$tableList))
		{
			if (empty(self::$columnsList[$tableName]))
			{
				$db = JFactory::getDbo();

				self::$columnsList[$tableName] = $db->getTableColumns($tableName, false);
			}

			return self::$columnsList[$tableName];
		}

		return array();
	}

	/**
	 * Get Translations Table Columns Array
	 *
	 * @param   string  $tableName  Original table name
	 * @param   bool    $addPrefix  Add prefix to the table name
	 *
	 * @return  array  An array of table columns
	 */
	public static function getTableColumns($tableName, $addPrefix = true)
	{
		if (empty(self::$tablePrefix))
		{
			self::loadTables();
		}

		if ($addPrefix)
		{
			$tableName = self::$tablePrefix . str_replace('#__', '', $tableName);
		}

		if (in_array($tableName, self::$tableList))
		{
			if (empty(self::$columnsList[$tableName]))
			{
				$db = JFactory::getDbo();
				$translate = property_exists($db, 'translate') ? $db->translate : false;
				$db->translate = false;
				self::$columnsList[$tableName] = $db->getTableColumns($tableName, false);
				$db->translate = $translate;
			}

			return self::$columnsList[$tableName];
		}

		return array();
	}

	/**
	 * Load all tables from current database into array
	 *
	 * @return  array  An array of table names
	 */
	public static function loadTables()
	{
		if (empty(self::$tablePrefix))
		{
			$db = JFactory::getDbo();
			$translate = property_exists($db, 'translate') ? $db->translate : false;
			$db->translate = false;
			self::$tableList = $db->getTableList();
			self::$tablePrefix = $db->getPrefix();
			$db->translate = $translate;
		}

		return self::$tableList;
	}

	/**
	 * Reset loaded tables
	 *
	 * @return  void
	 */
	public static function resetLoadedTables()
	{
		self::$tablePrefix = '';
		self::loadTables();
	}

	/**
	 * Get table name with suffix
	 *
	 * @param   string  $originalTableName  Original table name
	 * @param   string  $prefix             Table name prefix
	 *
	 * @return  string  Table name used for getting translations
	 */
	public static function getTranslationsTableName($originalTableName, $prefix = '#__')
	{
		if (empty(self::$tablePrefix))
		{
			self::loadTables();
		}

		return $prefix . str_replace($prefix, '', $originalTableName) . '_rctranslations';
	}

	/**
	 * Install or update Translation table from current table object
	 *
	 * @param   RedcoreTableTranslation_Table  $table          Data needed for translation table creation
	 * @param   bool                           $notifications  Should we display notifications?
	 *
	 * @return  boolean  Returns true if translation table was successfully installed
	 */
	public static function setTranslationTable($table, $notifications = false)
	{
		$db = JFactory::getDbo();
		$newTable = self::getTranslationsTableName($table->name);
		$originalTable = str_replace('#__', '', $table->name);
		$originalTableWithPrefix = '#__' . $originalTable;
		$columns = self::getTranslationsTableColumns($originalTable);
		$newTableCreated = false;
		$innoDBSupport = self::checkIfDatabaseEngineExists();

		// We might be in installer and got new tables so we will get fresh list of the tables
		self::resetLoadedTables();
		$originalColumns = self::getTableColumns($originalTable);

		// If original table is not present then we cannot create shadow table
		if (empty($originalColumns))
		{
			if ($notifications)
			{
				JLog::add(
					JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR_TABLE', str_replace('#__', '', $table->name)), JLog::ERROR, 'jerror'
				);
			}

			return false;
		}

		if (empty($columns))
		{
			$newTableCreated = true;
			$query = 'CREATE TABLE IF NOT EXISTS ' . $db->qn($newTable)
				. ' ('
				. $db->qn('rctranslations_id') . ' int(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT, '
				. $db->qn('rctranslations_language') . ' char(7) NOT NULL DEFAULT ' . $db->q('') . ', '
				. $db->qn('rctranslations_originals') . ' TEXT NOT NULL, '
				. $db->qn('rctranslations_modified_by') . ' INT(11) NULL DEFAULT NULL, '
				. $db->qn('rctranslations_modified') . ' datetime NOT NULL DEFAULT ' . $db->q('0000-00-00 00:00:00') . ', '
				. $db->qn('rctranslations_state') . ' tinyint(3) NOT NULL DEFAULT ' . $db->q('1') . ', '
				. ' KEY ' . $db->qn('language_idx') . ' (' . $db->qn('rctranslations_language') . ',' . $db->qn('rctranslations_state') . ') '
				. ' ) DEFAULT CHARSET=utf8';

			if ($innoDBSupport)
			{
				$query .= ' ENGINE=InnoDB';
			}

			$db->setQuery($query);

			try
			{
				$db->execute();

				// Since we have new table we will reset it
				self::resetLoadedTables();
				$columns = self::getTableColumns($newTable);

				if (empty($columns))
				{
					throw new RuntimeException($newTable);
				}
			}
			catch (RuntimeException $e)
			{
				if ($notifications)
				{
					JLog::add(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), JLog::ERROR, 'jerror');
				}

				return false;
			}
		}

		// Language is automatically added to the table if table exists
		$columns = self::removeFixedColumnsFromArray($columns);
		$columnKeys = array_keys($columns);
		$fields = $table->get('columns', array());

		foreach ($fields as $fieldKey => $field)
		{
			$fields[$fieldKey]['exists'] = false;

			foreach ($columnKeys as $columnKey => $columnKeyValue)
			{
				if ($field['name'] == $columnKeyValue && $field['column_type'] != self::COLUMN_READONLY)
				{
					unset($columnKeys[$columnKey]);
					$fields[$fieldKey]['exists'] = true;
				}
			}
		}

		// We Add New or modify existing columns
		if (!empty($fields))
		{
			$newColumns = array();

			foreach ($fields as $fieldKey => $field)
			{
				if ($field['column_type'] != self::COLUMN_READONLY)
				{
					if (!empty($originalColumns[$field['name']]))
					{
						$modify = $field['exists'] ? 'MODIFY' : 'ADD';

						$newColumns[] = $modify . ' COLUMN ' . $db->qn($field['name'])
							. ' ' . $originalColumns[$field['name']]->Type
							. ' NULL'
							. ' DEFAULT NULL ';
					}
					else
					{
						if ($notifications)
						{
							JLog::add(
								JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR_COLUMNS', $originalTable, $field['name']), JLog::ERROR, 'jerror'
							);
						}

						return false;
					}
				}
			}

			if (!empty($newColumns))
			{
				try
				{
					$query = 'ALTER TABLE ' . $db->qn($newTable) . ' ' . implode(',', $newColumns);
					$db->setQuery($query);
					$db->execute();
				}
				catch (RuntimeException $e)
				{
					if ($notifications)
					{
						JLog::add(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), JLog::ERROR, 'jerror');
					}

					return false;
				}
			}
		}

		// Remove old constraints if they exist before removing the columns
		if (!$newTableCreated)
		{
			$loadedTable = self::getTranslationTableByName($originalTableWithPrefix);
			$primaryColumns = !empty($loadedTable) ? explode(',', $loadedTable->primary_columns) : array();

			self::removeExistingConstraintKeys($originalTableWithPrefix, $primaryColumns);
		}

		// We delete extra columns
		if (!empty($columnKeys) && !$newTableCreated)
		{
			$oldColumns = array();

			foreach ($columnKeys as $columnKey)
			{
				$oldColumns[] = 'DROP COLUMN ' . $db->qn($columnKey);
			}

			if (!empty($oldColumns))
			{
				try
				{
					$query = 'ALTER TABLE ' . $db->qn($newTable) . ' ' . implode(',', $oldColumns);
					$db->setQuery($query);
					$db->execute();
				}
				catch (RuntimeException $e)
				{
					if ($notifications)
					{
						JLog::add(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), JLog::ERROR, 'jerror');
					}

					return false;
				}
			}
		}

		self::updateTableIndexKeys($fields, $newTable, $notifications);

		$constraintType = 'none';

		if (method_exists('RBootstrap', 'getConfig'))
		{
			$constraintType = RBootstrap::getConfig('translations_constraint_type', 'none');
		}

		// New install use default value foreign key if InnoDB is present
		if (($constraintType == 'foreign_keys'))
		{
			if ($innoDBSupport)
			{
				self::updateTableForeignKeys($fields, $newTable, $originalTableWithPrefix);
			}
			else
			{
				if ($notifications)
				{
					JLog::add(JText::_('COM_REDCORE_TRANSLATION_TABLE_CONTENT_ELEMENT_INNODB_MISSING'), JLog::WARNING, 'jerror');
				}
			}
		}
		elseif ($constraintType == 'triggers')
		{
			self::updateTableTriggers($fields, $newTable, $originalTableWithPrefix, $notifications);
		}

		return true;
	}

	/**
	 * Install Content Element from XML file
	 *
	 * @param   string  $option         The Extension Name ex. com_redcore
	 * @param   string  $xmlFile        XML file to install
	 * @param   bool    $fullPath       Full path to the XML file
	 * @param   bool    $notifications  Full path to the XML file
	 *
	 * @return  boolean  Returns true if Content element was successfully installed
	 */
	public static function installContentElement($option = 'com_redcore', $xmlFile = '', $fullPath = false, $notifications = false)
	{
		// Load Content Element
		$contentElement = RTranslationContentElement::getContentElement($option, $xmlFile, $fullPath);

		if (empty($contentElement) || empty($contentElement->table))
		{
			if ($notifications)
			{
				JLog::add(
					JText::sprintf(
						'LIB_REDCORE_TRANSLATION_TABLE_CONTENT_ELEMENT_NOT_INSTALLED', empty($contentElement->table) ? empty($contentElement->table) : ''
					),
					JLog::WARNING, 'jerror'
				);
			}

			return false;
		}

		// Create table with fields
		$db = JFactory::getDbo();
		$originalTable = '#__' . $contentElement->table;

		// Check if that table is already installed
		$fields = array();
		$contentElement->fieldsToColumns = array();
		$primaryKeys = array();
		$fieldsXml = $contentElement->getTranslateFields();

		foreach ($fieldsXml as $field)
		{
			$fieldAttributes = (array) $field->attributes();
			$fieldAttributes = !empty($fieldAttributes) ? $fieldAttributes['@attributes'] : array();

			$fieldsToColumn = array();
			$fieldsToColumn['name'] = (string) $fieldAttributes['name'];
			$fieldsToColumn['title'] = (string) $field;
			$fieldsToColumn['description'] = !empty($fieldAttributes['description']) ? (string) $fieldAttributes['description'] : '';
			$fieldsToColumn['fallback'] = isset($fieldAttributes['alwaysFallback']) && (string) $fieldAttributes['alwaysFallback'] == 'true' ? 1 : 0;
			$fieldsToColumn['value_type'] = (string) $fieldAttributes['type'];
			$fieldsToColumn['params'] = $fieldAttributes;

			foreach ($fieldsToColumn['params'] as $paramKey => $paramValue)
			{
				if (in_array($paramKey, array('name', 'type', 'alwaysFallback', 'translate')))
				{
					unset($fieldsToColumn['params'][$paramKey]);
				}
			}

			$translate = isset($fieldAttributes['translate']) && ((string) $fieldAttributes['translate']) == '0' ? false : true;
			$primaryKey = isset($fieldAttributes['type']) && ((string) $fieldAttributes['type']) == 'referenceid' ? true : false;

			if ($primaryKey)
			{
				$fieldsToColumn['column_type'] = self::COLUMN_PRIMARY;
				$fieldsToColumn['value_type'] = 'referenceid';
			}
			elseif (!$translate)
			{
				$fieldsToColumn['column_type'] = self::COLUMN_READONLY;
			}
			else
			{
				$fieldsToColumn['column_type'] = self::COLUMN_TRANSLATE;
			}

			$contentElement->fieldsToColumns[] = $fieldsToColumn;

			// We are not saving this fields, we only show them in editor
			if (isset($fieldAttributes['translate']) && (string) $fieldAttributes['translate'] == '0' && (string) $fieldAttributes['type'] != 'referenceid')
			{
				continue;
			}

			$fieldName = (string) $fieldAttributes['name'];
			$fields[$fieldName] = $db->qn($fieldName);

			if ($primaryKey)
			{
				$primaryKeys[$fieldName] = $db->qn($fieldName);
			}
		}

		if (empty($fields))
		{
			if ($notifications)
			{
				JLog::add(
					JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR_NO_FIELDS', $xmlFile), JLog::ERROR, 'jerror'
				);
			}

			return false;
		}

		$loadedTable = self::getTranslationTableByName($originalTable);
		$contentElement->tableId = !empty($loadedTable) ? $loadedTable->id : 0;

		self::setInstalledTranslationTables(
			$option,
			$originalTable,
			$contentElement,
			$notifications
		);

		return true;
	}

	/**
	 * Adds table Index Keys based on primary keys for improved performance
	 *
	 * @param   array   $fields         Array of fields from translation table
	 * @param   string  $newTable       Table name
	 * @param   bool    $notifications  Table name
	 *
	 * @return  boolean  Returns true if Content element was successfully installed
	 */
	public static function updateTableIndexKeys($fields, $newTable, $notifications = false)
	{
		$indexKeys = array();
		$db = JFactory::getDbo();

		$tableKeys      = $db->getTableKeys($newTable);
		$languageKey    = $db->qn('rctranslations_language');
		$stateKey       = $db->qn('rctranslations_state');

		foreach ($fields as $field)
		{
			if ((string) $field['value_type'] == 'referenceid')
			{
				$fieldName      = (string) $field['name'];
				$constraintKey  = md5($newTable . '_' . $fieldName . '_idx');
				$keyFound       = false;

				foreach ($tableKeys as $tableKey)
				{
					if ($tableKey->Key_name == $constraintKey)
					{
						$keyFound = true;
						break;
					}
				}

				if (!$keyFound)
				{
					$indexKeys[$constraintKey] = 'ADD KEY '
						. $db->qn($constraintKey)
						. ' (' . $languageKey . ',' . $stateKey . ',' . $db->qn($fieldName) . ')';
				}
			}
		}

		if (!empty($indexKeys))
		{
			$indexKeysQuery = 'ALTER TABLE '
				. $db->qn($newTable)
				. ' '
				. implode(', ', $indexKeys);

			try
			{
				$db->setQuery($indexKeysQuery);
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				if ($notifications)
				{
					JLog::add(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), JLog::ERROR, 'jerror');
				}

				return false;
			}
		}

		return true;
	}

	/**
	 * Adds table triggers based on primary keys for constraint between tables
	 *
	 * @param   array   $fields         Array of fields from translation table
	 * @param   string  $newTable       Translation Table name
	 * @param   string  $originalTable  Original Table name
	 * @param   bool    $notifications  Show notifications
	 *
	 * @return  boolean  Returns true if triggers are successfully installed
	 */
	public static function updateTableTriggers($fields, $newTable, $originalTable, $notifications = false)
	{
		$db = JFactory::getDbo();
		$triggerKey = md5($originalTable . '_rctranslationstrigger');
		$primaryKeys = array();

		if (!empty($fields))
		{
			foreach ($fields as $field)
			{
				if ((string) $field['type'] == 'referenceid')
				{
					$fieldName = (string) $field['name'];
					$primaryKeys[] = $db->qn($fieldName) . ' = OLD.' . $db->qn($fieldName);
				}
			}
		}

		if (!empty($primaryKeys))
		{
			$query = 'CREATE TRIGGER ' . $db->qn($triggerKey) . '
				AFTER DELETE
				ON ' . $db->qn($originalTable) . '
				FOR EACH ROW BEGIN
					DELETE FROM ' . $db->qn($newTable) . ' WHERE ' . implode(' AND ', $primaryKeys) . ';
				END;';

			try
			{
				$db->setQuery($query);
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				if ($notifications)
				{
					JLog::add(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), JLog::ERROR, 'jerror');
				}

				return false;
			}
		}

		return true;
	}

	/**
	 * Removes table foreign keys based on primary keys for constraint between tables
	 *
	 * @param   string  $originalTable  Original Table name
	 * @param   array   $primaryKeys    Primary keys of the table
	 *
	 * @return  boolean  Returns true if Foreign keys are successfully installed
	 */
	public static function removeExistingConstraintKeys($originalTable, $primaryKeys = array())
	{
		$innoDBSupport = self::checkIfDatabaseEngineExists();

		// Remove Triggers
		$db = JFactory::getDbo();
		$triggerKey = md5($originalTable . '_rctranslationstrigger');

		try
		{
			$db->setQuery('DROP TRIGGER IF EXISTS ' . $db->qn($triggerKey));
			$db->execute();
		}
		catch (RuntimeException $e)
		{

		}

		if ($innoDBSupport && !empty($primaryKeys))
		{
			$newTable = self::getTranslationsTableName($originalTable, '');

			foreach ($primaryKeys as $primaryKey => $primaryKeyQuoted)
			{
				$constraintKey = $db->qn(md5($newTable . '_' . $primaryKeyQuoted . '_fk'));

				$query = 'ALTER TABLE ' . $db->qn($newTable) . ' DROP FOREIGN KEY ' . $constraintKey . ' # ' . $newTable . '_' . $primaryKey . '_fk';

				try
				{
					$db->setQuery($query);
					@$db->execute();
				}
				catch (RuntimeException $e)
				{

				}
			}
		}
	}

	/**
	 * Adds table foreign keys Keys based on primary keys for constraint between tables
	 *
	 * @param   array   $fields         Array of fields from translation table
	 * @param   string  $newTable       Translation Table name
	 * @param   string  $originalTable  Original Table name
	 * @param   bool    $notifications  Show notifications
	 *
	 * @return  boolean  Returns true if Foreign keys are successfully installed
	 */
	public static function updateTableForeignKeys($fields, $newTable, $originalTable, $notifications = false)
	{
		$db = JFactory::getDbo();

		if (!empty($fields))
		{
			foreach ($fields as $field)
			{
				if ((string) $field['value_type'] == 'referenceid')
				{
					$fieldName = (string) $field['name'];

					if (!empty($fieldName))
					{
						$primaryKey = $db->qn($fieldName);
						$constraintKey = $db->qn(md5($newTable . '_' . $fieldName . '_fk'));
						$query = 'ALTER TABLE ' . $db->qn($newTable) . ' ADD CONSTRAINT '
							. $constraintKey
							. ' FOREIGN KEY (' . $primaryKey . ')'
							. ' REFERENCES ' . $db->qn($originalTable) . ' (' . $primaryKey . ')'
							. '	ON DELETE CASCADE'
							. ' ON UPDATE NO ACTION ';

						try
						{
							$db->setQuery($query);
							$db->execute();
						}
						catch (RuntimeException $e)
						{
							if ($notifications)
							{
								JLog::add(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), JLog::WARNING, 'jerror');
							}

							return false;
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * Purge Tables
	 *
	 * @param   array  $cid  Table IDs
	 *
	 * @return  boolean  Returns true if Table was successfully purged
	 */
	public static function purgeTables($cid)
	{
		$db = JFactory::getDbo();

		if (!is_array($cid))
		{
			$cid = array($cid);
		}

		foreach ($cid as $id)
		{
			$table = self::getTranslationTableById($id);

			if ($table)
			{
				$newTableName = self::getTranslationsTableName($table->name);

				try
				{
					$db->truncateTable($newTableName);
				}
				catch (RuntimeException $e)
				{
					JLog::add(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), JLog::WARNING, 'jerror');

					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Delete Content Element Table and XML file
	 *
	 * @param   string  $option   The Extension Name ex. com_redcore
	 * @param   string  $xmlFile  XML file to install
	 *
	 * @return  boolean  Returns true if Content element was successfully purged
	 */
	public static function deleteContentElement($option = 'com_redcore', $xmlFile = '')
	{
		$xmlFilePath = RTranslationContentElement::getContentElementXmlPath($option, $xmlFile);

		try
		{
			JFile::delete($xmlFilePath);
		}
		catch (Exception $e)
		{
			JLog::add(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), JLog::WARNING, 'jerror');

			return false;
		}

		return true;
	}

	/**
	 * Preforms Batch action against all Content elements of given Extension
	 *
	 * @param   string  $option         The Extension Name ex. com_redcore
	 * @param   string  $action         Action to preform
	 * @param   bool    $notifications  Show notifications
	 *
	 * @return  boolean  Returns true if Action was successful
	 */
	public static function batchContentElements($option = 'com_redcore', $action = '', $notifications = false)
	{
		RTranslationContentElement::$contentElements = array();
		$contentElements = RTranslationContentElement::getContentElements(true, $option);
		$tables = self::getInstalledTranslationTables(true);

		if (!empty($contentElements))
		{
			foreach ($contentElements as $componentName => $contentElement)
			{
				if (!empty($option) && $option != $contentElement->extension)
				{
					continue;
				}

				// We will check if this table is installed from a different path than the one provided
				foreach ($tables as $table)
				{
					if (str_replace('#__', '', $table->name) == $contentElement->table
						&& $table->xml_path != RTranslationContentElement::getPathWithoutBase($contentElement->contentElementXmlPath))
					{
						// We skip this file because it is not the one that is already installed
						continue 2;
					}
				}

				switch ($action)
				{
					case 'install':
						if (!empty($contentElement->table))
						{
							self::installContentElement($option, $contentElement->contentElementXmlPath, true, $notifications);
						}

						break;
					case 'delete':
						self::deleteContentElement($option, $contentElement->contentElementXml);
						break;
				}
			}
		}

		return true;
	}

	/**
	 * Upload Content Element to redcore media location
	 *
	 * @param   array  $files  The array of Files (file descriptor returned by PHP)
	 *
	 * @return  boolean  Returns true if Upload was successful
	 */
	public static function uploadContentElement($files = array())
	{
		$uploadOptions = array(
			'allowedFileExtensions' => 'xml',
			'allowedMIMETypes'      => 'application/xml, text/xml',
			'overrideExistingFile'  => true,
		);

		return RFilesystemFile::uploadFiles($files, RTranslationContentElement::getContentElementFolderPath('upload'), $uploadOptions);
	}

	/**
	 * Remove fixed columns from array
	 *
	 * @param   array  $columns  All the columns from the table
	 *
	 * @return  array  Filtered array of columns
	 */
	public static function removeFixedColumnsFromArray($columns = array())
	{
		unset($columns['rctranslations_id']);
		unset($columns['rctranslations_language']);
		unset($columns['rctranslations_originals']);
		unset($columns['rctranslations_modified']);
		unset($columns['rctranslations_modified_by']);
		unset($columns['rctranslations_state']);

		return $columns;
	}

	/**
	 * Creates json encoded original value with hashed column values needed for editor
	 *
	 * @param   array|object  $original  Original data array
	 * @param   array         $columns   All the columns from the table
	 *
	 * @return  string  Json encoded string of array values
	 */
	public static function createOriginalValueFromColumns($original = array(), $columns = array())
	{
		$data = array();
		$original = (array) $original;

		foreach ($columns as $column)
		{
			$data[$column] = md5(isset($original[$column]) ? $original[$column] : '');
		}

		return json_encode($data);
	}

	/**
	 * Checks if InnoDB database engine is installed and enabled on the MySQL server
	 *
	 * @param   string  $engine  Database Engine name
	 *
	 * @return  boolean  Returns true if InnoDB engine is active
	 */
	public static function checkIfDatabaseEngineExists($engine = 'InnoDB')
	{
		if (!isset(self::$dbEngines[$engine]))
		{
			self::$dbEngines[$engine] = false;
			$db = JFactory::getDbo();

			$db->setQuery('SHOW ENGINES');
			$results = $db->loadObjectList();

			if (!empty($results))
			{
				foreach ($results as $result)
				{
					if (strtoupper($result->Engine) == strtoupper($engine))
					{
						if (strtoupper($result->Support) != 'NO')
						{
							self::$dbEngines[$engine] = true;
						}
					}
				}
			}
		}

		return self::$dbEngines[$engine];
	}

	/**
	 * Get list of all translation tables with columns
	 *
	 * @param   bool  $fullLoad   Full load tables
	 * @param   bool  $isEnabled  If true is just return "Enabled" translation table.
	 *
	 * @return  array             Array or table with columns columns
	 */
	public static function getInstalledTranslationTables($fullLoad = false, $isEnabled = false)
	{
		static $fullLoaded;

		if ($fullLoad && (is_null($fullLoaded) || !$fullLoaded))
		{
			$db = JFactory::getDbo();
			$oldTranslate = isset($db->translate) ? $db->translate : false;

			// We do not want to translate this value
			$db->translate = false;

			$query = $db->getQuery(true)
				->select('tt.*')
				->from($db->qn('#__redcore_translation_tables', 'tt'))
				->order('tt.title');

			$tables = $db->setQuery($query)->loadObjectList('name');

			foreach ($tables as $key => $table)
			{
				$tables[$key]->columns = explode(',', $table->primary_columns . ',' . $table->translate_columns);
				$tables[$key]->primaryKeys = explode(',', $table->primary_columns);
				$tables[$key]->fallbackColumns = explode(',', $table->fallback_columns);
				$tables[$key]->table = $table->name;
				$tables[$key]->option = $table->extension_name;
				$tables[$key]->formLinks = json_decode($table->form_links, true);

				if (isset(self::$translationColumns[$key]))
				{
					$tables[$key]->allColumns = self::$translationColumns[$key];
				}
			}

			// We put translation check back on
			$db->translate = $oldTranslate;
			self::$installedTranslationTables = $tables;

			$fullLoaded = true;
		}

		if (!$fullLoad && !isset(self::$installedTranslationTables))
		{
			$db = JFactory::getDbo();
			$oldTranslate = isset($db->translate) ? $db->translate : false;

			// We do not want to translate this value
			$db->translate = false;

			$query = $db->getQuery(true)
				->select(
					array(
						$db->qn('tt.translate_columns', 'columns'),
						$db->qn('tt.primary_columns', 'primaryKeys'),
						$db->qn('tt.fallback_columns', 'fallbackColumns'),
						$db->qn('tt.name', 'table'),
						$db->qn('tt.title', 'title'),
						$db->qn('tt.extension_name', 'option'),
						$db->qn('tt.form_links', 'formLinks'),
					)
				)
				->from($db->qn('#__redcore_translation_tables', 'tt'))
				->where('tt.state = 1')
				->order('tt.title');

			$tables = $db->setQuery($query)->loadObjectList('table');

			foreach ($tables as $key => $table)
			{
				$tables[$key]->columns = explode(',', $table->primaryKeys . ',' . $table->columns);
				$tables[$key]->primaryKeys = explode(',', $table->primaryKeys);
				$tables[$key]->fallbackColumns = explode(',', $table->fallbackColumns);
				$tables[$key]->formLinks = json_decode($table->formLinks, true);

				if (isset(self::$translationColumns[$key]))
				{
					$tables[$key]->allColumns = self::$translationColumns[$key];
				}
			}

			// We put translation check back on
			$db->translate = $oldTranslate;
			self::$installedTranslationTables = $tables;
		}

		if ($isEnabled && $fullLoaded)
		{
			$tables = self::$installedTranslationTables;

			foreach ($tables as $key => $table)
			{
				if (!$table->state)
				{
					unset($tables[$key]);
				}
			}

			return $tables;
		}

		return self::$installedTranslationTables;
	}

	/**
	 * Populate translation column data for the table
	 *
	 * @param   string  $tableName  Table name
	 *
	 * @return  array               Array or table with columns columns
	 */
	public static function setTranslationTableWithColumn($tableName)
	{
		$table = self::getTranslationTableByName($tableName);

		if ($table)
		{
			if (!isset(self::$translationColumns[$table->name]))
			{
				$db = JFactory::getDbo();

				$query = $db->getQuery(true)
					->select('tc.*')
					->from($db->qn('#__redcore_translation_columns', 'tc'))
					->where($db->qn('translation_table_id') . ' = ' . $table->id)
					->order($db->qn('id'));

				self::$translationColumns[$table->name] = $db->setQuery($query)->loadAssocList('name');

				foreach (self::$translationColumns[$table->name] as $key => $column)
				{
					if (!empty($column['params']))
					{
						self::$translationColumns[$table->name][$key] = array_merge($column, json_decode($column['params'], true));
					}
				}
			}

			self::$installedTranslationTables[$table->name]->allColumns = self::$translationColumns[$table->name];

			return self::$installedTranslationTables[$table->name];
		}

		return $table;
	}

	/**
	 * Get translation table with columns
	 *
	 * @param   int  $id  Translation table ID
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function getTranslationTableById($id)
	{
		$tables = self::getInstalledTranslationTables(true);

		foreach ($tables as $table)
		{
			if ($table->id == $id)
			{
				return $table;
			}
		}

		return null;
	}

	/**
	 * Get translation table with columns
	 *
	 * @param   string  $name  Translation table Name
	 *
	 * @return  array          Array or table with columns columns
	 */
	public static function getTranslationTableByName($name)
	{
		$tables = self::getInstalledTranslationTables(true);
		$name = '#__' . str_replace('#__', '', $name);

		return isset($tables[$name]) ? $tables[$name] : null;
	}

	/**
	 * Set a value to translation table list
	 *
	 * @param   string                      $option          Extension option name
	 * @param   string                      $table           Table name
	 * @param   RTranslationContentElement  $contentElement  Content Element
	 * @param   bool                        $notifications   Show notifications
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function setInstalledTranslationTables($option, $table, $contentElement, $notifications = false)
	{
		// Initialize installed tables before proceeding
		self::getInstalledTranslationTables(true);
		$translationTableModel = RModel::getAdminInstance('Translation_Table', array(), 'com_redcore');

		// If content element is empty then we delete this table from the system
		if (empty($contentElement))
		{
			$tableObject = self::getTranslationTableByName($table);

			if ($tableObject)
			{
				$translationTableModel->delete($tableObject->id);
			}

			unset(self::$installedTranslationTables[$table]);
		}
		else
		{
			$data = array(
				'name'              => $table,
				'extension_name'    => $option,
				'title'             => $contentElement->name,
				'version'           => $contentElement->version,
				'columns'           => $contentElement->fieldsToColumns,
				'formLinks'         => $contentElement->getEditForms(),
				'description'       => $contentElement->getTranslateDescription(),
				'author'            => $contentElement->getTranslateAuthor(),
				'copyright'         => $contentElement->getTranslateCopyright(),
				'xml_path'          => RTranslationContentElement::getPathWithoutBase($contentElement->contentElementXmlPath),
				'xml_hashed'        => $contentElement->xml_hashed,
				'filter_query'      => implode(' AND ', (array) $contentElement->getTranslateFilter()),
				'state'             => 1,
				'id'                => $contentElement->tableId,
				'showNotifications' => $notifications
			);
			$translationTableModel->save($data);
		}
	}
}
