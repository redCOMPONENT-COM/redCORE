<?php
/**
 * @package     Redcore
 * @subpackage  Translation
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
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
			$db = JFactory::getDbo();

			return $db->getTableColumns($tableName, false);
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
			self::$tableList = $db->getTableList();
			self::$tablePrefix = $db->getPrefix();
		}

		return self::$tableList;
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
			JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_NOT_INSTALLED'), 'warning');

			return false;
		}

		// Create table with fields
		$db = JFactory::getDbo();

		// Check if that table is already installed
		$columns = self::getTranslationsTableColumns($contentElement->table);
		$originalColumns = $db->getTableColumns('#__' . $contentElement->table, false);
		$fields = array();
		$primaryKeys = array();
		$constraintKeys = array();
		$primaryKeys['rctranslations_language'] = $db->qn('rctranslations_language');
		$fieldsXml = $contentElement->getTranslateFields();
		$newTable = self::getTranslationsTableName($contentElement->table);
		$originalTable = '#__' . $contentElement->table;

		foreach ($fieldsXml as $field)
		{
			// If not in original table then do not create it
			if (empty($originalColumns[(string) $field['name']]))
			{
				JFactory::getApplication()->enqueueMessage(
					JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR_COLUMNS', $xmlFile, (string) $field['name']),
					'error'
				);

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
				$constraintKey = $db->qn(md5($newTable . '_' . $fieldName . '_fk'));
				$constraintKeys[$constraintKey] = 'ADD CONSTRAINT '
					. $constraintKey
					. ' FOREIGN KEY (' . $db->qn($fieldName) . ')'
					. ' REFERENCES ' . $db->qn($originalTable) . ' (' . $db->qn($fieldName) . ')'
					. '	ON DELETE CASCADE'
					. ' ON UPDATE NO ACTION ';
			}
		}

		if (empty($fields))
		{
			JFactory::getApplication()->enqueueMessage(
				JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR_NO_FIELDS', $xmlFile),
				'error'
			);

			return false;
		}

		$newTableCreated = false;

		if (empty($columns))
		{
			$newTableCreated = true;
			$query = 'CREATE TABLE ' . $db->qn($newTable)
				. ' ('
				. $db->qn('rctranslations_id') . ' int(10) PRIMARY KEY AUTO_INCREMENT, '
				. $db->qn('rctranslations_language') . ' char(7) NOT NULL DEFAULT ' . $db->q('') . ', '
				. $db->qn('rctranslations_originals') . ' TEXT NOT NULL, '
				. $db->qn('rctranslations_modified') . ' datetime NOT NULL DEFAULT ' . $db->q('0000-00-00 00:00:00') . ', '
				. $db->qn('rctranslations_state') . ' tinyint(3) NOT NULL DEFAULT ' . $db->q('1') . ', '
				. ' KEY ' . $db->qn('language_idx') . ' (' . $db->qn('rctranslations_language') . ') '
				. ' )';

			$db->setQuery($query);

			try
			{
				$db->execute();
				$columns = $db->getTableColumns($newTable, false);
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');

				return false;
			}
		}

		$primaryKeysIndex = implode(',', $primaryKeys);
		unset($primaryKeys['rctranslations_language']);
		$primaryKey = ' KEY ' . $db->qn('language_idx') . ' (' . $primaryKeysIndex . ') ';
		$allContentElementsFields = implode(',', array_keys($fields));

		$constraintKeysQuery = 'ALTER TABLE '
			. $db->qn($newTable)
			. ' '
			. implode(', ', $constraintKeys);

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

		$queryConstraints = '';

		if (!$newTableCreated)
		{
			$translationTables = RTranslationHelper::getInstalledTranslationTables();
			$installedTable = !empty($translationTables[$originalTable]) ? $translationTables[$originalTable] : null;

			if (!empty($installedTable->primaryKeys))
			{
				foreach ($installedTable->primaryKeys as $installedPrimaryKey)
				{
					$queryConstraints .= ', DROP FOREIGN KEY ' . $db->qn(md5($newTable . '_' . $installedPrimaryKey . '_fk'));
				}
			}
		}

		// We Add New columns
		if (!empty($fields))
		{
			$query = 'ALTER TABLE ' . $db->qn($newTable)
				. ' DROP KEY ' . $db->qn('language_idx');

			if (!$newTableCreated)
			{
				$query .= $queryConstraints;
			}

			foreach ($fields as $fieldKey => $field)
			{
				if (!empty($originalColumns[$fieldKey]))
				{
					$query .= ', ADD COLUMN ' . $field
						. ' ' . $originalColumns[$fieldKey]->Type
						. ' NULL'
						. ' DEFAULT NULL ';
				}
			}

			$query .= ', ADD ' . $primaryKey;

			try
			{
				$db->setQuery($query);
				$db->execute();

				// Adding the constraints afterwards
				if (!empty($constraintKeysQuery))
				{
					$db->setQuery($constraintKeysQuery);
					$db->execute();
				}
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');

				return false;
			}
		}

		// We delete extra columns
		if (!empty($columnKeys) && !$newTableCreated)
		{
			$query = 'ALTER TABLE ' . $db->qn($newTable)
				. ' DROP KEY ' . $db->qn('language_idx')
				. $queryConstraints;

			foreach ($columnKeys as $columnKey)
			{
				$query .= ', DROP COLUMN ' . $db->qn($columnKey);
			}

			$query .= ', ADD ' . $primaryKey;

			try
			{
				$db->setQuery($query);
				$db->execute();

				// Adding the constraints afterwards
				if (!empty($constraintKeysQuery))
				{
					$db->setQuery($constraintKeysQuery);
					$db->execute();
				}
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');

				return false;
			}
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
			catch (Exception $e)
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
		$result = $dispatcher->trigger('onExtensionBeforeSave', array('com_redcore.config', $table, $isNew));

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
		$dispatcher->trigger('onExtensionAfterSave', array('com_redcore.config', $table, $isNew));

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
}
