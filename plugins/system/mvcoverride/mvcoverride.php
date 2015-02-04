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
JLoader::import('system.mvcoverride.helper.mvcloader', JPATH_PLUGINS);

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

	protected static $componentList = array();

	protected static $option;

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
		JPlugin::loadLanguage('plg_system_mvcoverride');

		parent::__construct($subject, $config);

		JPluginHelper::importPlugin('redcore');

		$app = JFactory::getApplication();

		$includePath = $this->params->get('includePath', '{JPATH_BASE}/code,{JPATH_THEMES}/{template}/code');

		$includePath = str_replace(
			array('{JPATH_BASE}', '{JPATH_THEMES}', '{template}'),
			array(JPATH_BASE, JPATH_THEMES, $app->getTemplate()),
			$includePath
		);

		$includePath = explode(',', $includePath);

		// Register additional include paths for code replacements from plugins
		$app->triggerEvent('onMVCOverrideIncludePaths', array(&$includePath));

		MVCOverrideHelperCodepool::addCodePath($includePath);

		MVCLoader::setupOverrideLoader(
			$this->params->get('changePrivate', 0),
			$this->params->get('extendPrefix', ''),
			$this->params->get('extendSuffix', 'Default')
		);

		$this->setOverrideFiles();
	}

	/**
	 * onAfterRoute function.
	 *
	 * @access public
	 * @return void
	 */
	public function onAfterRoute()
	{
		$option = $this->getOption();

		if ($option === false || !isset(self::$componentList[$option]))
		{
			return;
		}

		MVCOverrideHelperCodepool::initialize();

		// Add override paths for the current component files
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
	}

	/**
	 * Set Override Files
	 *
	 * @return  void
	 */
	public function setOverrideFiles()
	{
		$includePaths = MVCOverrideHelperCodepool::addCodePath(null);

		foreach ($includePaths as $includePath)
		{
			if ($components = JFolder::folders($includePath))
			{
				foreach ($components as $component)
				{
					self::$componentList[$component] = $component;
					$this->addOverrideFiles($includePath, $component);
				}
			}
		}
	}

	/**
	 * Add new files
	 *
	 * @param   string  $includePath  Path for check inner files
	 * @param   string  $component    Component name folder
	 *
	 * @return void
	 */
	private function addOverrideFiles($includePath, $component)
	{
		$types = array('controllers', 'models', 'helpers', 'views');

		foreach ($types as $type)
		{
			$searchFolder = $includePath . '/' . $component . '/' . $type;

			if (!JFolder::exists($searchFolder))
			{
				continue;
			}

			$componentName = str_replace('com_', '', $component);

			switch ($type)
			{
				case 'helpers':
				if ($listFiles = JFolder::files($searchFolder, '.php', false, true))
				{
					foreach ($listFiles as $file)
					{
						$fileName = JFile::stripExt(basename($file));
						$indexName = $componentName . 'helper' . $fileName;
						$this->getOverrideFileInfo($includePath, $component, $file, $type, $indexName);
					}
				}
					break;

				case 'views':
				// Reading view folders
				if ($views = JFolder::folders($searchFolder))
				{
					foreach ($views as $view)
					{
						// Get view formats files
						if ($listFiles = JFolder::files($searchFolder . '/' . $view, '.php', false, true))
						{
							foreach ($listFiles as $file)
							{
								$this->getOverrideFileInfo($includePath, $component, $file, $type);
							}
						}
					}
				}
					break;

				default:
				if ($listFiles = JFolder::files($searchFolder, '.php', false, true))
				{
					foreach ($listFiles as $file)
					{
						$this->getOverrideFileInfo($includePath, $component, $file, $type);
					}
				}
			}
		}
	}

	/**
	 * Get file info
	 *
	 * @param   string  $includePath  Path for check inner files
	 * @param   string  $component    Component name folder
	 * @param   string  $filePath     Checking file path
	 * @param   string  $type         Type file
	 * @param   string  $indexName    Name usage for helper index
	 *
	 * @return stdClass
	 */
	private function getOverrideFileInfo($includePath, $component, $filePath, $type = '', $indexName = '')
	{
		$filePath = JPath::clean($filePath);
		$sameFolderPrefix = $component . '/' . $type;

		if ($type == 'helpers')
		{
			$app = JFactory::getApplication();
			$baseName = basename($filePath);
			$prefix = substr($baseName, 0, 5);

			if (($app->isAdmin() && $prefix == 'admin') || (!$app->isAdmin() && $prefix != 'admin') )
			{
				$realPath = JPATH_SITE . '/components' . substr($filePath, strlen($includePath));
			}
			else
			{
				$realPath = JPATH_ADMINISTRATOR . '/components/' . $sameFolderPrefix . '/' . substr($baseName, 5);
			}
		}
		else
		{
			$realPath = JPATH_BASE . '/components/' . substr($filePath, strlen($includePath));
		}

		$realPath = JPath::clean($realPath);

		if (!JFile::exists($realPath))
		{
			return;
		}

		$forOverrideFile = file_get_contents($realPath);
		$originalClass = MVCOverrideHelperOverride::getOriginalClass($forOverrideFile);
		unset($forOverrideFile);

		if ($type == 'helpers')
		{
			JLoader::register($indexName, $filePath);
		}
		else
		{
			// Set path for new file
			MVCLoader::setOverrideFile($originalClass, $filePath);
		}

		// Set path for override file
		MVCLoader::setOverrideFile(
			$originalClass,
			$realPath,
			true,
			$this->params->get('extendPrefix', ''),
			$this->params->get('extendSuffix', 'Default')
		);
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
}
