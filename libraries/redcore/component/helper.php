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
 * A Component helper.
 *
 * @package     Redcore
 * @subpackage  Component
 * @since       1.0
 */
final class RComponentHelper
{
	/**
	 * Get the element name of the components using redcore.
	 *
	 * @return  array  An array of component names (com_redshopb...)
	 */
	public static function getRedcoreComponents()
	{
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(JPATH_ADMINISTRATOR . '/components')
		);

		$components = array();

		/** @var SplFileInfo $fileInfo */
		foreach ($iterator as $fileInfo)
		{
			if ($fileInfo->isFile() && 'xml' === pathinfo($fileInfo->getFilename(), PATHINFO_EXTENSION))
			{
				$content = @file_get_contents($fileInfo->getRealPath());

				if (!is_string($content))
				{
					continue;
				}

				$element = new SimpleXMLElement($content);

				if ('com_redcore' === trim(strtolower($element->name)))
				{
					continue;
				}

				if ($element->xpath('//redcore'))
				{
					$components[] = 'com_' . strstr($fileInfo->getFilename(), '.xml', true);
				}
			}
		}

		return $components;
	}
}
