<?php
/**
 * @package     Redcore
 * @subpackage  Translation
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
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

		return $prefix . $originalTableName . '_rctranslations';
	}

	/**
	 * Install Content Element from XML file
	 *
	 * @param   string  $option             The Extension Name ex. com_redcore
	 * @param   string  $xmlFile            XML file to install
	 * @param   bool    $showNotifications  Show notifications
	 *
	 * @return  boolean  Returns true if Content element was successfully installed
	 */
	public static function installContentElement($option = 'com_redcore', $xmlFile = '', $showNotifications = true)
	{
		// Load Content Element
		$contentElement = RTranslationHelper::getContentElement($option, $xmlFile);

		if (empty($contentElement) || empty($contentElement->table))
		{
			if ($showNotifications)
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_NOT_INSTALLED'), 'warning');
			}

			return false;
		}

		// Create table with fields
		$db = JFactory::getDbo();

		// We might be in installer and got new tables so we will get fresh list of the tables
		self::resetLoadedTables();
		$originalColumns = self::getTableColumns($contentElement->table);

		// If original table is not present then we cannot create shadow table
		if (empty($originalColumns))
		{
			if ($showNotifications)
			{
				JFactory::getApplication()->enqueueMessage(
					JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR_TABLE', $xmlFile, (string) $contentElement->table),
					'error'
				);
			}

			return false;
		}

		// Check if that table is already installed
		$columns = self::getTranslationsTableColumns($contentElement->table);
		$fields = array();
		$primaryKeys = array();
		$fieldsXml = $contentElement->getTranslateFields();
		$newTable = self::getTranslationsTableName($contentElement->table);
		$originalTable = '#__' . $contentElement->table;

		foreach ($fieldsXml as $field)
		{
			// If not in original table then do not create it
			if (empty($originalColumns[(string) $field['name']]))
			{
				if ($showNotifications)
				{
					JFactory::getApplication()->enqueueMessage(
						JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR_COLUMNS', $xmlFile, (string) $field['name']),
						'error'
					);
				}

				return false;
			}

			// We are not saving this fields, we only show them in editor
			if ((string) $field['translate'] == '0' && (string) $field['type'] != 'referenceid')
			{
				continue;
			}

			$fields[(string) $field['name']] = $db->qn((string) $field['name']);

			if ((string) $field['type'] == 'referenceid')
			{
				$fieldName = (string) $field['name'];

				$primaryKeys[$fieldName] = $db->qn($fieldName);
			}
		}

		if (empty($fields))
		{
			if ($showNotifications)
			{
				JFactory::getApplication()->enqueueMessage(
					JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR_NO_FIELDS', $xmlFile),
					'error'
				);
			}

			return false;
		}

		$newTableCreated = false;
		$innoDBSupport = self::checkIfDatabaseEngineExists();

		if (empty($columns))
		{
			$newTableCreated = true;
			$query = 'CREATE TABLE ' . $db->qn($newTable)
				. ' ('
				. $db->qn('rctranslations_id') . ' int(10) UNSIGNED PRIMARY KEY AUTO_INCREMENT, '
				. $db->qn('rctranslations_language') . ' char(7) NOT NULL DEFAULT ' . $db->q('') . ', '
				. $db->qn('rctranslations_originals') . ' TEXT NOT NULL, '
				. $db->qn('rctranslations_modified') . ' datetime NOT NULL DEFAULT ' . $db->q('0000-00-00 00:00:00') . ', '
				. $db->qn('rctranslations_state') . ' tinyint(3) NOT NULL DEFAULT ' . $db->q('1') . ', '
				. ' KEY ' . $db->qn('language_idx') . ' (' . $db->qn('rctranslations_language') . ',' . $db->qn('rctranslations_state') . ') '
				. ' )';

			if ($innoDBSupport)
			{
				$query .= 'ENGINE=InnoDB';
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
				if ($showNotifications)
				{
					JFactory::getApplication()->enqueueMessage(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');
				}

				return false;
			}
		}

		$allContentElementsFields = implode(',', array_keys($fields));

		// Language is automatically added to the table if table exists
		$columns = self::removeFixedColumnsFromArray($columns);
		$columnKeys = array_keys($columns);

		foreach ($fields as $fieldKey => $field)
		{
			foreach ($columnKeys as $columnKey => $columnKeyValue)
			{
				if ($fieldKey == $columnKeyValue)
				{
					unset($columnKeys[$columnKey]);
					unset($fields[$fieldKey]);
				}
			}
		}

		// We Add New columns
		if (!empty($fields))
		{
			$newColumns = array();

			foreach ($fields as $fieldKey => $field)
			{
				if (!empty($originalColumns[$fieldKey]))
				{
					$newColumns[] = 'ADD COLUMN ' . $field
						. ' ' . $originalColumns[$fieldKey]->Type
						. ' NULL'
						. ' DEFAULT NULL ';
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
					if ($showNotifications)
					{
						JFactory::getApplication()->enqueueMessage(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');
					}

					return false;
				}
			}
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
					if ($showNotifications)
					{
						JFactory::getApplication()->enqueueMessage(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');
					}

					return false;
				}
			}
		}

		self::updateTableIndexKeys($fieldsXml, $newTable);

		if (!$newTableCreated)
		{
			self::removeExistingConstraintKeys($originalTable, $primaryKeys);
		}

		$constraintType = 'foreign_keys';

		if (method_exists('RBootstrap', 'getConfig'))
		{
			$constraintType = RBootstrap::getConfig('translations_constraint_type', 'foreign_keys');
		}

		// New install use default value foreign key if InnoDB is present
		if (($constraintType == 'foreign_keys'))
		{
			if ($innoDBSupport)
			{
				self::updateTableForeignKeys($fieldsXml, $newTable, $originalTable);
			}
			else
			{
				if ($showNotifications)
				{
					JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_INNODB_MISSING'), 'message');
				}
			}
		}
		elseif ($constraintType == 'triggers')
		{
			self::updateTableTriggers($fieldsXml, $newTable, $originalTable);
		}

		$contentElement->allContentElementsFields = explode(',', $allContentElementsFields);
		$contentElement->allPrimaryKeys = array_keys($primaryKeys);

		RTranslationHelper::setInstalledTranslationTables(
			$option,
			$originalTable,
			$contentElement
		);
		self::saveRedcoreTranslationConfig();

		if ($showNotifications)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_INSTALLED'), 'message');
		}

		return true;
	}

	/**
	 * Adds table Index Keys based on primary keys for improved performace
	 *
	 * @param   array   $fieldsXml  Array of fields from content Element
	 * @param   string  $newTable   Table name
	 *
	 * @return  boolean  Returns true if Content element was successfully installed
	 */
	public static function updateTableIndexKeys($fieldsXml, $newTable)
	{
		$indexKeys = array();
		$db = JFactory::getDbo();

		$tableKeys      = $db->getTableKeys($newTable);
		$languageKey    = $db->qn('rctranslations_language');
		$stateKey       = $db->qn('rctranslations_state');

		foreach ($fieldsXml as $field)
		{
			if ((string) $field['type'] == 'referenceid')
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
				JFactory::getApplication()->enqueueMessage(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');

				return false;
			}
		}

		return true;
	}

	/**
	 * Adds table triggers based on primary keys for constraint between tables
	 *
	 * @param   array   $fieldsXml      Array of fields from content Element
	 * @param   string  $newTable       Translation Table name
	 * @param   string  $originalTable  Original Table name
	 *
	 * @return  boolean  Returns true if triggers are successfully installed
	 */
	public static function updateTableTriggers($fieldsXml, $newTable, $originalTable)
	{
		$db = JFactory::getDbo();
		$triggerKey = md5($originalTable . '_rctranslationstrigger');
		$primaryKeys = array();

		foreach ($fieldsXml as $field)
		{
			if ((string) $field['type'] == 'referenceid')
			{
				$fieldName = (string) $field['name'];
				$primaryKeys[] = $db->qn($fieldName) . ' = OLD.' . $db->qn($fieldName);
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
				JFactory::getApplication()->enqueueMessage(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');

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
				$constraintKey = $db->qn(md5($newTable . '_' . $primaryKey . '_fk'));

				$query = 'ALTER TABLE ' . $db->qn($newTable) . ' DROP FOREIGN KEY ' . $constraintKey . ' # ' . $newTable . '_' . $primaryKey . '_fk';

				try
				{
					$db->setQuery($query);
					$db->execute();
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
	 * @param   array   $fieldsXml      Array of fields from content Element
	 * @param   string  $newTable       Translation Table name
	 * @param   string  $originalTable  Original Table name
	 *
	 * @return  boolean  Returns true if Foreign keys are successfully installed
	 */
	public static function updateTableForeignKeys($fieldsXml, $newTable, $originalTable)
	{
		$db = JFactory::getDbo();

		if (!empty($fieldsXml))
		{
			foreach ($fieldsXml as $field)
			{
				if ((string) $field['type'] == 'referenceid')
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
							JFactory::getApplication()->enqueueMessage(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');

							return false;
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * Uninstall Content Element from database
	 *
	 * @param   string  $option             The Extension Name ex. com_redcore
	 * @param   string  $xmlFile            XML file to install
	 * @param   bool    $showNotifications  Show notifications
	 *
	 * @return  boolean  Returns true if Content element was successfully installed
	 */
	public static function uninstallContentElement($option = 'com_redcore', $xmlFile = '', $showNotifications = true)
	{
		$translationTables = RTranslationHelper::getInstalledTranslationTables();

		if (!empty($translationTables))
		{
			$db = JFactory::getDbo();

			foreach ($translationTables as $translationTable => $translationTableParams)
			{
				if ($option == $translationTableParams->option && $xmlFile == $translationTableParams->xml)
				{
					$newTable = self::getTranslationsTableName($translationTable, '');

					try
					{
						self::updateTableTriggers(array(), '', $translationTable);
						$db->dropTable($newTable);

						RTranslationHelper::setInstalledTranslationTables($option, $translationTable, null);
						self::saveRedcoreTranslationConfig();
					}
					catch (Exception $e)
					{
						JFactory::getApplication()->enqueueMessage(JText::sprintf('LIB_REDCORE_TRANSLATIONS_DELETE_ERROR', $e->getMessage()), 'error');
					}
				}
			}
		}

		if ($showNotifications)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_UNINSTALLED'), 'message');
		}

		return true;
	}

	/**
	 * Purge Content Element Table
	 *
	 * @param   string  $option   The Extension Name ex. com_redcore
	 * @param   string  $xmlFile  XML file to install
	 *
	 * @return  boolean  Returns true if Content element was successfully purged
	 */
	public static function purgeContentElement($option = 'com_redcore', $xmlFile = '')
	{
		// Load Content Element
		$contentElement = RTranslationHelper::getContentElement($option, $xmlFile);

		if (empty($contentElement) || empty($contentElement->table))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_NOT_INSTALLED'), 'warning');

			return false;
		}

		// Check if that table is already installed
		$columns = self::getTranslationsTableColumns($contentElement->table);

		if (!empty($columns))
		{
			// Delete Table
			$db = JFactory::getDbo();

			$newTable = self::getTranslationsTableName($contentElement->table);

			try
			{
				$db->truncateTable($newTable);
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');

				return false;
			}
		}

		JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_PURGED'), 'message');

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
		// Load Content Element
		$contentElement = RTranslationHelper::getContentElement($option, $xmlFile);

		if (self::uninstallContentElement($option, $xmlFile) || empty($contentElement->table))
		{
			if (empty($contentElement))
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_NOT_INSTALLED'), 'warning');

				return false;
			}

			$xmlFilePath = RTranslationContentElement::getContentElementXmlPath($option, $xmlFile);

			try
			{
				JFile::delete($xmlFilePath);
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');

				return false;
			}

			JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_DELETED'), 'message');

			return true;
		}

		return false;
	}

	/**
	 * Preforms Batch action against all Content elements of given Extension
	 *
	 * @param   string  $option             The Extension Name ex. com_redcore
	 * @param   string  $action             Action to preform
	 * @param   bool    $showNotifications  Show notification after each Action
	 *
	 * @return  boolean  Returns true if Action was successful
	 */
	public static function batchContentElements($option = 'com_redcore', $action = '', $showNotifications = true)
	{
		$contentElements = RTranslationHelper::getContentElements($option);

		if (!empty($contentElements))
		{
			foreach ($contentElements as $contentElement)
			{
				switch ($action)
				{
					case 'install':
						self::installContentElement($option, $contentElement->contentElementXml, $showNotifications);
						break;
					case 'uninstall':
						self::uninstallContentElement($option, $contentElement->contentElementXml, $showNotifications);
						break;
					case 'purge':
						self::purgeContentElement($option, $contentElement->contentElementXml);
						break;
					case 'delete':
						self::deleteContentElement($option, $contentElement->contentElementXml);
						break;
				}
			}
		}

		// Delete missing tables as well
		if ($action == 'uninstall')
		{
			$translationTables = RTranslationHelper::getInstalledTranslationTables();

			if (!empty($translationTables))
			{
				foreach ($translationTables as $translationTableParams)
				{
					if ($option == $translationTableParams->option)
					{
						self::uninstallContentElement($option, $translationTableParams->xml, $showNotifications);
					}
				}
			}
		}

		return true;
	}

	/**
	 * Upload Content Element to redcore media location
	 *
	 * @param   string  $option  The Extension Name ex. com_redcore
	 * @param   array   $files   The array of Files (file descriptor returned by PHP)
	 *
	 * @return  boolean  Returns true if Upload was successful
	 */
	public static function uploadContentElement($option = 'com_redcore', $files = array())
	{
		$uploadOptions = array(
			'allowedFileExtensions' => 'xml',
			'allowedMIMETypes'      => 'application/xml, text/xml',
			'overrideExistingFile'  => true,
		);

		return RFilesystemFile::uploadFiles($files, RTranslationContentElement::getContentElementFolderPath($option), $uploadOptions);
	}

	/**
	 * Method to save the configuration data.
	 *
	 * @return  bool   True on success, false on failure.
	 */
	public static function saveRedcoreTranslationConfig()
	{
		$data = array();
		$component = JComponentHelper::getComponent('com_redcore');

		$component->params->set('translations', RTranslationHelper::getInstalledTranslationTables());

		$data['params'] = $component->params->toString('JSON');

		$dispatcher = RFactory::getDispatcher();
		$table = JTable::getInstance('Extension');
		$isNew = true;

		// Load the previous Data
		if (!$table->load($component->id))
		{
			return false;
		}

		// Bind the data.
		if (!$table->bind($data))
		{
			return false;
		}

		// Check the data.
		if (!$table->check())
		{
			return false;
		}

		// Trigger the onConfigurationBeforeSave event.
		$result = $dispatcher->trigger('onExtensionBeforeSave', array('com_redcore.config', &$table, $isNew));

		if (in_array(false, $result, true))
		{
			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			return false;
		}

		// Trigger the onConfigurationAfterSave event.
		$dispatcher->trigger('onExtensionAfterSave', array('com_redcore.config', &$table, $isNew));

		return true;
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
}
