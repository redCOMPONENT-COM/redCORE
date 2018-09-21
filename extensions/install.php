<?php
/**
 * @package     Redcore
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

use Joomla\Registry\Registry;

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
	public $status;

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
	public $installer;

	/**
	 * Extension element name
	 *
	 * @var  string
	 */
	protected $extensionElement = '';

	/**
	 * Manifest of the extension being processed
	 *
	 * @var  SimpleXMLElement
	 */
	protected $manifest;

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
		if (null === $this->installer)
		{
			$this->installer = new JInstaller;
		}

		return $this->installer;
	}

	/**
	 * Getter with manifest cache support
	 *
	 * @param   JInstallerAdapter  $parent  Parent object
	 *
	 * @return  SimpleXMLElement
	 */
	protected function getManifest($parent)
	{
		if (null === $this->manifest)
		{
			$this->loadManifest($parent);
		}

		return $this->manifest;
	}

	/**
	 * Method to run before an install/update/uninstall method
	 *
	 * @param   string             $type    Type of change (install, update or discover_install)
	 * @param   JInstallerAdapter  $parent  Class calling this method
	 *
	 * @return  boolean
	 * @throws  \Exception
	 */
	public function preflight($type, $parent)
	{
		$this->installRedcore($type, $parent);
		$this->loadRedcoreLibrary();
		$this->loadRedcoreLanguage();
		$manifest               = $this->getManifest($parent);
		$extensionType          = $manifest->attributes()->type;
		$this->extensionElement = $this->getElement($parent, $manifest);

		// Reads current (old) version from manifest
		$db      = JFactory::getDbo();
		$version = $db->setQuery(
			$db->getQuery(true)
				->select($db->qn('s.version_id'))
				->from($db->qn('#__schemas', 's'))
				->join('inner', $db->qn('#__extensions', 'e') . ' ON ' . $db->qn('e.extension_id') . ' = ' . $db->qn('s.extension_id'))
				->where($db->qn('e.element') . ' = ' . $db->q($this->extensionElement))
		)
			->loadResult();

		if (!empty($version))
		{
			$this->oldVersion = (string) $version;
		}

		if ($extensionType == 'component' && in_array($type, array('install', 'update', 'discover_install')))
		{
			// Update SQL pre-processing
			if ($type == 'update')
			{
				if (!$this->preprocessUpdates($parent))
				{
					return false;
				}
			}

			// In case we are installing redcore
			if (get_called_class() === 'Com_RedcoreInstallerScript')
			{
				if (!$this->checkComponentVersion($this->getRedcoreComponentFolder(), __DIR__, 'redcore.xml'))
				{
					throw new \Exception(JText::_('COM_REDCORE_INSTALL_ERROR_OLDER_VERSION'));
				}

				if (!class_exists('RComponentHelper'))
				{
					$searchPaths = array(
						// Discover install
						JPATH_LIBRARIES . '/redcore/component',
						// Install
						__DIR__ . '/redCORE/libraries/redcore/component',
						__DIR__ . '/libraries/redcore/component'
					);

					$componentHelper = JPath::find($searchPaths, 'helper.php');

					if ($componentHelper)
					{
						require_once $componentHelper;
					}
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
							if (get_called_class() === 'Com_RedcoreInstallerScript')
							{
								$this->loadRedcoreLanguage(__DIR__);
								$checked['name'] = JText::_($checked['name']);
							}

							$messageKey = $key == 'extensions'
								? 'COM_REDCORE_INSTALL_ERROR_REQUIREMENTS_EXTENSIONS'
								: 'COM_REDCORE_INSTALL_ERROR_REQUIREMENTS';

							throw new \Exception(JText::sprintf($messageKey, $checked['name'], $checked['required'], $checked['current']));
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
	 * @param   JInstallerAdapter  $parent  Class calling this method
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
	 * @param   JInstallerAdapter  $parent  Class calling this method
	 *
	 * @return  boolean                     True on success
	 */
	public function installOrUpdate($parent)
	{
		// Install extensions
		// We have already installed redCORE library on preflight so we will not do it again
		if (get_called_class() !== 'Com_RedcoreInstallerScript')
		{
			$this->installLibraries($parent);
		}

		$this->loadRedcoreLibrary();
		$this->installMedia($parent);
		$this->installWebservices($parent);
		$this->installModules($parent);
		$this->installPlugins($parent);
		$this->installTemplates($parent);
		$this->installCli($parent);

		return true;
	}

	/**
	 * Method to process SQL updates previous to the install process
	 *
	 * @param   JInstallerAdapter  $parent  Class calling this method
	 *
	 * @return  boolean          True on success
	 */
	public function preprocessUpdates($parent)
	{
		$manifest = $parent->get('manifest');

		if (isset($manifest->update))
		{
			if (isset($manifest->update->attributes()->folder))
			{
				$path       = $manifest->update->attributes()->folder;
				$sourcePath = $parent->getParent()->getPath('source');

				if (isset($manifest->update->pre, $manifest->update->pre->schemas))
				{
					$schemaPaths = $manifest->update->pre->schemas->children();

					if (count($schemaPaths))
					{
						// If it just upgraded redCORE to a newer version using RFactory for database, it forces using the redCORE database drivers
						if (substr(get_class(JFactory::$database), 0, 1) == 'J' && $this->extensionElement != 'com_redcore')
						{
							RFactory::$database = null;
							JFactory::$database = RFactory::getDbo();
						}

						$db = JFactory::getDbo();

						$dbDriver   = strtolower($db->name);
						$dbDriver   = $dbDriver == 'mysqli' ? 'mysql' : $dbDriver;
						$schemaPath = '';

						foreach ($schemaPaths as $entry)
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
									$schemaPath = (string) $entry;
									break;
								}
							}
						}

						if ($schemaPath != '')
						{
							$files = str_replace('.sql', '', JFolder::files($sourcePath . '/' . $path . '/' . $schemaPath, '\.sql$'));
							usort($files, 'version_compare');

							if (count($files))
							{
								foreach ($files as $file)
								{
									if (version_compare($file, $this->oldVersion) > 0)
									{
										$buffer  = file_get_contents($sourcePath . '/' . $path . '/' . $schemaPath . '/' . $file . '.sql');
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
														JLog::add(
															JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)),
															JLog::WARNING,
															'jerror'
														);

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
	 * @param   JInstallerAdapter  $parent              Class calling this method
	 * @param   bool               $executeAfterUpdate  The name of the function to execute
	 *
	 * @return  boolean          True on success
	 */
	public function phpUpdates($parent, $executeAfterUpdate)
	{
		$manifest = $parent->get('manifest');

		if (isset($manifest->update))
		{
			if (isset($manifest->update->php) && isset($manifest->update->php->path))
			{
				$updatePath = (string) $manifest->update->php->path;

				if ($updatePath != '')
				{
					switch ((string) $manifest['type'])
					{
						case 'plugin':
							$sourcePath = JPATH_PLUGINS . '/' . (string) $manifest['group'] . '/' . $this->extensionElement;
							break;
						case 'module':
							if ((string) $manifest['client'] == 'administrator')
							{
								$sourcePath = JPATH_ADMINISTRATOR . '/modules/' . $this->extensionElement;
							}
							else
							{
								$sourcePath = JPATH_SITE . '/modules/' . $this->extensionElement;
							}
							break;
						case 'library':
							$sourcePath = JPATH_BASE . '/libraries/' . $this->extensionElement;
							break;
						case 'component':
						default:
							$sourcePath = JPATH_ADMINISTRATOR . '/components/' . $this->extensionElement;
							break;
					}

					if (is_dir($sourcePath . '/' . $updatePath))
					{
						$files = str_replace('.php', '', JFolder::files($sourcePath . '/' . $updatePath, '\.php$'));

						if (!empty($files))
						{
							usort($files, 'version_compare');

							if (count($files))
							{
								foreach ($files as $file)
								{
									if (version_compare($file, $this->oldVersion) > 0)
									{
										if (!$this->processPHPUpdateFile(
											$parent,
											$sourcePath . '/' . $updatePath . '/' . $file . '.php', $file, $executeAfterUpdate
										))
										{
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

		return true;
	}

	/**
	 * Method to process a single PHP update file
	 *
	 * @param   JInstallerAdapter  $parent              Class calling this method
	 * @param   string             $file                File to process
	 * @param   string             $version             File version
	 * @param   bool               $executeAfterUpdate  The name of the function to execute
	 *
	 * @return  boolean          True on success
	 */
	public function processPHPUpdateFile($parent, $file, $version, $executeAfterUpdate)
	{
		static $upgradeClasses;

		if (!isset($upgradeClasses))
		{
			$upgradeClasses = array();
		}

		require_once $file;

		$extensionElement    = $this->extensionElement;
		$extensionElementArr = explode ('_', $extensionElement);

		foreach ($extensionElementArr as $key => $item)
		{
			$extensionElementArr[$key] = ucfirst($item);
		}

		$extensionElement = implode('_', $extensionElementArr);

		$versionArr = explode('-', $version);

		foreach ($versionArr as $key => $item)
		{
			$versionArr[$key] = ucfirst($item);
		}

		$version = implode('_', $versionArr);
		$version = str_replace('.', '_', $version);

		$class = $extensionElement . 'UpdateScript_' . $version;

		$methodName = $executeAfterUpdate ? 'executeAfterUpdate' : 'execute';

		if (class_exists($class))
		{
			if (!isset($upgradeClasses[$class]))
			{
				$upgradeClasses[$class] = new $class;
			}

			if (method_exists($upgradeClasses[$class], $methodName))
			{
				if (!$upgradeClasses[$class]->{$methodName}($parent))
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
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  void
	 */
	private function installLibraries($parent)
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

				// Standard install
				if (is_dir($extPath))
				{
					$result = $installer->install($extPath);
				}
				// Discover install
				elseif ($extId = $this->searchExtension($extName, 'library', '-1'))
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
	 * @param   JInstallerAdapter  $parent  class calling this method
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
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function installModules($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $parent->get('manifest');
		$src       = $parent->getParent()->getPath('source');
		$nodes     = $manifest->modules->module;

		if (empty($nodes))
		{
			return;
		}

		foreach ($nodes as $node)
		{
			$extName   = $node->attributes()->name;
			$extClient = $node->attributes()->client;
			$extPath   = $src . '/modules/' . $extClient . '/' . $extName;
			$result    = 0;

			// Standard install
			if (is_dir($extPath))
			{
				$installer->setAdapter('module');
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

	/**
	 * Install the package libraries
	 *
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function installPlugins($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $parent->get('manifest');
		$src       = $parent->getParent()->getPath('source');
		$nodes     = $manifest->plugins->plugin;

		if (empty($nodes))
		{
			return;
		}

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
				$installer->setAdapter('plugin');
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
				$db    = JFactory::getDBO();
				$query = $db->getQuery(true);
				$query->update($db->qn('#__extensions'));
				$query->set($db->qn('enabled') . ' = 1');
				$query->set($db->qn('state') . ' = 1');
				$query->where($db->qn('type') . ' = ' . $db->q('plugin'));
				$query->where($db->qn('element') . ' = ' . $db->q($extName));
				$query->where($db->qn('folder') . ' = ' . $db->q($extGroup));
				$db->setQuery($query)->execute();
			}
		}
	}

	/**
	 * Install the package translations
	 *
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function installTranslations($parent)
	{
		// Required objects
		$manifest = $parent->get('manifest');

		if (method_exists('RTranslationTable', 'batchContentElements'))
		{
			$nodes = $manifest->translations->translation;

			if (empty($nodes))
			{
				return;
			}

			foreach ($nodes as $node)
			{
				$extName = (string) $node->attributes()->name;

				try
				{
					RTranslationTable::batchContentElements($extName, 'install');
				}
				catch (Exception $e)
				{
					// We are already setting message queue so we don't need to set it here as well
				}

				$this->_storeStatus('translations', array('name' => $extName, 'result' => true));
			}
		}
	}

	/**
	 * Function to install redCORE for components
	 *
	 * @param   string             $type    type of change (install, update or discover_install)
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  void
	 * @throws  Exception
	 */
	protected function installRedcore($type, $parent)
	{
		// If it's installing redcore as dependency
		if (get_called_class() != 'Com_RedcoreInstallerScript' && $type != 'discover_install')
		{
			$manifest = $this->getManifest($parent);

			if ($manifest->redcore)
			{
				$installer              = $this->getInstaller();
				$redcoreFolder          = __DIR__;
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
		// If it is installing redCORE we want to make sure it installs redCORE library first
		elseif (get_called_class() == 'Com_RedcoreInstallerScript' && in_array($type, array('install', 'update', 'discover_install')))
		{
			$install = $this->checkComponentVersion(JPATH_LIBRARIES . '/redcore', __DIR__ . '/libraries/redcore', 'redcore.xml');

			// Only install if installation in the package is newer version
			if ($install)
			{
				$this->installLibraries($parent);
			}
		}
	}

	/**
	 * Function to install redCORE for components
	 *
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function postInstallRedcore($parent)
	{
		$manifest = $this->getManifest($parent);
		$type     = $manifest->attributes()->type;

		if ($type == 'component')
		{
			$redcoreNode = $manifest->redcore;

			if ($redcoreNode)
			{
				$redcoreFolder = __DIR__;

				if (!empty($redcoreFolder))
				{
					$version = $redcoreNode->attributes()->version;

					$class  = get_called_class();
					$option = strtolower(strstr($class, 'Installer', true));

					$db    = JFactory::getDBO();
					$query = $db->getQuery(true)
						->select($db->qn('params'))
						->from($db->qn('#__extensions'))
						->where($db->qn('type') . ' = ' . $db->q($type))
						->where($db->qn('element') . ' = ' . $db->q($option));

					$comParams = new Registry($db->setQuery($query)->loadResult());
					$comParams->set('redcore',
						array('version' => (string) $version)
					);

					$query = $db->getQuery(true);
					$query->update($db->qn('#__extensions'));
					$query->set($db->qn('params') . ' = ' . $db->q($comParams->toString()));
					$query->where($db->qn('type') . ' = ' . $db->q($type));
					$query->where($db->qn('element') . ' = ' . $db->q($option));
					$db->setQuery($query)->execute();
				}
			}
		}
	}

	/**
	 * Install the package templates
	 *
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  void
	 */
	private function installTemplates($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $parent->get('manifest');
		$src       = $parent->getParent()->getPath('source');
		$nodes     = $manifest->templates->template;

		if ($nodes)
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
					$installer->setAdapter('template');
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
	 * Install the package Cli scripts
	 *
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  void
	 */
	private function installCli($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $parent->get('manifest');
		$src       = $parent->getParent()->getPath('source');

		if (!$manifest)
		{
			return;
		}

		$installer->setPath('source', $src);
		$element = $manifest->cli;

		if (!$element || !count($element->children()))
		{
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return;
		}

		$nodes = $element->children();

		foreach ($nodes as $node)
		{
			// Here we set the folder name we are going to copy the files to.
			$name = (string) $node->attributes()->name;

			// Here we set the folder we are going to copy the files to.
			$destination = JPath::clean(JPATH_ROOT . '/cli/' . $name);

			// Here we set the folder we are going to copy the files from.
			$folder = (string) $node->attributes()->folder;

			if ($folder && file_exists($src . '/' . $folder))
			{
				$source = $src . '/' . $folder;
			}
			else
			{
				// Cli folder does not exist
				continue;
			}

			$copyFiles = $this->prepareFilesForCopy($element, $source, $destination);

			$installer->copyFiles($copyFiles, true);
		}
	}

	/**
	 * Method to parse through a webservices element of the installation manifest and take appropriate
	 * action.
	 *
	 * @param   JInstallerAdapter  $parent  class calling this method
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

		// Here we set the folder we are going to copy the files from.
		$folder = (string) $element->attributes()->folder;

		// This prevents trying to install webservice from other extension directory if webservice folder is not set
		if (!$folder || !is_dir($src . '/' . $folder))
		{
			return false;
		}

		// Here we set the folder we are going to copy the files to.
		$destination = JPath::clean(RApiHalHelper::getWebservicesPath());
		$source      = $src . '/' . $folder;

		$copyFiles = $this->prepareFilesForCopy($element, $source, $destination);

		// Copy the webservice XML files
		$return = $installer->copyFiles($copyFiles, true);

		// Recreate or create new SOAP WSDL files
		if (method_exists('RApiSoapHelper', 'generateWsdlFromFolder'))
		{
			foreach ($element->children() as $file)
			{
				RApiSoapHelper::generateWsdlFromFolder($destination . '/' . $file);
			}
		}

		return $return;
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
			$path         = array();
			$path['src']  = $source . '/' . $file;
			$path['dest'] = $destination . '/' . $file;

			// Is this path a file or folder?
			$path['type'] = ($file->getName() == 'folder') ? 'folder' : 'file';

			if (basename($path['dest']) != $path['dest'])
			{
				$newDir = dirname($path['dest']);

				if (!JFolder::create($newDir))
				{
					JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_CREATE_DIRECTORY', $newDir), JLog::WARNING, 'jerror');

					return array();
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
	 * @param   string             $type    type of change (install, update or discover_install)
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  boolean
	 */
	public function postflight($type, $parent)
	{
		$installer = get_called_class();

		// If it's installing redcore as dependency
		if ($installer !== 'Com_RedcoreInstallerScript' && $type != 'discover_install')
		{
			$this->postInstallRedcore($parent);
		}

		// Execute the postflight tasks from the manifest
		$this->postFlightFromManifest($type, $parent);

		$this->installTranslations($parent);

		/** @var JXMLElement $manifest */
		$manifest = $parent->get('manifest');

		if (in_array($type, array('install', 'update', 'discover_install')))
		{
			$attributes = current($manifest->attributes());

			// If it's a component
			if (isset($attributes['type']) && (string) $attributes['type'] === 'component')
			{
				$this->loadRedcoreLanguage();
				$this->displayComponentInfo($parent);
			}
		}

		if ($type == 'update')
		{
			$db = JFactory::getDbo();
			$db->setQuery('TRUNCATE ' . $db->qn('#__redcore_schemas'))
				->execute();
		}

		// If this is install redcore component.
		if ($installer == 'Com_RedcoreInstallerScript')
		{
			$this->insertSiteDomain();
		}

		return true;
	}

	/**
	 * Execute the postflight tasks from the manifest if there is any.
	 *
	 * @param   object             $type    type of change (install, update or discover_install)
	 * @param   JInstallerAdapter  $parent  class calling this method
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
				$class    = get_called_class();

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
	 * @param   string             $type    Type of change (install, update or discover_install)
	 * @param   JInstallerAdapter  $parent  Class calling this method
	 * @param   string             $client  The client
	 *
	 * @return  void
	 */
	protected function deleteMenu($type, $parent, $client = null)
	{
		/** @var JXMLElement $manifest */
		$manifest   = $parent->get('manifest');
		$attributes = current($manifest->attributes());

		// If it's not a component
		if (!isset($attributes['type']))
		{
			return;
		}

		$type          = $attributes['type'];
		$componentName = (string) $manifest->name;

		if (empty($componentName))
		{
			return;
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->delete('#__menu')
			->where('type = ' . $db->q($type));

		if (!empty($client))
		{
			$query->where($db->qn('client_id') . ' = ' . $db->q($client));
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
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true)
			->select($db->qn('extension_id'))
			->from($db->qn("#__extensions"))
			->where($db->qn('type') . ' = ' . $db->q($type))
			->where($db->qn('element') . ' = ' . $db->q($element));

		if (null !== $state)
		{
			$query->where($db->qn('state') . ' = ' . (int) $state);
		}

		if (null !== $folder)
		{
			$query->where($db->qn('folder') . ' = ' . $db->q($folder));
		}

		return $db->setQuery($query)->loadResult();
	}

	/**
	 * method to update the component
	 *
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return void
	 */
	public function update($parent)
	{
		// Process PHP update files
		$this->phpUpdates($parent, false);

		// Common tasks for install or update
		$this->installOrUpdate($parent);

		// Process PHP update files
		$this->phpUpdates($parent, true);
	}

	/**
	 * Prevents uninstalling redcore component if some components using it are still installed.
	 *
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 */
	private function preventUninstallRedcore($parent)
	{
		$this->loadRedcoreLibrary();

		// Avoid uninstalling redcore if there is a component using it
		$manifest  = $this->getManifest($parent);
		$isRedcore = 'COM_REDCORE' == (string) $manifest->name;

		if ($isRedcore)
		{
			if (method_exists('RComponentHelper', 'getRedcoreComponents'))
			{
				$components = RComponentHelper::getRedcoreComponents();

				if (!empty($components))
				{
					$app     = JFactory::getApplication();
					$message = sprintf(
						'Cannot uninstall redCORE because the following components are using it: <br /> [%s]',
						implode(',<br /> ', $components)
					);

					$app->enqueueMessage($message, 'error');
					$app->redirect('index.php?option=com_installer&view=manage');
				}
			}
		}
	}

	/**
	 * method to uninstall the component
	 *
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 */
	public function uninstall($parent)
	{
		$this->preventUninstallRedcore($parent);

		// Uninstall extensions
		$this->uninstallTranslations($parent);
		$this->uninstallMedia($parent);
		$this->uninstallWebservices($parent);
		$this->uninstallModules($parent);
		$this->uninstallPlugins($parent);
		$this->uninstallTemplates($parent);
		$this->uninstallCli($parent);
		$this->uninstallLibraries($parent);
	}

	/**
	 * Uninstall all Translation tables from database
	 *
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 */
	protected function uninstallTranslations($parent)
	{
		if (method_exists('RTranslationTable', 'batchContentElements'))
		{
			// Required objects
			$manifest              = $parent->get('manifest');
			$class                 = get_called_class();
			$deleteIds             = array();
			$translationTables     = RTranslationTable::getInstalledTranslationTables(true);
			$translationTableModel = RModel::getAdminInstance('Translation_Table', array(), 'com_redcore');

			// Delete specific extension translation tables
			if ($class != 'Com_RedcoreInstallerScript')
			{
				$nodes = $manifest->translations->translation;

				if ($nodes)
				{
					foreach ($nodes as $node)
					{
						$extensionOption = (string) $node->attributes()->name;

						if (!empty($translationTables))
						{
							foreach ($translationTables as $translationTableParams)
							{
								if ($extensionOption == $translationTableParams->option)
								{
									$deleteIds[] = $translationTableParams->id;
								}
							}
						}
					}
				}
			}
			// We delete everything
			else
			{
				if (!empty($translationTables))
				{
					foreach ($translationTables as $translationTableParams)
					{
						$deleteIds[] = $translationTableParams->id;
					}
				}
			}

			if (!empty($deleteIds))
			{
				foreach ($deleteIds as $deleteId)
				{
					try
					{
						$translationTableModel->delete($deleteId);
					}
					catch (Exception $e)
					{
						JFactory::getApplication()->enqueueMessage(
							JText::sprintf('LIB_REDCORE_TRANSLATIONS_DELETE_ERROR', $e->getMessage()), 'error'
						);
					}
				}
			}
		}
	}

	/**
	 * Uninstall the package libraries
	 *
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function uninstallLibraries($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $this->getManifest($parent);
		$nodes     = $manifest->libraries->library;

		if ($nodes)
		{
			foreach ($nodes as $node)
			{
				$extName = $node->attributes()->name;
				$result  = 0;
				$extId   = $this->searchExtension($extName, 'library');

				if ($extId)
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
	 * @param   JInstallerAdapter  $parent  class calling this method
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
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  boolean
	 */
	protected function uninstallWebservices($parent)
	{
		// Required objects
		$manifest = $this->getManifest($parent);

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
		$files  = $element->children();
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
				JLog::add(JText::sprintf('LIB_REDCORE_INSTALLER_ERROR_FAILED_TO_DELETE', $path), JLog::WARNING, 'jerror');
				$returnValue = false;
			}
		}

		return $returnValue;
	}

	/**
	 * Uninstall the Cli
	 *
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  boolean
	 */
	protected function uninstallCli($parent)
	{
		// Required objects
		$manifest = $this->getManifest($parent);

		if (!$manifest)
		{
			return false;
		}

		// We will use cli removal function to remove cli folders
		$element = $manifest->cli;

		if (!$element || !count($element->children()))
		{
			// Either the tag does not exist or has no children therefore we return zero files processed.
			return true;
		}

		$returnValue = true;

		// Get the array of file nodes to process
		$folders = $element->children();
		$source  = JPATH_ROOT . '/cli/';

		// Process each folder in the $folders array
		foreach ($folders as $folder)
		{
			// Here we set the folder name we are going to delete from cli main folder
			$name = (string) $folder->attributes()->name;

			// If name is not set we should not delete whole cli folder
			if (empty($name))
			{
				continue;
			}

			$path = $source . '/' . $name;

			// Delete the files/folders
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
				JLog::add(JText::sprintf('LIB_REDCORE_INSTALLER_ERROR_FAILED_TO_DELETE', $path), JLog::WARNING, 'jerror');
				$returnValue = false;
			}
		}

		return $returnValue;
	}

	/**
	 * Uninstall the package modules
	 *
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function uninstallModules($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $this->getManifest($parent);
		$nodes     = $manifest->modules->module;

		if (empty($nodes))
		{
			return;
		}

		foreach ($nodes as $node)
		{
			$extName   = $node->attributes()->name;
			$extClient = $node->attributes()->client;
			$result    = 0;
			$extId     = $this->searchExtension($extName, 'module');

			if ($extId)
			{
				$result = $installer->uninstall('module', $extId);
			}

			// Store the result to show install summary later
			$this->_storeStatus('modules', array('name' => $extName, 'client' => $extClient, 'result' => $result));
		}
	}

	/**
	 * Uninstall the package plugins
	 *
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function uninstallPlugins($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $this->getManifest($parent);
		$nodes     = $manifest->plugins->plugin;

		if (empty($nodes))
		{
			return;
		}

		foreach ($nodes as $node)
		{
			$extName  = $node->attributes()->name;
			$extGroup = $node->attributes()->group;
			$result   = 0;
			$extId    = $this->searchExtension($extName, 'plugin', null, $extGroup);

			if ($extId)
			{
				$result = $installer->uninstall('plugin', $extId);
			}

			// Store the result to show install summary later
			$this->_storeStatus('plugins', array('name' => $extName, 'group' => $extGroup, 'result' => $result));
		}
	}

	/**
	 * Uninstall the package templates
	 *
	 * @param   JInstallerAdapter  $parent  class calling this method
	 *
	 * @return  void
	 */
	protected function uninstallTemplates($parent)
	{
		// Required objects
		$installer = $this->getInstaller();
		$manifest  = $this->getManifest($parent);
		$nodes     = $manifest->templates->template;

		if (empty($nodes))
		{
			return;
		}

		foreach ($nodes as $node)
		{
			$extName   = $node->attributes()->name;
			$extClient = $node->attributes()->client;
			$result    = 0;
			$extId     = $this->searchExtension($extName, 'template', 0);

			if ($extId)
			{
				$result = $installer->uninstall('template', $extId);
			}

			// Store the result to show install summary later
			$this->_storeStatus('templates', array('name' => $extName, 'client' => $extClient, 'result' => $result));
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
		if (null === $this->status)
		{
			$this->status = new stdClass;
		}

		// Initialise current status type if needed
		if (!isset($this->status->{$type}))
		{
			$this->status->{$type} = array();
		}

		// Insert the status
		$this->status->{$type}[] = $status;
	}

	/**
	 * Method to display component info
	 *
	 * @param   JInstallerAdapter  $parent   Class calling this method
	 * @param   string             $message  Message to apply to the Component info layout
	 *
	 * @return  void
	 */
	public function displayComponentInfo($parent, $message = '')
	{
		$this->loadRedcoreLibrary();

		if ($this->showComponentInfo && method_exists('RComponentHelper', 'displayComponentInfo'))
		{
			$manifest = $this->getManifest($parent);
			echo RComponentHelper::displayComponentInfo((string) $manifest->name, $message);
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
		|| $lang->load('com_redcore', $path . '/components/com_redcore', null, true, true)
		|| $lang->load('com_redcore', $path . '/components/com_redcore/admin', null, true, true);
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
	 * @throws  Exception
	 */
	public function checkComponentVersion($original, $source, $xmlFile)
	{
		if (is_dir($original))
		{
			try
			{
				$source      = $source . '/' . $xmlFile;
				$sourceXml   = new SimpleXMLElement($source, 0, true);
				$original    = $original . '/' . $xmlFile;
				$originalXml = new SimpleXMLElement($original, 0, true);

				if (version_compare((string) $sourceXml->version, (string) $originalXml->version, '<'))
				{
					return false;
				}
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage(
					JText::_('COM_REDCORE_INSTALL_UNABLE_TO_CHECK_VERSION'),
					'message'
				);
			}
		}

		return true;
	}

	/**
	 * Gets or generates the element name (using the manifest)
	 *
	 * @param   JInstallerAdapter  $parent    Parent adapter
	 * @param   SimpleXMLElement   $manifest  Extension manifest
	 *
	 * @return  string  Element
	 */
	public function getElement($parent, $manifest = null)
	{
		if (method_exists($parent, 'getElement'))
		{
			return $parent->getElement();
		}

		if (null === $manifest)
		{
			$manifest = $parent->get('manifest');
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

	/**
	 * Shit happens. Patched function to bypass bug in package uninstaller
	 *
	 * @param   JInstallerAdapter  $parent  Parent object
	 *
	 * @return  void
	 */
	protected function loadManifest($parent)
	{
		$element      = strtolower(str_replace('InstallerScript', '', get_called_class()));
		$elementParts = explode('_', $element);

		// Type not properly detected or not a package
		if (count($elementParts) !== 2 || strtolower($elementParts[0]) !== 'pkg')
		{
			$this->manifest = $parent->get('manifest');

			return;
		}

		$rootPath     = $parent->getParent()->getPath('extension_root');
		$manifestPath = dirname($rootPath);
		$manifestFile = $manifestPath . '/' . $element . '.xml';

		// Package manifest found
		if (file_exists($manifestFile))
		{
			$this->manifest = new SimpleXMLElement($manifestFile);

			return;
		}

		$this->manifest = $parent->get('manifest');
	}

	/**
	 * Setup site url for redCORE config
	 *
	 * @return  void
	 */
	private function insertSiteDomain()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('extension_id'))
			->from($db->qn('#__extensions'))
			->where($db->qn('type') . ' = ' . $db->q('component'))
			->where($db->qn('element') . ' = ' . $db->q('com_redcore'));

		$extensionId = $db->setQuery($query)->loadResult();

		if (!$extensionId)
		{
			return;
		}

		/** @var JTableExtension $table */
		$table = JTable::getInstance('Extension', 'JTable');

		if (!$table->load($extensionId))
		{
			return;
		}

		$params = new Registry($table->get('params'));

		// Skip update if already exist
		if ($params->get('domain', ''))
		{
			return;
		}

		$params->set('domain', $_SERVER['SERVER_NAME']);
		$table->set('params', $params->toString());
		$table->store();
	}
}
