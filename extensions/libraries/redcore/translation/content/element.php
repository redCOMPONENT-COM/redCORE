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

		if (!empty($contentElementXml))
		{
			$this->contentElementXmlPath = self::getContentElementXmlPath($extension, $contentElementXml);

			$content = @file_get_contents($this->contentElementXmlPath);

			if (is_string($content))
			{
				$xmlDoc = new SimpleXMLElement($content);

				$this->xml_hashed = md5($content);
				$this->xml = $xmlDoc;
				$this->table = $this->getTableName();
				$this->name = $this->getContentElementName();
				$this->primaryKey = $this->getPrimaryKey();
			}
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
	 * Get list of filters for translation editor
	 *
	 * @return  array  Array of translatable fields
	 */
	public function getTranslateFilter()
	{
		if (!empty($this->xml->reference->table->filter))
		{
			return $this->xml->reference->table->filter;
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
		$formLinks = array();

		if (isset($this->xml->reference->component))
		{
			// Old way
			if (isset($this->xml->reference->component->form))
			{
				foreach ($this->xml->reference->component->form as $form)
				{
					$formArray = explode('#', $form);
					$formLink = array();

					if (count($formArray) > 1)
					{
						$formLink['option'] = $formArray[0];
						$formLink['view'] = $formArray[1];
						$formLink['identifier'] = !empty($formArray[2]) ? $formArray[2] : '';
						$formLink['layout'] = !empty($formArray[4]) ? preg_replace("/[^a-zA-Z0-9]+/", "", $formArray[4]) : 'edit';

						// Set defaults
						$this->getEditFormDefaults($formLink);

						$formLinks[] = $formLink;
					}
				}
			}
			// Current structure
			else
			{
				foreach ($this->xml->reference->component->editForm as $editForm)
				{
					$formLink = array();

					if (!empty($editForm['option']) && !empty($editForm['view']))
					{
						$attributes = $editForm->attributes();

						foreach ($attributes as $key => $attribute)
						{
							$formLink[strtolower($key)] = (string) $attribute;
						}

						// Set defaults
						$this->getEditFormDefaults($formLink);

						$formLinks[] = $formLink;
					}
				}
			}
		}

		return $formLinks;
	}

	/**
	 * Sets default values to the form links if they are not set
	 *
	 * @param   array  &$formLink  Form link options
	 *
	 * @return  void
	 */
	public function getEditFormDefaults(&$formLink)
	{
		// Defaults
		$formLink['admin'] = !empty($formLink['admin']) ? $formLink['admin'] : 'false';
		$formLink['layout'] = !empty($formLink['layout']) ? $formLink['layout'] : 'edit';
		$formLink['identifier'] = isset($formLink['identifier']) ? $formLink['identifier'] : 'id';
		$formLink['showbutton'] = !empty($formLink['showbutton']) ? $formLink['showbutton'] : 'true';
		$formLink['htmlposition'] = !empty($formLink['htmlposition']) ? $formLink['htmlposition'] : '.btn-toolbar:first';
	}

	/**
	 * Get status of the Content Element
	 *
	 * @return  String  Content Element Status
	 */
	public function getFieldDifference()
	{
		$return = '';
		$fieldsTable = RTranslationTable::getTranslationsTableColumns($this->table);

		// Language is automatically added to the table if table exists
		$fieldsTable = RTranslationTable::removeFixedColumnsFromArray($fieldsTable);
		$fieldsXml = $this->getTranslateFields();
		$fields = array();

		foreach ($fieldsXml as $field)
		{
			$fields[(string) $field['name']] = (string) $field['name'];

			if ((string) $field['translate'] == '0')
			{
				unset($fieldsTable[(string) $field['name']]);
				unset($fields[(string) $field['name']]);
				continue;
			}

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
			$return .= JText::_('COM_REDCORE_TRANSLATION_TABLE_CONTENT_ELEMENT_FIELDS_MISSING') . implode(', ', $fields);
		}

		return $return;
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

	/**
	 * Gets path without base path
	 *
	 * @param   string  $path  Path
	 *
	 * @return  string
	 */
	public static function getPathWithoutBase($path)
	{
		return str_replace(JPATH_SITE, '', $path);
	}

	/**
	 * Gets path without base path
	 *
	 * @param   bool    $notInstalled       Filter only not installed Content elements
	 * @param   string  $onlyFromExtension  Show only from specific extension
	 *
	 * @return  array
	 */
	public static function getContentElements($notInstalled = false, $onlyFromExtension = '')
	{
		$xmlFileOptions = RTranslationHelper::loadAllContentElements();
		$tables = RTranslationHelper::getTranslationTables();
		$xmlFiles = array();

		if (!empty($xmlFileOptions))
		{
			foreach ($xmlFileOptions as $option => $xmlFileTables)
			{
				if (!empty($onlyFromExtension) && $onlyFromExtension != $option)
				{
					continue;
				}

				foreach ($xmlFileTables as $key => $contentElement)
				{
					$xmlFiles[$key] = $contentElement;

					if ($notInstalled && !empty($tables))
					{
						foreach ($tables as $table)
						{
							if ($table->xml_path == self::getPathWithoutBase($contentElement->contentElementXmlPath))
							{
								// We remove it from the list
								unset($xmlFiles[$key]);
								break;
							}
						}
					}
				}
			}
		}

		return $xmlFiles;
	}
}
