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
 * A Translation helper.
 *
 * @package     Redcore
 * @subpackage  Component
 * @since       1.0
 */
final class RTranslationHelper
{
	/**
	 * An array to hold tables from database
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $contentElements = array();

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
				new RecursiveDirectoryIterator(JPATH_SITE . '/media/redcore/contentelements')
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
			$contentElementsXml = JFolder::files(JPATH_SITE . '/media/redcore/contentelements/' . $extension, '.xml', true);

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
		if (empty(self::$contentElements))
		{
			self::loadContentElements($extensionName);
		}

		if (!empty(self::$contentElements[$extensionName]))
		{
			return self::$contentElements[$extensionName];
		}

		return null;
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
}
