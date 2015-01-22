<?php
/**
 * @package     RedCORE.Plugin
 * @subpackage  System.MVCOverride
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.folder');

JLoader::import('system.mvcoverride.helper.override', JPATH_PLUGINS);
JLoader::import('system.mvcoverride.helper.codepool', JPATH_PLUGINS);

/**
 * PlgSystemMVCOverride class.
 *
 * @extends JPlugin
 * @since  2.5
 */
class PlgSystemMVCOverride extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 */
	protected $autoloadLanguage = true;

	protected static $option;

	protected $files = array();

	protected static $loadClass = array();

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'group', 'params', 'language'
	 *                             (this list is not meant to be comprehensive).
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);
		$option = $this->getOption();

		if ($option === false)
		{
			return;
		}

		// Constants to replace JPATH_COMPONENT, JPATH_COMPONENT_SITE and JPATH_COMPONENT_ADMINISTRATOR
		// Constants is deprecated and not using in new changes
		define('JPATH_SOURCE_COMPONENT', JPATH_BASE . '/components/' . $option);
		define('JPATH_SOURCE_COMPONENT_SITE', JPATH_SITE . '/components/' . $option);
		define('JPATH_SOURCE_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/' . $option);

		JPlugin::loadLanguage('plg_system_mvcoverride');
		spl_autoload_register(array('PlgSystemMVCOverride', '_autoload'), false, true);
		MVCOverrideHelperCodepool::initialize();

		JPluginHelper::importPlugin('redcore');

		$app = JFactory::getApplication();

		// Get files that can be overrided
		$componentOverrideFiles = $this->loadComponentFiles($option);

		// Template name
		$template = $app->getTemplate();

		// Code paths
		$includePath = array();

		// Base extensions path
		$includePath[] = JPATH_BASE . '/code';

		// Template code path
		$includePath[] = JPATH_THEMES . '/' . $template . '/code';

		// Register additional include paths for code replacements from plugins
		$app->triggerEvent('onMVCOverrideIncludePaths', array(&$includePath));

		MVCOverrideHelperCodepool::addCodePath($includePath);

		foreach (MVCOverrideHelperCodepool::addCodePath() as $codePool)
		{
			if (version_compare(JVERSION, '3.0', '>='))
			{
				JViewLegacy::addViewHelperPath($codePool . '/' . $option);
				JViewLegacy::addViewTemplatePath($codePool . '/' . $option);
			}
			else
			{
				JView::addViewHelperPath($codePool . '/' . $option);
				JView::addViewTemplatePath($codePool . '/' . $option);
			}

			JModuleHelper::addIncludePath($codePool . '/modules');
			JTable::addIncludePath($codePool . '/' . $option . '/tables');

			JModelForm::addComponentFormPath($codePool . '/' . $option . '/models/forms');
			JModelForm::addComponentFieldPath($codePool . '/' . $option . '/models/fields');
		}

		// Loading override files
		if (!empty($componentOverrideFiles))
		{
			$includePaths = MVCOverrideHelperCodepool::addCodePath(null, true);

			foreach ($componentOverrideFiles as $key => $componentFile)
			{
				if ($filePath = JPath::find($includePaths, $componentFile->newPath))
				{
					// Include the original code and replace class name add a Default on
					if ($this->params->get('extendDefault', 1))
					{
						$forOverrideFile = file_get_contents($componentFile->root . $componentFile->path);
						$originalClass = MVCOverrideHelperOverride::getOriginalClass($forOverrideFile);
						unset($forOverrideFile);
						self::register(
							$originalClass,
							$componentFile->root . $componentFile->path,
							true,
							MVCOverrideHelperOverride::PREFIX,
							MVCOverrideHelperOverride::SUFFIX,
							$this->params->get('changePrivate', 0)
						);

						// Load helpers
						if (!is_int($key))
						{
							JLoader::register($key, $filePath);
						}
						else
						{
							self::register($originalClass, $filePath, true, '', '');
						}
					}
					else
					{
						require_once $filePath;
					}
				}
			}
		}
	}

	/**
	 * Directly register a class to the autoload list.
	 *
	 * @param   string       $class          The class name to register.
	 * @param   string       $path           Full path to the file that holds the class to register.
	 * @param   boolean      $force          True to overwrite the autoload path value for the class if it already exists.
	 * @param   string|null  $prefix         Prefix class
	 * @param   string|null  $suffix         Suffix class
	 * @param   int          $changePrivate  Flag for change private to public or not
	 *
	 * @return  void
	 */
	public static function register($class, $path, $force = true, $prefix = null, $suffix = null, $changePrivate = 0)
	{
		// Sanitize class name.
		$class = strtolower($prefix . $class . $suffix);

		// Only attempt to register the class if the name and file exist.
		if (!empty($class) && is_file($path))
		{
			// Register the class with the autoloader if not already registered or the force flag is set.
			if (empty(self::$loadClass[$class]) || $force)
			{
				self::$loadClass[$class] = array('path' => $path, 'prefix' => $prefix, 'suffix' => $suffix, 'changePrivate' => $changePrivate);
			}
		}
	}


	/**
	 * Autoload a class based on name.
	 *
	 * @param   string  $class  The class to be loaded.
	 *
	 * @return  boolean  True if the class was loaded, false otherwise.
	 */
	private static function _autoload($class)
	{
		// Sanitize class name.
		$class = strtolower($class);

		if (isset(self::$loadClass[$class]) && file_exists(self::$loadClass[$class]['path']))
		{
			$data = self::$loadClass[$class];

			if (file_exists($data['path']))
			{
				if ($data['prefix'] === '' && $data['suffix'] === '')
				{
					return include $data['path'];
				}
				else
				{
					$bufferContent = MVCOverrideHelperOverride::createDefaultClass($data['path'], $data['prefix'], $data['suffix']);

					// Change private methods to protected methods
					if (isset($data['changePrivate']) && $data['changePrivate'])
					{
						$bufferContent = preg_replace(
							'/private *function/i',
							'protected function',
							$bufferContent
						);
					}

					// Finally we can load the base class
					MVCOverrideHelperOverride::load($bufferContent);

					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Get file info
	 *
	 * @param   string  $path  Path
	 * @param   string  $side  Side execute
	 * @param   string  $type  Type files
	 *
	 * @return stdClass
	 */
	private function getFileInfo($path, $side = 'component', $type = '')
	{
		$object = new stdClass;
		$object->path = JPath::clean($path);
		$object->side = $side;
		$app = JFactory::getApplication();

		// Cleaning files
		switch ($side)
		{
			case 'component':
				$object->path = substr($object->path, strlen(JPATH_BASE . '/components/'));
				$object->root = JPATH_BASE . '/components/';
				break;
			case 'site':
				$object->path = substr($object->path, strlen(JPATH_SITE . '/components/'));
				$object->root = JPATH_SITE . '/components/';
				break;
			case 'admin':
				$object->path = substr($object->path, strlen(JPATH_ADMINISTRATOR . '/components/'));
				$object->root = JPATH_ADMINISTRATOR . '/components/';
				break;
		}

		if ((($app->isAdmin() && $side == 'component') || $side == 'admin') && $type == 'helper')
		{
			$object->newPath = str_replace(JFile::getName($object->path), 'admin' . JFile::getName($object->path), $object->path);
		}
		else
		{
			$object->newPath = $object->path;
		}

		return $object;
	}

	/**
	 * Get option
	 *
	 * @return bool|mixed|string
	 */
	private function getOption()
	{
		if (self::$option)
		{
			return self::$option;
		}

		$app = JFactory::getApplication();
		self::$option = $app->input->getCmd('option', '');

		if (empty(self::$option) && $app->isSite())
		{
			$menuDefault = JFactory::getApplication()->getMenu()->getDefault();

			if (!$menuDefault)
			{
				return false;
			}

			$componentID = $menuDefault->component_id;
			$db = JFactory::getDBO();
			$query = $db->getQuery(true)
				->select('element')
				->from($db->qn('#__extensions'))
				->where('extension_id = ' . $db->quote($componentID));
			$db->setQuery($query);
			self::$option = $db->loadResult();
		}

		return self::$option;
	}

	/**
	 * Add new files
	 *
	 * @param   string  $folder  Name folder
	 * @param   string  $type    Type files
	 * @param   string  $side    Side execute
	 *
	 * @return void
	 */
	private function addNewFiles($folder, $type, $side = 'component')
	{
		if (!JFolder::exists($folder))
		{
			return;
		}

		$app = JFactory::getApplication();
		$componentName = str_replace('com_', '', $this->getOption());

		switch ($type)
		{
			case 'helper':
				if ($listFiles = JFolder::files($folder, '.php', false, true))
				{
					foreach ($listFiles as $file)
					{
						if (($app->isAdmin() && $side == 'component') || $side == 'admin')
						{
							$indexName = $componentName . 'helperadmin' . JFile::stripExt(JFile::getName($file));
						}
						else
						{
							$indexName = $componentName . 'helper' . JFile::stripExt(JFile::getName($file));
						}

						$this->files[$indexName] = $this->getFileInfo($file, $side, $type);
					}
				}
				break;
			case 'view':
				// Reading view folders
				if ($views = JFolder::folders($folder))
				{
					foreach ($views as $view)
					{
						// Get view formats files
						if ($listFiles = JFolder::files($folder . '/' . $view, '.php', false, true))
						{
							foreach ($listFiles as $file)
							{
								$this->files[] = $this->getFileInfo($file, $side);
							}
						}
					}
				}
				break;
			default:
				if ($listFiles = JFolder::files($folder, '.php', false, true))
				{
					foreach ($listFiles as $file)
					{
						$this->files[] = $this->getFileInfo($file, $side);
					}
				}
		}
	}

	/**
	 * loadComponentFiles function.
	 *
	 * @param   mixed  $option  Component name
	 *
	 * @access private
	 *
	 * @return array
	 */
	private function loadComponentFiles($option)
	{
		$JPATH_COMPONENT = JPATH_BASE . '/components/' . $option;

		// Check if default controller exists
		if (JFile::exists($JPATH_COMPONENT . '/controller.php'))
		{
			$this->files[] = $this->getFileInfo($JPATH_COMPONENT . '/controller.php');
		}

		$this->addNewFiles($JPATH_COMPONENT . '/controllers', 'controller');
		$this->addNewFiles($JPATH_COMPONENT . '/models', 'model');
		$this->addNewFiles(JPATH_SITE . '/components/' . $option . '/helpers', 'helper', 'site');
		$this->addNewFiles(JPATH_ADMINISTRATOR . '/components/' . $option . '/helpers', 'helper', 'admin');
		$this->addNewFiles($JPATH_COMPONENT . '/views', 'view');

		return $this->files;
	}
}
