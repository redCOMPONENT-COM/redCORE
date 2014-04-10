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
 * A Translation Table helper.
 *
 * @package     Redcore
 * @subpackage  Component
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

		return null;
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
	 * @param   string  $option   The Extension Name ex. com_redcore
	 * @param   string  $xmlFile  XML file to install
	 *
	 * @return  boolean  Returns true if Content element was successfully installed
	 */
	public static function installContentElement($option = 'com_redcore', $xmlFile = '')
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
		$fields = array();
		$primaryKeys = array();
		$primaryKeys[] = $db->qn('language');
		$fieldsXml = $contentElement->getTranslateFields();

		foreach ($fieldsXml as $field)
		{
			$fields[(string) $field['name']] = $db->qn((string) $field['name']);

			if ((string) $field['type'] == 'referenceid')
			{
				$primaryKeys[] = $db->qn((string) $field['name']);
			}
		}

		$newTable = $db->qn(self::getTranslationsTableName($contentElement->table));
		$originalTable = $db->qn('#__' . $contentElement->table);
		$primaryKeys = implode(',', $primaryKeys);
		$primaryKey = ' KEY ' . $db->qn('language_idx') . ' (' . $primaryKeys . ') ';

		if (empty($columns))
		{
			$fieldsCreate = implode(',', $fields);

			$query = 'CREATE TABLE ' . $newTable
				. ' (' . $db->qn('language') . ' char(7) NOT NULL DEFAULT ' . $db->q('') . ', '
				. $primaryKey
				. ' ) SELECT ' . $fieldsCreate . ' FROM ' . $originalTable . ' where 1 = 2';

			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');

				return false;
			}
		}
		else
		{
			// Language is automatically added to the table if table exists
			unset($columns['language']);
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
				$originalColumns = $db->getTableColumns('#__' . $contentElement->table, false);

				$query = 'ALTER TABLE ' . $newTable
					. ' DROP KEY ' . $db->qn('language_idx');

				foreach ($fields as $fieldKey => $field)
				{
					if (!empty($originalColumns[$fieldKey]))
					{
						$query .= ', ADD COLUMN ' . $field
							. ' ' . $originalColumns[$fieldKey]->Type
							. ' ' . ($originalColumns[$fieldKey]->Null == 'NO' ? 'NOT NULL' : 'NULL')
							. ' DEFAULT ' . $db->q($originalColumns[$fieldKey]->Default);
					}
				}

				$query .= ', ADD ' . $primaryKey;

				try
				{
					$db->setQuery($query);
					$db->execute();
				}
				catch (Exception $e)
				{
					JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');

					return false;
				}
			}

			// We delete extra columns
			if (!empty($columnKeys))
			{
				$query = 'ALTER TABLE ' . $newTable
					. ' DROP KEY ' . $db->qn('language_idx');

				foreach ($columnKeys as $columnKey)
				{
					$query .= ', DROP COLUMN ' . $db->qn($columnKey);
				}

				$query .= ', ADD ' . $primaryKey;

				try
				{
					$db->setQuery($query);
					$db->execute();
				}
				catch (Exception $e)
				{
					JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');

					return false;
				}
			}
		}

		JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_INSTALLED'), 'message');

		return true;
	}

	/**
	 * Uninstall Content Element from database
	 *
	 * @param   string  $option   The Extension Name ex. com_redcore
	 * @param   string  $xmlFile  XML file to install
	 *
	 * @return  boolean  Returns true if Content element was successfully installed
	 */
	public static function uninstallContentElement($option = 'com_redcore', $xmlFile = '')
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

			$newTable = $db->qn(self::getTranslationsTableName($contentElement->table));

			$query = 'DROP TABLE ' . $newTable;

			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');

				return false;
			}
		}

		JFactory::getApplication()->enqueueMessage(JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_UNINSTALLED'), 'message');

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

			$newTable = $db->qn(self::getTranslationsTableName($contentElement->table));

			$query = 'TRUNCATE ' . $newTable;

			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');

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
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), 'error');

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
	 * @param   string  $option  The Extension Name ex. com_redcore
	 * @param   string  $action  Action to preform
	 *
	 * @return  boolean  Returns true if Action was successful
	 */
	public static function batchContentElements($option = 'com_redcore', $action = '')
	{
		$contentElements = RTranslationHelper::getContentElements($option);

		if (!empty($contentElements))
		{
			foreach ($contentElements as $contentElement)
			{
				switch ($action)
				{
					case 'install':
						self::installContentElement($option, $contentElement->contentElementXml);
						break;
					case 'uninstall':
						self::uninstallContentElement($option, $contentElement->contentElementXml);
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
}
