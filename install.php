<?php
/**
 * @package     RedRad
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Custom installation of redRAD
 *
 * @package     RedRad
 * @subpackage  Install
 * @since       1.0
 */
class Pkg_RedradInstallerScript
{
	/**
	 * Status of the installation
	 *
	 * @var  stdClass
	 */
	public $status = null;

	public $installer = null;

	/**
	 * Get the common JInstaller instance used to install all the extensions
	 *
	 * @return JInstaller The JInstaller object
	 */
	public function getInstaller()
	{
		if (is_null($this->installer))
		{
			$this->installer = new JInstaller;
		}

		return $this->installer;
	}

	/**
	 * Shit happens. Patched function to bypass bug in package uninstaller
	 *
	 * @param   JInstaller  $parent  Parent object
	 *
	 * @return  SimpleXMLElement
	 */
	protected function getManifest($parent)
	{
		$element = strtolower(str_replace('InstallerScript', '', __CLASS__));
		$elementParts = explode('_', $element);

		if (count($elementParts) == 2)
		{
			$extType = $elementParts[0];
			$extName = $elementParts[1];

			if ($extType == 'pkg')
			{
				$rootPath = $parent->getParent()->getPath('extension_root');
				$manifestPath = dirname($rootPath);
				$manifestFile = $manifestPath . '/' . $element . '.xml';

				if (file_exists($manifestFile))
				{
					return JFactory::getXML($manifestFile);
				}
			}
		}

		return $parent->get('manifest');
	}

	/**
	 * Method to install the component
	 *
	 * @param   object  $parent  Class calling this method
	 *
	 * @return  boolean          True on success
	 */
	public function install($parent)
	{
		// Common tasks for install or update
		$this->installOrUpdate($parent);

		return true;
	}

	/**
	 * Method to install the component
	 *
	 * @param   object  $parent  Class calling this method
	 *
	 * @return  boolean          True on success
	 */
	public function installOrUpdate($parent)
	{
		// Install extensions
		$this->installLibraries($parent);
		$this->installMedia($parent);
		$this->installModules($parent);
		$this->installPlugins($parent);
		$this->installTemplates($parent);

		return true;
	}

	/**
	 * Install the package libraries
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	private function installLibraries($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $parent->get('manifest');
		$src       = $parent->getParent()->getPath('source');

		if ($nodes = $manifest->libraries->library)
		{
			foreach ($nodes as $node)
			{
				$extName = $node->attributes()->name;
				$extPath = $src . '/libraries/' . $extName;
				$result  = 0;

				// Standard install
				if (is_dir($extPath))
				{
					$result = $installer->install($extPath);
				}
				elseif ($extId = $this->searchExtension($extName, 'library', '-1'))
				// Discover install
				{
					$result = $installer->discover_install($extId);
				}

				$this->_storeStatus('libraries', array('name' => $extName, 'result' => $result));
			}
		}
	}

	/**
	 * Install the media folder
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	private function installMedia($parent)
	{
		$installer = $this->getInstaller();
		$manifest  = $this->getManifest($parent);
		$src       = $parent->getParent()->getPath('source');

		if ($manifest && $manifest->attributes()->type == 'package')
		{
			$installer->setPath('source', $src);
			$installer->parseMedia($manifest->media);
		}
	}

	/**
	 * Install the package modules
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function installModules($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $parent->get('manifest');
		$src       = $parent->getParent()->getPath('source');

		if ($nodes = $manifest->modules->module)
		{
			foreach ($nodes as $node)
			{
				$extName   = $node->attributes()->name;
				$extClient = $node->attributes()->client;
				$extPath   = $src . '/modules/' . $extClient . '/' . $extName;
				$result    = 0;

				// Standard install
				if (is_dir($extPath))
				{
					$result = $installer->install($extPath);
				}
				elseif ($extId = $this->searchExtension($extName, 'module', '-1'))
				// Discover install
				{
					$result = $installer->discover_install($extId);
				}

				$this->_storeStatus('modules', array('name' => $extName, 'client' => $extClient, 'result' => $result));
			}
		}
	}

	/**
	 * Install the package libraries
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function installPlugins($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $parent->get('manifest');
		$src       = $parent->getParent()->getPath('source');

		if ($nodes = $manifest->plugins->plugin)
		{
			foreach ($nodes as $node)
			{
				$extName  = $node->attributes()->name;
				$extGroup = $node->attributes()->group;
				$extPath  = $src . '/plugins/' . $extGroup . '/' . $extName;
				$result   = 0;

				// Standard install
				if (is_dir($extPath))
				{
					$result = $installer->install($extPath);
				}
				elseif ($extId = $this->searchExtension($extName, 'plugin', '-1', $extGroup))
				// Discover install
				{
					$result = $installer->discover_install($extId);
				}

				// Store the result to show install summary later
				$this->_storeStatus('plugins', array('name' => $extName, 'group' => $extGroup, 'result' => $result));

				// Enable the installed plugin
				if ($result)
				{
					$db = JFactory::getDBO();
					$query = $db->getQuery(true);
					$query->update($db->quoteName("#__extensions"));
					$query->set("enabled=1");
					$query->set('state = 0');
					$query->where("type='plugin'");
					$query->where("element=" . $db->quote($extName));
					$query->where("folder=" . $db->quote($extGroup));
					$db->setQuery($query);
					$db->query();
				}
			}
		}
	}

	/**
	 * Function to install redRAD for components
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function installRedrad($parent)
	{
		$installer = $this->getInstaller();
		$manifest  = $this->getManifest($parent);
		$src       = $parent->getParent()->getPath('source');
		$type      = $manifest->attributes()->type;

		if ($type == 'component')
		{
			if ($redradNode = $manifest->redrad)
			{
				$redradFolder = dirname(__FILE__);

				if (is_dir($redradFolder))
				{
					$installer->install($redradFolder);
				}

				if (!empty($redradFolder))
				{
					$version = $redradNode->attributes()->version;

					$class = get_called_class();
					$option = strtolower(strstr($class, 'Installer', true));

					$db = JFactory::getDBO();
					$query = $db->getQuery(true)
						->select('params')
						->from($db->quoteName("#__extensions"))
						->where("type=" . $db->quote($type))
						->where("element=" . $db->quote($option));

					$db->setQuery($query);

					$comParams = new JRegistry($db->loadResult());

					$shit = array('version' => (string) $version);
					$comParams->set('redrad',
						array(
							'version' => (string) $version)
					);

					$query = $db->getQuery(true);
					$query->update($db->quoteName("#__extensions"));
					$query->set('params = ' . $db->quote($comParams->toString()));
					$query->where("type=" . $db->quote($type));
					$query->where("element=" . $db->quote($option));
					$db->setQuery($query);
					$db->query();
				}
			}
		}
	}

	/**
	 * Install the package templates
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	private function installTemplates($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $parent->get('manifest');
		$src       = $parent->getParent()->getPath('source');

		if ($nodes = $manifest->templates->template)
		{
			foreach ($nodes as $node)
			{
				$extName   = $node->attributes()->name;
				$extClient = $node->attributes()->client;
				$extPath   = $src . '/templates/' . $extClient . '/' . $extName;
				$result    = 0;

				// Standard install
				if (is_dir($extPath))
				{
					$result = $installer->install($extPath);
				}
				elseif ($extId = $this->searchExtension($extName, 'template', '-1'))
				// Discover install
				{
					$result = $installer->discover_install($extId);
				}

				$this->_storeStatus('templates', array('name' => $extName, 'client' => $extClient, 'result' => $result));
			}
		}
	}

	/**
	 * Method to run after an install/update/uninstall method
	 *
	 * @param   object  $type    type of change (install, update or discover_install)
	 * @param   object  $parent  class calling this method
	 *
	 * @return void
	 */
	public function postflight($type, $parent)
	{
		// If it's installing redrad as dependency
		if (get_called_class() != 'Pkg_RedradInstallerScript')
		{
			$this->installRedrad($parent);
		}

		return true;
	}

	/**
	 * Search a extension in the database
	 *
	 * @param   string  $element  Extension technical name/alias
	 * @param   string  $type     Type of extension (component, file, language, library, module, plugin)
	 * @param   string  $state    State of the searched extension
	 * @param   string  $folder   Folder name used mainly in plugins
	 *
	 * @return  integer           Extension identifier
	 */
	protected function searchExtension($element, $type, $state = null, $folder = null)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select('extension_id')
			->from($db->quoteName("#__extensions"))
			->where("type = " . $db->quote($type))
			->where("element = " . $db->quote($element));

		if (!is_null($state))
		{
			$query->where("state = " . (int) $state);
		}

		if (!is_null($folder))
		{
			$query->where("folder = " . $db->quote($folder));
		}

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * method to update the component
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return void
	 */
	public function update($parent)
	{
		// Common tasks for install or update
		$this->installOrUpdate($parent);
	}

	/**
	 * method to uninstall the component
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return void
	 */
	public function uninstall($parent)
	{
		// Uninstall extensions
		$this->uninstallLibraries($parent);
		$this->uninstallMedia($parent);
		$this->uninstallModules($parent);
		$this->uninstallPlugins($parent);
		$this->uninstallTemplates($parent);
	}

	/**
	 * Uninstall the package libraries
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function uninstallLibraries($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $this->getManifest($parent);
		$src       = $parent->getParent()->getPath('source');

		if ($nodes = $manifest->libraries->library)
		{
			foreach ($nodes as $node)
			{
				$extName = $node->attributes()->name;
				$extPath = $src . '/libraries/' . $extName;
				$result  = 0;

				if ($extId = $this->searchExtension($extName, 'library', 0))
				{
					$result = $installer->uninstall('library', $extId);
				}

				// Store the result to show install summary later
				$this->_storeStatus('libraries', array('name' => $extName, 'result' => $result));
			}
		}
	}

	/**
	 * Uninstall the media folder
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function uninstallMedia($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $this->getManifest($parent);

		if ($manifest && $manifest->attributes()->type == 'package')
		{
			$installer->removeFiles($manifest->media);
		}
	}

	/**
	 * Uninstall the package modules
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function uninstallModules($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $this->getManifest($parent);
		$src       = $parent->getParent()->getPath('source');

		if ($nodes = $manifest->modules->module)
		{
			foreach ($nodes as $node)
			{
				$extName   = $node->attributes()->name;
				$extClient = $node->attributes()->client;
				$extPath   = $src . '/modules/' . $extClient . '/' . $extName;
				$result    = 0;

				if ($extId = $this->searchExtension($extName, 'module', 0))
				{
					$result = $installer->uninstall('module', $extId);
				}

				// Store the result to show install summary later
				$this->_storeStatus('modules', array('name' => $extName, 'client' => $extClient, 'result' => $result));
			}
		}
	}

	/**
	 * Uninstall the package plugins
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function uninstallPlugins($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $this->getManifest($parent);
		$src       = $parent->getParent()->getPath('source');

		if ($nodes = $manifest->plugins->plugin)
		{
			foreach ($nodes as $node)
			{
				$extName  = $node->attributes()->name;
				$extGroup = $node->attributes()->group;
				$extPath  = $src . '/plugins/' . $extGroup . '/' . $extName;
				$result   = 0;

				if ($extId = $this->searchExtension($extName, 'plugin', 0, $extGroup))
				{
					$result = $installer->uninstall('plugin', $extId);
				}

				// Store the result to show install summary later
				$this->_storeStatus('plugins', array('name' => $extName, 'group' => $extGroup, 'result' => $result));
			}
		}
	}

	/**
	 * Uninstall the package templates
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function uninstallTemplates($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $this->getManifest($parent);
		$src       = $parent->getParent()->getPath('source');

		if ($nodes = $manifest->templates->template)
		{
			foreach ($nodes as $node)
			{
				$extName   = $node->attributes()->name;
				$extClient = $node->attributes()->client;
				$extPath   = $src . '/templates/' . $extClient . '/' . $extName;
				$result    = 0;

				if ($extId = $this->searchExtension($extName, 'template', 0))
				{
					$result = $installer->uninstall('template', $extId);
				}

				// Store the result to show install summary later
				$this->_storeStatus('templates', array('name' => $extName, 'client' => $extClient, 'result' => $result));
			}
		}
	}

	/**
	 * Store the result of trying to install an extension
	 *
	 * @param   string  $type    Type of extension (libraries, modules, plugins)
	 * @param   array   $status  The status info
	 *
	 * @return void
	 */
	private function _storeStatus($type, $status)
	{
		// Initialise status object if needed
		if (is_null($this->status))
		{
			$this->status = new stdClass;
		}

		// Initialise current status type if needed
		if (!isset($this->status->{$type}))
		{
			$this->status->{$type} = array();
		}

		// Insert the status
		array_push($this->status->{$type}, $status);
	}
}
