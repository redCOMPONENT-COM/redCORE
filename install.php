<?php
/**
 * @package     Redcore
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

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

	/**
	 * Show component info after install / update
	 *
	 * @var  boolean
	 */
	public $showComponentInfo = true;

	/**
	 * Installer instance
	 *
	 * @var  JInstaller
	 */
	public $installer = null;

	/**
	 * Extension element name
	 *
	 * @var  string
	 */
	protected $extensionElement = '';

	/**
	 * Old version according to manifest
	 *
	 * @var  string
	 */
	protected $oldVersion = '0.0.0';

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
	 * Method to run before an install/update/uninstall method
	 *
	 * @param   object      $type    type of change (install, update or discover_install)
	 * @param   JInstaller  $parent  class calling this method
	 *
	 * @return bool
	 */
	public function preflight($type, $parent)
	{
		$this->installRedcore($type, $parent);
		$this->loadRedcoreLibrary();
		$this->loadRedcoreLanguage();
		$manifest = $this->getManifest($parent);
		$extensionType = $manifest->attributes()->type;
		$this->extensionElement = $this->getElement($manifest, $parent);

		if ($extensionType == 'component' && in_array($type, array('install', 'update', 'discover_install')))
		{
			// Update SQL pre-processing
			if ($type == 'update')
			{
				// Reads current (old) version from manifest
				$db = JFactory::getDbo();
				$version = $db->setQuery(
					$db->getQuery(true)
						->select($db->qn('s.version_id'))
						->from($db->qn('#__schemas', 's'))
						->join('inner', $db->qn('#__extensions', 'e') . ' ON ' . $db->qn('e.extension_id') . ' = ' . $db->qn('s.extension_id'))
						->where('e.element = ' . $db->q($this->extensionElement))
				)
					->loadResult();

				if (!empty($version))
				{
					$this->oldVersion = (string) $version;
				}

				if (!$this->preprocessUpdates($parent))
				{
					return false;
				}
			}

			// In case we are installing redcore
			if (get_called_class() == 'Com_RedcoreInstallerScript')
			{
				if (!$this->checkComponentVersion($this->getRedcoreComponentFolder(), dirname(__FILE__), 'redcore.xml'))
				{
					JFactory::getApplication()->enqueueMessage(
						JText::_('COM_REDCORE_INSTALL_ERROR_OLDER_VERSION'),
						'error'
					);

					return false;
				}

				$searchPaths = array(
					// Discover install
					JPATH_LIBRARIES . '/redcore/component',
					// Install
					dirname(__FILE__) . '/redCORE/libraries/redcore/component',
					dirname(__FILE__) . '/libraries/redcore/component',
				);

				if ($componentHelper = JPath::find($searchPaths, 'helper.php'))
				{
					require_once $componentHelper;
				}
			}

			$requirements = array();

			if (method_exists('RComponentHelper', 'checkComponentRequirements'))
			{
				$requirements = RComponentHelper::checkComponentRequirements($manifest->requirements);
			}

			if (!empty($requirements))
			{
				foreach ($requirements as $key => $requirement)
				{
					foreach ($requirement as $checked)
					{
						if (!$checked['status'])
						{
							// In case redCORE cannot be installed we do not have the language string
							if (get_called_class() == 'Com_RedcoreInstallerScript')
							{
								$this->loadRedcoreLanguage(dirname(__FILE__));
								$checked['name'] = JText::_($checked['name']);
							}

							$messageKey = $key == 'extensions' ? 'COM_REDCORE_INSTALL_ERROR_REQUIREMENTS_EXTENSIONS' : 'COM_REDCORE_INSTALL_ERROR_REQUIREMENTS';

							JFactory::getApplication()->enqueueMessage(
								JText::sprintf($messageKey, $checked['name'], $checked['required'], $checked['current']),
								'error'
							);

							return false;
						}
					}
				}
			}
		}

		return true;
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
		$this->loadRedcoreLibrary();
		$this->installMedia($parent);
		$this->installWebservices($parent);
		$this->installModules($parent);
		$this->installPlugins($parent);
		$this->installTemplates($parent);

		return true;
	}

	/**
	 * Method to process SQL updates previous to the install process
	 *
	 * @param   object  $parent  Class calling this method
	 *
	 * @return  boolean          True on success
	 */
	public function preprocessUpdates($parent)
	{
		$manifest  = $parent->get('manifest');

		if (isset($manifest->update))
		{
			if (isset($manifest->update->attributes()->folder))
			{
				$path = $manifest->update->attributes()->folder;

				if (isset($manifest->update->pre) && isset($manifest->update->pre->schemas))
				{
					$schemapaths = $manifest->update->pre->schemas->children();

					if (count($schemapaths))
					{
						$sourcePath = $parent->getParent()->getPath('source');

						// If it just upgraded redCORE to a newer version using RFactory for database, it forces using the redCORE database drivers
						if (substr(get_class(JFactory::$database), 0, 1) == 'J' && $this->extensionElement != 'com_redcore')
						{
							RFactory::$database = null;
							JFactory::$database = null;
							JFactory::$database = RFactory::getDbo();
						}

						$db = JFactory::getDbo();

						$dbDriver = strtolower($db->name);
						$schemapath = '';

						if ($dbDriver == 'mysqli')
						{
							$dbDriver = 'mysql';
						}

						foreach ($schemapaths as $entry)
						{
							if (isset($entry->attributes()->type))
							{
								$uDriver = strtolower($entry->attributes()->type);

								if ($uDriver == 'mysqli')
								{
									$uDriver = 'mysql';
								}

								if ($uDriver == $dbDriver)
								{
									$schemapath = (string) $entry;
									break;
								}
							}
						}

						if ($schemapath != '')
						{
							$files = str_replace('.sql', '', JFolder::files($sourcePath . '/' . $path . '/' . $schemapath, '\.sql$'));
							usort($files, 'version_compare');

							if (count($files))
							{
								foreach ($files as $file)
								{
									if (version_compare($file, $this->oldVersion) > 0)
									{
										$buffer = file_get_contents($sourcePath . '/' . $path . '/' . $schemapath . '/' . $file . '.sql');
										$queries = RHelperDatabase::splitSQL($buffer);

										if (count($queries))
										{
											foreach ($queries as $query)
											{
												if ($query != '' && $query{0} != '#')
												{
													$db->setQuery($query);

													if (!$db->execute(true))
													{
														JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)), JLog::WARNING, 'jerror');

														return false;
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * Method to process PHP update files defined in the manifest file
	 *
	 * @param   object  $parent  Class calling this method
	 *
	 * @return  boolean          True on success
	 */
	public function phpUpdates($parent)
	{
		$manifest  = $parent->get('manifest');

		if (isset($manifest->update))
		{
			if (isset($manifest->update->php) && isset($manifest->update->php->path))
			{
				$updatePath = (string) $manifest->update->php->path;

				if ($updatePath != '')
				{
					$sourcePath = JPATH_ADMINISTRATOR . '/components/' . $this->extensionElement;
					$db = JFactory::getDbo();

					$files = str_replace('.php', '', JFolder::files($sourcePath . '/' . $updatePath, '\.php$'));
					usort($files, 'version_compare');

					if (count($files))
					{
						foreach ($files as $file)
						{
							if (version_compare($file, $this->oldVersion) > 0)
							{
								if (!$this->processPHPUpdateFile($sourcePath . '/' . $updatePath . '/' . $file . '.php', $file))
								{
									return false;
								}
							}
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * Ensures first character of every item in an array to be uppercase
	 *
	 * @param   string  &$item  Array item
	 * @param   string  $key    Array item key
	 *
	 * @return  void
	 */
	protected static function ucFirstArray(&$item, $key)
	{
		$item = ucfirst($item);
	}

	/**
	 * Method to process a single PHP update file
	 *
	 * @param   string  $file     File to process
	 * @param   string  $version  File version
	 *
	 * @return  boolean          True on success
	 */
	public function processPHPUpdateFile($file, $version)
	{
		include $file;
		$extensionElementParts = explode('_', $this->extensionElement);
		array_walk($extensionElementParts, 'self::ucFirstArray');

		$class = implode('_', $extensionElementParts) . 'UpdateScript_' . str_replace('.', '_', $version);

		if (class_exists($class))
		{
			$upgrader = new $class;

			if (method_exists($upgrader, 'execute'))
			{
				if (!$upgrader->execute())
				{
					return false;
				}
			}
		}

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
				$disabled = !empty($node->attributes()->disabled) ? true : false;
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
				if ($result && !$disabled)
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
					$db->execute();
				}
			}
		}
	}

	/**
	 * Install the package translations
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function installTranslations($parent)
	{
		// Required objects
		$manifest  = $parent->get('manifest');

		if (method_exists('RTranslationTable', 'batchContentElements'))
		{
			if ($nodes = $manifest->translations->translation)
			{
				foreach ($nodes as $node)
				{
					$extName   = (string) $node->attributes()->name;

					try
					{
						RTranslationTable::batchContentElements($extName, 'install', false);
					}
					catch (Exception $e)
					{
						// We are already setting message queue so we don't need to set it here as well
					}

					$this->_storeStatus('translations', array('name' => $extName, 'result' => true));
				}
			}
		}
	}

	/**
	 * Function to install redCORE for components
	 *
	 * @param   object  $type    type of change (install, update or discover_install)
	 * @param   object  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function installRedcore($type, $parent)
	{
		// If it's installing redcore as dependency
		if (get_called_class() != 'Com_RedcoreInstallerScript' && $type != 'discover_install')
		{
			$manifest  = $this->getManifest($parent);
			$type      = $manifest->attributes()->type;

			if ($type == 'component')
			{
				if ($manifest->redcore)
				{
					$installer = $this->getInstaller();
					$redcoreFolder = dirname(__FILE__);
					$redcoreComponentFolder = $this->getRedcoreComponentFolder();

					if (is_dir($redcoreFolder) && JPath::clean($redcoreFolder) != JPath::clean($redcoreComponentFolder))
					{
						$install = $this->checkComponentVersion($redcoreComponentFolder, $redcoreFolder, 'redcore.xml');

						if ($install)
						{
							$installer->install($redcoreFolder);
							$this->loadRedcoreLibrary();
						}
					}
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
	protected function postInstallRedcore($parent)
	{
		$manifest  = $this->getManifest($parent);
		$type      = $manifest->attributes()->type;

		if ($type == 'component')
		{
			if ($redcoreNode = $manifest->redcore)
			{
				$redcoreFolder = dirname(__FILE__);

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
					$db->execute();
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
	 * Method to parse through a webservices element of the installation manifest and take appropriate
	 * action.
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  boolean     True on success
	 *
	 * @since   1.3
	 */
	public function installWebservices($parent)
	{
		$installer = $this->getInstaller();
		$manifest  = $this->getManifest($parent);
		$src       = $parent->getParent()->getPath('source');

		if (!$manifest)
		{
			return false;
		}

		$installer->setPath('source', $src);
		$element = $manifest->webservices;

		if (!$element || !count($element->children()))
		{
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return false;
		}

		// Here we set the folder we are going to copy the files to.
		$destination = JPath::clean(RApiHalHelper::getWebservicesPath());

		// Here we set the folder we are going to copy the files from.
		$folder = (string) $element->attributes()->folder;

		if ($folder && file_exists($src . '/' . $folder))
		{
			$source = $src . '/' . $folder;
		}
		else
		{
			$source = $src;
		}

		$copyFiles = $this->prepareFilesForCopy($element, $source, $destination);

		return $installer->copyFiles($copyFiles, true);
	}

	/**
	 * Method to parse through a xml element of the installation manifest and take appropriate action.
	 *
	 * @param   SimpleXMLElement  $element      Element to iterate
	 * @param   string            $source       Source location of the files
	 * @param   string            $destination  Destination location of the files
	 *
	 * @return  array
	 *
	 * @since   1.4
	 */
	public function prepareFilesForCopy($element, $source, $destination)
	{
		$copyFiles = array();

		// Process each file in the $files array (children of $tagName).
		foreach ($element->children() as $file)
		{
			$path = array();
			$path['src'] = $source . '/' . $file;
			$path['dest'] = $destination . '/' . $file;

			// Is this path a file or folder?
			$path['type'] = ($file->getName() == 'folder') ? 'folder' : 'file';

			if (basename($path['dest']) != $path['dest'])
			{
				$newdir = dirname($path['dest']);

				if (!JFolder::create($newdir))
				{
					JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_CREATE_DIRECTORY', $newdir), JLog::WARNING, 'jerror');

					return false;
				}
			}

			// Add the file to the copyfiles array
			$copyFiles[] = $path;
		}

		return $copyFiles;
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
			$this->postInstallRedcore($parent);
		}

		// Execute the postflight tasks from the manifest
		$this->postFlightFromManifest($type, $parent);

		$this->installTranslations($parent);

		if (in_array($type, array('install', 'update', 'discover_install')))
		{
			/** @var JXMLElement $manifest */
			$manifest = $parent->get('manifest');
			$attributes = current($manifest->attributes());

			// If it's a component
			if (isset($attributes['type']) && (string) $attributes['type'] == 'component')
			{
				$this->loadRedcoreLanguage();
				$this->displayComponentInfo($parent);
			}
		}

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
		// Process PHP update files
		$this->phpUpdates($parent);

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
		$this->loadRedcoreLibrary();

		// Avoid uninstalling redcore if there is a component using it
		$manifest = $this->getManifest($parent);
		$isRedcore = 'COM_REDCORE' === (string) $manifest->name;

		if ($isRedcore)
		{
			if ((method_exists('RComponentHelper', 'getRedcoreComponents')) && $components = RComponentHelper::getRedcoreComponents())
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
		$this->uninstallTranslations();
		$this->uninstallMedia($parent);
		$this->uninstallWebservices($parent);
		$this->uninstallModules($parent);
		$this->uninstallPlugins($parent);
		$this->uninstallTemplates($parent);
		$this->uninstallLibraries($parent);
	}

	/**
	 * Uninstall all Translation tables from database
	 *
	 * @return  void
	 */
	protected function uninstallTranslations()
	{
		$class = get_called_class();
		$extensionOption = strtolower(strstr($class, 'Installer', true));

		$translationTables = RTranslationHelper::getInstalledTranslationTables();

		if (!empty($translationTables))
		{
			$db = JFactory::getDbo();

			foreach ($translationTables as $translationTable => $translationTableParams)
			{
				if ((method_exists('RTranslationTable', 'getTranslationsTableName'))
					&& ($class == 'Com_RedcoreInstallerScript' || $extensionOption == $translationTableParams->option))
				{
					$newTable = RTranslationTable::getTranslationsTableName($translationTable, '');

					try
					{
						RTranslationTable::removeExistingConstraintKeys($translationTable);
						$db->dropTable($newTable);
					}
					catch (Exception $e)
					{
						JFactory::getApplication()->enqueueMessage(JText::sprintf('LIB_REDCORE_TRANSLATIONS_DELETE_ERROR', $e->getMessage()), 'error');
					}
				}
			}
		}
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
	 * Uninstall the webservices
	 *
	 * @param   object  $parent  class calling this method
	 *
	 * @return  boolean
	 */
	protected function uninstallWebservices($parent)
	{
		// Required objects
		$manifest  = $this->getManifest($parent);

		if (!$manifest)
		{
			return false;
		}

		// We will use webservices removal function to remove webservice files
		$element = $manifest->webservices;

		if (!$element || !count($element->children()))
		{
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return true;
		}

		$returnValue = true;

		// Get the array of file nodes to process
		$files = $element->children();
		$source = RApiHalHelper::getWebservicesPath();

		// Process each file in the $files array (children of $tagName).
		foreach ($files as $file)
		{
			$path = $source . '/' . $file;

			// Actually delete the files/folders

			if (is_dir($path))
			{
				$val = JFolder::delete($path);
			}
			else
			{
				$val = JFile::delete($path);
			}

			if ($val === false)
			{
				JLog::add(JText::sprintf('LIB_REDCORE_INSTALLER_ERROR_FAILED_TO_DELETE', $path), JLog::WARNING, 'jerror', JLog::WARNING, 'jerror');
				$returnValue = false;
			}
		}

		return $returnValue;
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

	/**
	 * Method to display component info
	 *
	 * @param   object  $parent   Class calling this method
	 * @param   string  $message  Message to apply to the Component info layout
	 *
	 * @return  void
	 */
	public function displayComponentInfo($parent, $message = '')
	{
		$this->loadRedcoreLibrary();

		if ($this->showComponentInfo)
		{
			if (method_exists('RComponentHelper', 'displayComponentInfo'))
			{
				$manifest  = $this->getManifest($parent);
				echo RComponentHelper::displayComponentInfo((string) $manifest->name, $message);
			}
		}
	}

	/**
	 * Load redCORE component language file
	 *
	 * @param   string  $path  Path to the language folder
	 *
	 * @return  void
	 */
	public function loadRedcoreLanguage($path = JPATH_ADMINISTRATOR)
	{
		// Load common and local language files.
		$lang = JFactory::getLanguage();

		// Load language file
		$lang->load('com_redcore', $path, null, true, true)
		|| $lang->load('com_redcore', $path . "/components/com_redcore", null, true, true)
		|| $lang->load('com_redcore', $path . "/component/admin", null, true, true);
	}

	/**
	 * Load redCORE library
	 *
	 * @return  void
	 */
	public function loadRedcoreLibrary()
	{
		$redcoreLoader = JPATH_LIBRARIES . '/redcore/bootstrap.php';

		if (file_exists($redcoreLoader))
		{
			require_once $redcoreLoader;

			RBootstrap::bootstrap(false);
		}
	}

	/**
	 * Checks version of the extension and returns
	 *
	 * @param   string  $original  Original path
	 * @param   string  $source    Install path
	 * @param   string  $xmlFile   Component filename
	 *
	 * @return  boolean  Returns true if current version is lower or equal or if that extension do not exist
	 */
	public function checkComponentVersion($original, $source, $xmlFile)
	{
		if (is_dir($original))
		{
			$source = $source . '/' . $xmlFile;
			$sourceXml = JFactory::getXML($source);
			$original = $original . '/' . $xmlFile;
			$originalXml = JFactory::getXML($original);

			if (version_compare((string) $sourceXml->version, (string) $originalXml->version, '<'))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Gets or generates the element name (using the manifest)
	 *
	 * @param   SimpleXMLElement  $manifest  Extension manifest
	 * @param   object            $parent    Parent adapter
	 *
	 * @return  string  Element
	 */
	public function getElement($manifest, $parent)
	{
		if (method_exists($parent, 'getElement'))
		{
			return $parent->getElement();
		}

		if (isset($manifest->element))
		{
			$element = (string) $manifest->element;
		}
		else
		{
			$element = (string) $manifest->name;
		}

		// Filter the name for illegal characters
		return strtolower(JFilterInput::getInstance()->clean($element, 'cmd'));
	}

	/**
	 * Gets the path of redCORE component
	 *
	 * @return  string
	 */
	public function getRedcoreComponentFolder()
	{
		return JPATH_ADMINISTRATOR . '/components/com_redcore';
	}
}
