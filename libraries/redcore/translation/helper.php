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
 * A Translation helper.
 *
 * @package     Redcore
 * @subpackage  Translation
 * @since       1.0
 */
class RTranslationHelper
{
	/**
	 * Defines if jQuery Migrate should be loaded in Frontend component/modules
	 *
	 * @var    bool
	 */
	public static $pluginParams = null;

	/**
	 * An array to hold tables from database
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $contentElements = array();

	/**
	 * An array to hold tables from database
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $installedTranslationTables = null;

	/**
	 * An array to hold tables from database
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $siteLanguage = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		self::$pluginParams = new JRegistry;
	}

	/**
	 * Loading of related XML files
	 *
	 * @param   string  $extensionName  Extension name
	 *
	 * @return  array  List of objects
	 */
	public static function loadContentElements($extensionName = '')
	{
		jimport('joomla.filesystem.folder');
		$extensions = array();

		if (empty($extensionName))
		{
			$iterator = new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator(JPATH_SITE . '/media/redcore/translations')
			);

			/** @var SplFileInfo $fileInfo */
			foreach ($iterator as $fileInfo)
			{
				if ($fileInfo->isDir())
				{
					$extensions[] = $fileInfo->getFilename();
				}
			}
		}
		else
		{
			$extensions[] = $extensionName;
		}

		foreach ($extensions as $extension)
		{
			$contentElementsXml = array();
			$contentElementsXmlRedcorePath = RTranslationContentElement::getContentElementFolderPath($extension, true);

			if (is_dir($contentElementsXmlRedcorePath))
			{
				$contentElementsXml = JFolder::files($contentElementsXmlRedcorePath, '.xml', true);
			}

			$contentElementsXmlExtensionPath = RTranslationContentElement::getContentElementFolderPath($extension);

			if (is_dir($contentElementsXmlExtensionPath))
			{
				$contentElementsXmlExtension = JFolder::files($contentElementsXmlExtensionPath, '.xml', true);

				if (!empty($contentElementsXmlExtension))
				{
					$contentElementsXml = array_merge($contentElementsXml, $contentElementsXmlExtension);
				}
			}

			if (!empty($contentElementsXml))
			{
				self::$contentElements[$extension] = array();

				foreach ($contentElementsXml as $contentElementXml)
				{
					$contentElement = new RTranslationContentElement($extension, $contentElementXml);
					self::$contentElements[$extension][$contentElement->table] = $contentElement;
				}
			}
		}
	}

	/**
	 * Loading of related XML files
	 *
	 * @param   string  $extensionName  Extension name
	 *
	 * @return  array  List of objects
	 */
	public static function getContentElements($extensionName = '')
	{
		if (empty(self::$contentElements) || empty(self::$contentElements[$extensionName]))
		{
			self::loadContentElements($extensionName);
		}

		if (!empty(self::$contentElements[$extensionName]))
		{
			return self::$contentElements[$extensionName];
		}

		return array();
	}

	/**
	 * Loading of related XML files
	 *
	 * @param   string  $extensionName       Extension name
	 * @param   string  $contentElementsXml  XML File name
	 *
	 * @return  mixed  RTranslationContentElement if found or null
	 */
	public static function getContentElement($extensionName = '', $contentElementsXml = '')
	{
		$contentElements = self::getContentElements($extensionName);

		if (!empty($contentElements))
		{
			foreach ($contentElements as $contentElement)
			{
				if ($contentElement->contentElementXml == $contentElementsXml)
				{
					return $contentElement;
				}
			}
		}

		return null;
	}

	/**
	 * Get list of all translation tables with columns
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function getInstalledTranslationTables()
	{
		if (!isset(self::$installedTranslationTables))
		{
			$db = JFactory::getDbo();

			// We do not want to translate this value
			$db->translate = false;

			$component = JComponentHelper::getComponent('com_redcore');

			// We put translation check back on
			$db->translate = true;
			self::$installedTranslationTables = (array) $component->params->get('translations', array());
		}

		return self::$installedTranslationTables;
	}

	/**
	 * Get default language
	 *
	 * @param   string  $client  Name of the client to get (site|admin)
	 *
	 * @return  string  Name of the language ex. en-GB
	 */
	public static function getSiteLanguage($client = 'site')
	{
		if (!isset(self::$siteLanguage))
		{
			$db = JFactory::getDbo();

			// We do not want to translate this value
			$db->translate = false;

			self::$siteLanguage = JComponentHelper::getParams('com_languages')->get($client);

			// We put translation check back on
			$db->translate = true;
		}

		return self::$siteLanguage;
	}

	/**
	 * Set a value to translation table list
	 *
	 * @param   string  $option          Extension option name
	 * @param   string  $table           Table name
	 * @param   object  $contentElement  Content Element
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function setInstalledTranslationTables($option, $table, $contentElement)
	{
		// Initialize installed tables before proceeding
		self::getInstalledTranslationTables();

		if (empty($contentElement))
		{
			unset(self::$installedTranslationTables[$table]);
			self::loadContentElements($option);
		}
		else
		{
			self::$installedTranslationTables[$table] = array(
				'option' => $option,
				'table' => $table,
				'name' => $contentElement->name,
				'columns' => $contentElement->allContentElementsFields,
				'primaryKeys' => $contentElement->allPrimaryKeys,
				'xml' => $contentElement->contentElementXml,
				'path' => $contentElement->contentElementXmlPath,
				'formLinks' => $contentElement->getEditForms(),
			);

			self::loadContentElements($option);
		}
	}

	/**
	 * Checks if this is edit form and restricts table from translations
	 *
	 * @param   array  $translationTables  List of translation tables
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function removeFromEditForm($translationTables)
	{
		$input = JFactory::getApplication()->input;
		$option = $input->getString('option', '');
		$view = $input->getString('view', '');
		$layout = $input->getString('layout', '');
		$task = $input->getString('layout', '');

		if ($layout == 'edit' || $task == 'edit')
		{
			foreach ($translationTables as $tableKey => $translationTable)
			{
				if (!empty($translationTable->formLinks))
				{
					foreach ($translationTable->formLinks as $formLink)
					{
						$formLinks = explode('#', $formLink);

						if (count($formLinks) > 1 && $option == $formLinks[0] && in_array($view, array('form', $formLinks[1])))
						{
							unset($translationTables[$tableKey]);
							break;
						}
					}
				}
			}
		}

		return $translationTables;
	}
}
