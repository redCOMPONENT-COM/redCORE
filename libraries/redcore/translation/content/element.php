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
 * A ContentElement helper.
 *
 * @package     Redcore
 * @subpackage  Component
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

		$content = @file_get_contents(self::getContentElementXmlPath($extension, $contentElementXml));

		if (!is_string($content))
		{
			return false;
		}

		$xmlDoc = new SimpleXMLElement($content);

		$this->xml = $xmlDoc;
		$this->name = $this->getContentElementName();
		$this->table = $this->getTableName();
		$this->primaryKey = $this->getPrimaryKey();
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
			return strtolower($this->xml->reference->table['name']);
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
			return $this->xml->name;
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
					return strtolower($field['name']);
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
	 * Get status of the Content Element
	 *
	 * @return  String  Content Element Status
	 */
	public function getStatus()
	{
		$fieldsTable = RTranslationTable::getTranslationsTableColumns($this->table);

		if (empty($fieldsTable))
		{
			return JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_NOT_INSTALLED');
		}

		// Language is automatically added to the table if table exists
		unset($fieldsTable['language']);
		$fieldsXml = $this->getTranslateFields();
		$fields = array();

		foreach ($fieldsXml as $fieldKey => $field)
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

		$return = '';

		if (!empty($fields))
		{
			$return .= JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_FIELDS_MISSING') . implode(', ', $fields);
		}

		return JText::_('COM_REDCORE_CONFIG_TRANSLATIONS_CONTENT_ELEMENT_INSTALLED') . ' ' . $return;
	}

	/**
	 * Get Path to the Content element XML file
	 *
	 * @param   string  $option   The Extension Name ex. com_redcore
	 * @param   string  $xmlFile  XML file to install
	 *
	 * @return  string  Path to XML file
	 */
	public static function getContentElementXmlPath($option = 'com_redcore', $xmlFile = '')
	{
		return JPATH_SITE . '/media/redcore/contentelements/' . $option . '/' . $xmlFile;
	}
}
