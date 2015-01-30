<?php
/**
 * @package     MVC Override.Plugin
 * @subpackage  System.MVCOverride
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Static class to handle loading of libraries.
 *
 * @since  1.4.10
 */
class MVCLoader extends JLoader
{
	protected static $changePrivate = 0;

	protected static $prefix = '';

	protected static $suffix = 'Default';

	protected static $overrideFiles = array();

	protected static $extendsClasses = array();

	/**
	 * Set in static array name class and relate data
	 *
	 * @param   string  $class           Name class
	 * @param   string  $path            Path file
	 * @param   bool    $isOverrideFile  Name class and path for override file
	 * @param   string  $prefix          Prefix for extend files
	 * @param   string  $suffix          Suffix for extend files
	 *
	 * @return void
	 */
	public static function setOverrideFile($class, $path, $isOverrideFile = false, $prefix = null, $suffix = null)
	{
		$object = new stdClass;
		$object->path = $path;
		$object->isOverride = $isOverrideFile;
		$object->prefix = $prefix;
		$object->suffix = $suffix;

		if ($isOverrideFile)
		{
			$class = $prefix . $class . $suffix;
		}

		// Sanitize class name.
		$class = strtolower($class);
		self::$overrideFiles[$class] = $object;
	}

	/**
	 * Method to setup the autoloaders for the Joomla Platform.
	 *
	 * @param   int     $changePrivate  Flag change values and functions in extends call to private instead protected
	 * @param   string  $prefix         Prefix for extend files
	 * @param   string  $suffix         Suffix for extend files
	 *
	 * @return  void
	 */
	public static function setupOverrideLoader($changePrivate = 0, $prefix = '', $suffix = 'Default')
	{
		self::$changePrivate = $changePrivate;
		self::$prefix = $prefix;
		self::$suffix = $suffix;

		// Register the prefix autoloader.
		spl_autoload_register(array('MVCLoader', '_override_load'), false, true);
	}

	/**
	 * Autoload function for override files
	 *
	 * @param   string  $class  Name class search
	 *
	 * @return bool|mixed
	 */
	public static function _override_load($class)
	{
		// Sanitize class name.
		$lowerClass = strtolower($class);

		// If the class already exists do nothing.
		if (class_exists($lowerClass, false))
		{
			return true;
		}

		// If the class is registered include the file.
		if (isset(self::$extendsClasses[$lowerClass]))
		{
			if (self::loadOverrideFile(self::$extendsClasses[$lowerClass], self::$prefix, self::$suffix))
			{
				return true;
			}
		}

		// If the class is registered include the file.
		if (isset(self::$classes[$lowerClass]))
		{
			if (self::checkOverride(self::$classes[$lowerClass], $lowerClass))
			{
				return true;
			}
		}

		foreach (self::$prefixes as $prefix => $lookup)
		{
			$chr = strlen($prefix) < strlen($class) ? $class[strlen($prefix)] : 0;

			if (strpos($class, $prefix) === 0 && ($chr === strtoupper($chr)))
			{
				if (self::_load(substr($class, strlen($prefix)), $lookup, $prefix))
				{
					return true;
				}
			}
		}

		if (isset(self::$overrideFiles[$lowerClass]))
		{
			$currentData = self::$overrideFiles[$lowerClass];

			if (isset($currentData->isOverride) && $currentData->isOverride)
			{
				if (self::loadOverrideFile($currentData->path, $currentData->prefix, $currentData->suffix))
				{
					return true;
				}
			}
			else
			{
				return include $currentData->path;
			}
		}

		return false;
	}

	/**
	 * Load a class based on name and lookup array.
	 *
	 * @param   string  $class   The class to be loaded (wihtout prefix).
	 * @param   array   $lookup  The array of base paths to use for finding the class file.
	 * @param   string  $prefix  Prefix part extension
	 *
	 * @return  boolean  True if the class was loaded, false otherwise.
	 */
	private static function _load($class, $lookup, $prefix)
	{
		// Split the class name into parts separated by camelCase.
		$parts = preg_split('/(?<=[a-z0-9])(?=[A-Z])/x', $class);

		// If there is only one part we want to duplicate that part for generating the path.
		$parts = (count($parts) === 1) ? array($parts[0], $parts[0]) : $parts;

		foreach ($lookup as $base)
		{
			// Generate the path based on the class name parts.
			$path = $base . '/' . implode('/', array_map('strtolower', $parts)) . '.php';

			// Load the file if it exists.
			if (file_exists($path))
			{
				if (self::checkOverride($path, $prefix . $class))
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Check exists file in override folder with the same path and class name
	 *
	 * @param   string  $oldPath  Real file path
	 * @param   string  $class    Name class
	 *
	 * @return bool|mixed
	 */
	protected static function checkOverride($oldPath, $class)
	{
		$newPath = substr($oldPath, strlen(JPATH_SITE) + 1);

		if ($filePath = JPath::find(MVCOverrideHelperCodepool::addCodePath(null, true), $newPath))
		{
			// Set in autoload path for extends file
			$fullNameClass = strtolower(self::$prefix . $class . self::$suffix);
			self::$extendsClasses[$fullNameClass] = $oldPath;

			return include $filePath;
		}

		return false;
	}

	/**
	 * Load Override File
	 *
	 * @param   string  $oldPath  Path file for override and load
	 * @param   string  $prefix   Prefix for extend files
	 * @param   string  $suffix   Suffix for extend files
	 *
	 * @return bool
	 */
	protected static function loadOverrideFile($oldPath, $prefix, $suffix)
	{
		$bufferContent = MVCOverrideHelperOverride::createDefaultClass($oldPath, $prefix, $suffix);

		// Change private methods to protected methods
		if (self::$changePrivate)
		{
			$bufferContent = preg_replace(
				'/private *function/i',
				'protected function',
				$bufferContent
			);
		}

		MVCOverrideHelperOverride::load($bufferContent);

		return true;
	}
}
