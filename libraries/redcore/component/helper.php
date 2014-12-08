<?php
/**
 * @package     Redcore
 * @subpackage  Component
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
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
		$componentPath = JPATH_ADMINISTRATOR . '/components';
		$folders = JFolder::folders($componentPath);

		$components = array();

		foreach ($folders as $folder)
		{
			$componentFolderPath = $componentPath . '/' . $folder;
			$folderFiles = JFolder::files($componentFolderPath, '.xml');

			foreach ($folderFiles as $folderFile)
			{
				$componentXmlPath = $componentFolderPath . '/' . $folderFile;

				try
				{
					$content = @file_get_contents($componentXmlPath);

					if (!is_string($content))
					{
						continue;
					}

					$element = new SimpleXMLElement($content);

					if (!isset($element->name) || 'com_redcore' === trim(strtolower($element->name)))
					{
						continue;
					}

					if ($element->xpath('//redcore'))
					{
						$components[] = 'com_' . strstr($folderFile, '.xml', true);
					}
				}
				catch (Exception $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage() . ': ' . $folder . '/' . $folderFile, 'error');
				}
			}
		}

		return $components;
	}

	/**
	 * Get XML manifest file of the component
	 *
	 * @param   string  $extensionName  Name of the extension you want to load Manifest file
	 *
	 * @return  SimpleXMLElement  Manifest file in XML format
	 */
	public static function getComponentManifestFile($extensionName = 'com_redcore')
	{
		$xmlComponentName = strtolower(substr($extensionName, 4));
		$componentXml = JPATH_ADMINISTRATOR . '/components/' . $extensionName . '/' . $xmlComponentName . '.xml';
		$manifestFile = false;

		if (file_exists($componentXml))
		{
			$content = @file_get_contents($componentXml);

			if (!is_string($content))
			{
				return false;
			}

			$manifestFile = new SimpleXMLElement($content);

			$manifestFile->xmlComponentName = $xmlComponentName;
		}

		return $manifestFile;
	}

	/**
	 * Check Component Requirements against known application versions and check for installed libraries
	 *
	 * @param   object  $requirements  List of requirements to check
	 *
	 * @return  array  List of requirements checked for the correct version
	 */
	public static function checkComponentRequirements($requirements)
	{
		if (empty($requirements))
		{
			$requirements = new stdClass;
		}

		$checked = array();
		$redCoreManifest = self::getComponentManifestFile();

		$phpRequired = !empty($requirements->php) ? $requirements->php : $redCoreManifest->requirements->php;
		$phpRequired = !empty($phpRequired) ? (string) $phpRequired : '5.3.0';

		$phpVersion = phpversion();
		$checked['applications'][] = array(
			'name'      => JText::_('COM_REDCORE_CONFIG_PHP_VERSION'),
			'current'   => $phpVersion,
			'required'  => $phpRequired,
			'status'    => version_compare($phpRequired, $phpVersion, '<=')
		);

		$mySqlRequired = !empty($requirements->mysql) ? (string) $requirements->mysql : '';

		if (!empty($mySqlRequired))
		{
			$db = JFactory::getDbo();
			$dbVersion  = $db->getVersion();

			if (!in_array($db->name, array('mysql', 'mysqli')))
			{
				$status = false;
			}
			else
			{
				$status = version_compare($mySqlRequired, $dbVersion, '<=');
			}

			$checked['applications'][] = array(
				'name'      => JText::_('COM_REDCORE_CONFIG_MYSQL_VERSION'),
				'current'   => $dbVersion,
				'required'  => $mySqlRequired,
				'status'    => $status
			);
		}

		if (!empty($requirements->extensions))
		{
			foreach ($requirements->extensions->extension as $extension)
			{
				$checked['extensions'][] = array(
					'name'      => $extension,
					'status'    => extension_loaded($extension)
				);
			}
		}

		return $checked;
	}

	/**
	 * Check Component Requirements against known application versions and check for installed libraries
	 *
	 * @param   string  $option   List of requirements to check
	 * @param   string  $message  Custom message for display
	 *
	 * @return  string  Component info Layout
	 */
	public static function displayComponentInfo($option, $message = '')
	{
		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_redcore/models', 'RedcoreModel');
		$option = strtolower($option);
		/** @var RedcoreModelConfig $modelConfig */
		$modelConfig = RModelAdmin::getInstance('Config', 'RedcoreModel', array('ignore_request' => true));

		$component = $modelConfig->getComponent($option);

		$loadInstallModules = array('%' . $component->xml->xmlComponentName . '%');
		$loadInstallPlugins = array();
		$loadInstallPlugins[(string) $component->xml->xmlComponentName] = '%' . $component->xml->xmlComponentName . '%';

		if ($component->xml->modules)
		{
			foreach ($component->xml->modules->module as $module)
			{
				$loadInstallModules[] = (string) $module['name'];
			}
		}

		if ($component->xml->plugins)
		{
			foreach ($component->xml->plugins->plugin as $plugin)
			{
				$loadInstallPlugins[(string) $plugin['group']] = (string) $plugin['name'];
			}
		}

		$modules = $modelConfig->getInstalledExtensions('module', $loadInstallModules);
		$plugins = $modelConfig->getInstalledExtensions('plugin', $loadInstallPlugins, $component->xml->xmlComponentName);
		$requirements = self::checkComponentRequirements($component->xml->requirements);

		return RLayoutHelper::render(
			'component.extensioninfo',
			array(
				'xml' => $component->xml,
				'requirements' => $requirements,
				'modules' => $modules,
				'plugins' => $plugins,
				'message' => $message,
			)
		);
	}
}
