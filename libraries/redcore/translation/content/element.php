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
 * A ContentElement helper.
 *
 * @package     Redcore
 * @subpackage  Translation
 * @since       1.0
 */
final class RTranslationContentElement
{
	/**
	 * The XML file content
	 *
	 * @var  SimpleXMLElement
	 */
	public $xml;

	/**
	 * The Extension Name ex. com_redcore
	 *
	 * @var  String
	 */
	public $extension;

	/**
	 * The Content Element XML file name
	 *
	 * @var  String
	 */
	public $contentElementXml;

	/**
	 * Full path to the Content Element XML file
	 *
	 * @var  String
	 */
	public $contentElementXmlPath;

	/**
	 * The Content Element name
	 *
	 * @var  String
	 */
	public $name;

	/**
	 * Table name
	 *
	 * @var  String
	 */
	public $table;

	/**
	 * Table Primary Key
	 *
	 * @var  String
	 */
	public $primaryKey;

	/**
	 * Constructor
	 *
	 * @param   string  $extension          The Extension Name ex. com_redcore
	 * @param   string  $contentElementXml  The Content Element XML file name
	 */
	public function __construct($extension = 'com_redcore', $contentElementXml = 'table.xml')
	{
		$this->extension = $extension;
		$this->contentElementXml = $contentElementXml;
		$this->contentElementXmlPath = self::getContentElementXmlPath($extension, $contentElementXml);

		$content = @file_get_contents($this->contentElementXmlPath);

		if (is_string($content))
		{
			$xmlDoc = new SimpleXMLElement($content);

			$this->xml = $xmlDoc;
			$this->table = $this->getTableName();
			$this->name = $this->getContentElementName();
			$this->primaryKey = $this->getPrimaryKey();
		}
	}

	/**
	 * Gets Table name
	 *
	 * @return  String  Table Name
	 */
	public function getTableName()
	{
		if (!empty($this->xml->reference->table['name']))
		{
			return strtolower((string) $this->xml->reference->table['name']);
		}

		return '';
	}

	/**
	 * Gets Content Element name
	 *
	 * @return  String  Name
	 */
	public function getContentElementName()
	{
		if (!empty($this->xml->name))
		{
			return (string) $this->xml->name;
		}

		return '';
	}

	/**
	 * Name of primary id
	 *
	 * @return  String  Table Primary Key
	 */
	public function getPrimaryKey()
	{
		if (!empty($this->xml->reference->table->field))
		{
			foreach ($this->xml->reference->table->field as $field)
			{
				if ($field['type'] == 'referenceid')
				{
					return strtolower((string) $field['name']);
				}
			}
		}

		return 'id';
	}

	/**
	 * Get list of fields for translate
	 *
	 * @return  array  Array of translatable fields
	 */
	public function getTranslateFields()
	{
		if (!empty($this->xml->reference->table->field))
		{
			return $this->xml->reference->table->field;
		}

		return array();
	}

	/**
	 * Get list of edit forms where we will not show translation
	 *
	 * @return  array  Array of edit form locations
	 */
	public function getEditForms()
	{
		if (!empty($this->xml->reference->component->form))
		{
			$forms = $this->xml->reference->component->form;
			$formLinks = array();

			foreach ($forms as $form)
			{
				$formLinks[] = (string) $form;
			}

			return $formLinks;
		}

		return array();
	}

	/**
	 * Get status of the Content Element
	 *
	 * @return  String  Content Element Status
	 */
	public function getStatus()
	{
		$return = '';

		if (empty($this->table))
		{
			$return .= ' ' . JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_NOT_VALID_FILE');
		}

		$fieldsTable = RTranslationTable::getTranslationsTableColumns($this->table);

		if (empty($fieldsTable))
		{
			return JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_NOT_INSTALLED') . $return;
		}

		// Language is automatically added to the table if table exists
		$fieldsTable = RTranslationTable::removeFixedColumnsFromArray($fieldsTable);
		$fieldsXml = $this->getTranslateFields();
		$fields = array();

		foreach ($fieldsXml as $field)
		{
			$fields[(string) $field['name']] = (string) $field['name'];

			foreach ($fieldsTable as $columnKey => $columnKeyValue)
			{
				$fields[$columnKey] = $columnKey;

				if ((string) $field['name'] == $columnKey)
				{
					unset($fieldsTable[$columnKey]);
					unset($fields[$columnKey]);
				}
			}
		}

		if (!empty($fields))
		{
			$return .= ' ' . JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_FIELDS_MISSING') . implode(', ', $fields);
		}

		return JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_INSTALLED') . $return;
	}

	/**
	 * Get Path to the Content element XML file
	 *
	 * @param   string  $option   The Extension Name ex. com_redcore
	 * @param   string  $xmlFile  XML file to install
	 *
	 * @return  string  Path to XML file
	 */
	public static function getContentElementXmlPath($option = '', $xmlFile = '')
	{
		jimport('joomla.filesystem.file');

		if (file_exists(self::getContentElementFolderPath($option) . '/' . $xmlFile))
		{
			return self::getContentElementFolderPath($option) . '/' . $xmlFile;
		}

		return self::getContentElementFolderPath($option, true) . '/' . $xmlFile;
	}

	/**
	 * Get Path to the Content element XML file
	 *
	 * @param   string  $option       The Extension Name ex. com_redcore
	 * @param   bool    $fromRedcore  Use redcore folder location
	 * @param   bool    $fromOption   Use redcore folder location
	 *
	 * @return  string  Path to XML file
	 */
	public static function getContentElementFolderPath($option = '', $fromRedcore = false, $fromOption = false)
	{
		jimport('joomla.filesystem.path');
		$extensionPath = JPATH_SITE . '/media/' . $option . '/translations';
		$redcorePath = JPATH_SITE . '/media/redcore/translations';

		if (empty($option))
		{
			return $redcorePath;
		}
		elseif ($fromOption)
		{
			return $extensionPath;
		}
		elseif (!is_dir($extensionPath) || $fromRedcore)
		{
			return $redcorePath . '/' . $option;
		}

		return $extensionPath;
	}
}
