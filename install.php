<?php
/**
 * @package     Redcore
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$bootstrapPaths = array(
	JPATH_LIBRARIES . '/redcore',
	dirname(__FILE__) . '/libraries/redcore'
);

if ($bootstrapFile = JPath::find($bootstrapPaths, 'bootstrap.php'))
{
	require_once $bootstrapFile;
}

/**
 * Custom installation of redCORE
 *
 * @package     Redcore
 * @subpackage  Install
 * @since       1.0
 */
class Com_RedcoreInstallerScript
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
	 * Function to install redCORE for components
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function installRedcore($parent)
	{
		$installer = $this->getInstaller();
		$manifest  = $this->getManifest($parent);
		$type      = $manifest->attributes()->type;

		if ($type == 'component')
		{
			if ($redcoreNode = $manifest->redcore)
			{
				$redcoreFolder = dirname(__FILE__);

				if (is_dir($redcoreFolder))
				{
					$installer->install($redcoreFolder);
				}

				if (!empty($redcoreFolder))
				{
					$version = $redcoreNode->attributes()->version;

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
					$comParams->set('redcore',
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

				// Discover install
				elseif ($extId = $this->searchExtension($extName, 'template', '-1'))
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
	 * @return  boolean
	 */
	public function postflight($type, $parent)
	{
		// If it's installing redcore as dependency
		if (get_called_class() != 'Com_RedcoreInstallerScript' && $type != 'discover_install')
		{
			$this->installRedcore($parent);
		}

		// Execute the postflight tasks from the manifest
		$this->postFlightFromManifest($type, $parent);

		return true;
	}

	/**
	 * Execute the postflight tasks from the manifest if there is any.
	 *
	 * @param   object  $type    type of change (install, update or discover_install)
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function postFlightFromManifest($type, $parent)
	{
		$manifest = $parent->get('manifest');

		if ($tasks = $manifest->postflight->task)
		{
			/** @var JXMLElement $task */
			foreach ($tasks as $task)
			{
				$attributes = current($task->attributes());
				$taskName = null;

				// No task name
				if (!isset($attributes['name']))
				{
					continue;
				}

				$taskName = $attributes['name'];
				$class = get_called_class();

				// Do we have some parameters ?
				$parameters = array();

				if ($params = $task->parameter)
				{
					foreach ($params as $param)
					{
						$parameters[] = (string) $param;
					}
				}

				$parameters = array_merge(array($type, $parent), $parameters);

				// Call the task with $type and $parent as parameters
				if (method_exists($class, $taskName))
				{
					call_user_func_array(array($class, $taskName), $parameters);
				}
			}
		}
	}

	/**
	 * Delete the menu item of the extension.
	 *
	 * @param   object  $type    Type of change (install, update or discover_install)
	 * @param   object  $parent  Class calling this method
	 * @param   string  $client  The client
	 *
	 * @return  void
	 */
	protected function deleteMenu($type, $parent, $client = null)
	{
		/** @var JXMLElement $manifest */
		$manifest = $parent->get('manifest');
		$attributes = current($manifest->attributes());

		// If it's not a component
		if (!isset($attributes['type']))
		{
			return;
		}

		$type = $attributes['type'];
		$componentName = (string) $manifest->name;

		if (empty($componentName))
		{
			return;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->delete('#__menu')
			->where('type = ' . $db->q($type));

		if ($client)
		{
			$query->where('client_id = ' . $db->q($client));
		}

		$query->where(
			array(
				'title = ' . $db->q($componentName),
				'title = ' . $db->q(strtolower($componentName))
			),
			'OR'
		);

		$db->setQuery($query);
		$db->execute();
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
	 * Prevents uninstalling redcore component if some components using it are still installed.
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 *
	 * @throws  RuntimeException
	 */
	private function preventUninstallRedcore($parent)
	{
		require_once JPATH_LIBRARIES . '/redcore/bootstrap.php';

		// Avoid uninstalling redcore if there is a component using it
		$manifest = $this->getManifest($parent);
		$isRedcore = 'COM_REDCORE' === (string) $manifest->name;

		if ($isRedcore)
		{
			if ($components = RComponentHelper::getRedcoreComponents())
			{
				$app = JFactory::getApplication();

				$message = sprintf(
					"Cannot uninstall redCORE because the following components are using it: <br /> [%s]",
					implode(",<br /> ", $components)
				);

				$app->enqueueMessage($message, 'error');

				$app->redirect('index.php?option=com_installer&view=manage');
			}
		}
	}

	/**
	 * method to uninstall the component
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 *
	 * @throws  RuntimeException
	 */
	public function uninstall($parent)
	{
		$this->preventUninstallRedcore($parent);

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

		if ($nodes = $manifest->libraries->library)
		{
			foreach ($nodes as $node)
			{
				$extName = $node->attributes()->name;
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

		if ($nodes = $manifest->modules->module)
		{
			foreach ($nodes as $node)
			{
				$extName   = $node->attributes()->name;
				$extClient = $node->attributes()->client;
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

		if ($nodes = $manifest->plugins->plugin)
		{
			foreach ($nodes as $node)
			{
				$extName  = $node->attributes()->name;
				$extGroup = $node->attributes()->group;
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

		if ($nodes = $manifest->templates->template)
		{
			foreach ($nodes as $node)
			{
				$extName   = $node->attributes()->name;
				$extClient = $node->attributes()->client;
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
