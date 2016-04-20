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
	 * The Content Element version
	 *
	 * @var  String
	 */
	public $version;

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
	 * An array to hold tables from database
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $contentElements = array();

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
				$this->version = $this->getContentElementVersion();
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
		if (isset($this->xml->name))
		{
			return (string) $this->xml->name;
		}

		return '';
	}

	/**
	 * Gets Content Element name
	 *
	 * @return  String  Name
	 */
	public function getContentElementVersion()
	{
		if (isset($this->xml->version))
		{
			return (string) $this->xml->version;
		}

		return '1.0.0';
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
	 * Get table description
	 *
	 * @return  string
	 */
	public function getTranslateDescription()
	{
		if (isset($this->xml->description))
		{
			return (string) $this->xml->description;
		}

		return '';
	}

	/**
	 * Get table copyright
	 *
	 * @return  string
	 */
	public function getTranslateCopyright()
	{
		if (isset($this->xml->copyright))
		{
			return (string) $this->xml->copyright;
		}

		return '';
	}

	/**
	 * Get table author
	 *
	 * @return  string
	 */
	public function getTranslateAuthor()
	{
		if (isset($this->xml->author))
		{
			return (string) $this->xml->author;
		}

		return '';
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
		$formLink['checkoriginalid'] = !empty($formLink['checkoriginalid']) ? $formLink['checkoriginalid'] : 'false';
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
		$xmlFileOptions = self::loadAllContentElements();
		$tables = RTranslationTable::getInstalledTranslationTables(true);
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

							if (str_replace('#__', '', $table->name) == $contentElement->table)
							{
								$xmlFiles[$key]->mainTable = $table;
							}
						}
					}
				}
			}
		}

		return $xmlFiles;
	}

	/**
	 * Found out which extensions have content element files
	 *
	 * @param   string  $mediaPath      Media path
	 * @param   bool    $redcoreFolder  Is this redcore media folder
	 *
	 * @return  array  List of objects
	 */
	public static function loadAllContentElements($mediaPath = '', $redcoreFolder = false)
	{
		if (!empty(self::$contentElements) && !$redcoreFolder)
		{
			return self::$contentElements;
		}

		jimport('joomla.filesystem.folder');

		if ($mediaPath == '')
		{
			self::loadAllContentElements(JPATH_SITE . '/media');
			self::loadAllContentElements(JPATH_SITE . '/media/redcore/translations', true);

			return self::$contentElements;
		}

		$mediaFolders = JFolder::folders($mediaPath);

		if ($mediaFolders)
		{
			foreach ($mediaFolders as $mediaFolder)
			{
				// We have already processed redcore media folder
				if ($mediaFolder == 'redcore' && $mediaPath == JPATH_SITE . '/media')
				{
					continue;
				}

				$folder = $mediaPath . '/' . $mediaFolder . ($redcoreFolder ? '' : '/translations');

				if (is_dir($folder))
				{
					$contentElementsXml = JFolder::files($folder, '.xml', false);

					if (!empty($contentElementsXml))
					{
						if (!isset(self::$contentElements[$mediaFolder]))
						{
							self::$contentElements[$mediaFolder] = array();
						}

						foreach ($contentElementsXml as $contentElementXml)
						{
							$contentElement = new RTranslationContentElement($mediaFolder, $contentElementXml);

							if (!empty($contentElement->table) || $mediaFolder == 'upload')
							{
								self::$contentElements[$mediaFolder][$folder . '/' . $contentElement->table] = $contentElement;
							}
						}
					}
				}
			}
		}

		return self::$contentElements;
	}

	/**
	 * Gets XML string from the given table object
	 *
	 * @param   RedcoreTableTranslation_Table  $table  Table object
	 *
	 * @return  SimpleXMLElement
	 */
	public static function generateTranslationXml($table)
	{
		$xml = new SimpleXMLElement('<?xml version="1.0"?><contentelement type="contentelement"></contentelement>');

		$params = !empty($table->params) ? json_decode($table->params, true) : array();

		$xml->addChild('name', $table->title);
		$xml->addChild('author', $params['author'] ? $params['author'] : '');
		$xml->addChild('copyright', $params['copyright'] ? $params['copyright'] : '');
		$xml->addChild('version', $table->version);
		$xml->addChild('description', $params['description'] ? $params['description'] : '');

		$reference = $xml->addChild('reference');

		// Add main table
		$referenceTable = $reference->addChild('table');
		$referenceTable->addAttribute('name', str_replace('#__', '', $table->name));
		$columns = $table->getTranslationColumns();

		// Add table columns
		foreach ($columns as $column)
		{
			$field = $referenceTable->addChild('field', $column['title']);

			$field->addAttribute('name', $column['name']);
			$field->addAttribute('type', $column['value_type']);
			$field->addAttribute('translate', $column['column_type'] == 'translate' ? '1' : '0');
			$field->addAttribute('alwaysFallback', $column['fallback'] ? 'true' : 'false');
			$field->addAttribute('filter', $column['filter']);
			$field->addAttribute('description', $column['description']);

			if (!empty($column['params']) && is_string($column['params']))
			{
				$column['params'] = json_decode($column['params'], true);
			}

			if ($column['params'])
			{
				foreach ($column['params'] as $paramKey => $param)
				{
					$field->addAttribute($paramKey, $param);
				}
			}
		}

		// Add table filters for editor
		self::addChildWithCDATA($referenceTable, 'filter', $table->filter_query);

		// Add reference edit forms
		$referenceComponent = $reference->addChild('component');
		$formLinks = $table->form_links ? json_decode($table->form_links, true) : array();

		foreach ($formLinks as $formLink)
		{
			$editForm = $referenceComponent->addChild('editForm');

			$editForm->addAttribute('admin', $formLink['admin']);
			$editForm->addAttribute('option', $formLink['option']);
			$editForm->addAttribute('view', $formLink['view']);
			$editForm->addAttribute('layout', $formLink['layout']);
			$editForm->addAttribute('identifier', $formLink['identifier']);
			$editForm->addAttribute('showbutton', $formLink['showbutton']);
			$editForm->addAttribute('htmlposition', $formLink['htmlposition']);
			$editForm->addAttribute('checkoriginalid', $formLink['checkoriginalid']);
		}

		return $xml;
	}

	/**
	 * Method to add child with text inside CDATA
	 *
	 * @param   SimpleXMLElement  &$xml   Xml element
	 * @param   string            $name   Name of the child
	 * @param   string            $value  Value of the child
	 *
	 * @return  SimpleXMLElement
	 *
	 * @since   1.4
	 */
	public static function addChildWithCDATA(&$xml, $name, $value = '')
	{
		$newChild = $xml->addChild($name);

		if (is_null($newChild))
		{
			return $newChild;
		}

		$node = dom_import_simplexml($newChild);
		$no   = $node->ownerDocument;
		$node->appendChild($no->createCDATASection($value));

		return $newChild;
	}

	/**
	 * Loading of related XML files
	 *
	 * @param   string  $extensionName       Extension name
	 * @param   string  $contentElementsXml  XML File name
	 * @param   bool    $fullPath            Full path to the XML file
	 *
	 * @return  mixed  RTranslationContentElement if found or null
	 */
	public static function getContentElement($extensionName = '', $contentElementsXml = '', $fullPath = false)
	{
		$contentElements = self::getContentElements(false, $extensionName);

		if (!empty($contentElements))
		{
			$contentElementsXmlFullPath = self::getPathWithoutBase($contentElementsXml);

			foreach ($contentElements as $contentElement)
			{
				if ($fullPath
					&& self::getPathWithoutBase($contentElement->contentElementXmlPath) == $contentElementsXmlFullPath)
				{
					return $contentElement;
				}

				if ($contentElement->contentElementXml == $contentElementsXml)
				{
					return $contentElement;
				}
			}
		}

		return null;
	}
}
