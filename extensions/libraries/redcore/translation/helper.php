<?php
/**
 * @package     Redcore
 * @subpackage  Translation
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');

/**
 * A Translation helper.
 *
 * @package     Redcore
 * @subpackage  Translation
 * @since       1.0
 */
class RTranslationHelper
{
	/**
	 * Defines if jQuery Migrate should be loaded in Frontend component/modules
	 *
	 * @var    JRegistry
	 */
	public static $pluginParams = null;

	/**
	 * An array to hold tables from database
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $contentElements = array();

	/**
	 * An array to hold tables from database
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $installedTranslationTables = null;

	/**
	 * Default language
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $siteLanguage = null;

	/**
	 * Include paths for searching for Params classes.
	 *
	 * @var    array
	 * @since  1.0
	 */
	public static $includePaths = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
		self::$pluginParams = new JRegistry;
	}

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
				new RecursiveDirectoryIterator(JPATH_SITE . '/media/redcore/translations')
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
			$contentElementsXml = array();
			$contentElementsXmlRedcorePath = RTranslationContentElement::getContentElementFolderPath($extension, true);

			if (is_dir($contentElementsXmlRedcorePath))
			{
				$contentElementsXml = JFolder::files($contentElementsXmlRedcorePath, '.xml', true);
			}

			$contentElementsXmlExtensionPath = RTranslationContentElement::getContentElementFolderPath($extension);

			if (is_dir($contentElementsXmlExtensionPath))
			{
				$contentElementsXmlExtension = JFolder::files($contentElementsXmlExtensionPath, '.xml', true);

				if (!empty($contentElementsXmlExtension))
				{
					$contentElementsXml = array_merge($contentElementsXml, $contentElementsXmlExtension);
				}
			}

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
		if (empty(self::$contentElements) || empty(self::$contentElements[$extensionName]))
		{
			self::loadContentElements($extensionName);
		}

		if (!empty(self::$contentElements[$extensionName]))
		{
			return self::$contentElements[$extensionName];
		}

		return array();
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

	/**
	 * Get list of all translation tables with columns
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function getInstalledTranslationTables()
	{
		if (!isset(self::$installedTranslationTables))
		{
			$db = JFactory::getDbo();
			$oldTranslate = $db->translate;

			// We do not want to translate this value
			$db->translate = false;

			$component = JComponentHelper::getComponent('com_redcore');

			// We put translation check back on
			$db->translate = $oldTranslate;
			self::$installedTranslationTables = (array) $component->params->get('translations', array());
		}

		return self::$installedTranslationTables;
	}

	/**
	 * Get default language
	 *
	 * @param   string  $client  Name of the client to get (site|admin)
	 *
	 * @return  string  Name of the language ex. en-GB
	 */
	public static function getSiteLanguage($client = 'site')
	{
		if (!isset(self::$siteLanguage))
		{
			$db = JFactory::getDbo();
			$oldTranslate = $db->translate;

			// We do not want to translate this value
			$db->translate = false;

			self::$siteLanguage = JComponentHelper::getParams('com_languages')->get($client);

			// We put translation check back on
			$db->translate = $oldTranslate;
		}

		return self::$siteLanguage;
	}

	/**
	 * Set a value to translation table list
	 *
	 * @param   string  $option          Extension option name
	 * @param   string  $table           Table name
	 * @param   object  $contentElement  Content Element
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function setInstalledTranslationTables($option, $table, $contentElement)
	{
		// Initialize installed tables before proceeding
		self::getInstalledTranslationTables();

		if (empty($contentElement))
		{
			unset(self::$installedTranslationTables[$table]);
			self::loadContentElements($option);
		}
		else
		{
			self::$installedTranslationTables[$table] = array(
				'option' => $option,
				'table' => $table,
				'name' => $contentElement->name,
				'columns' => $contentElement->allContentElementsFields,
				'primaryKeys' => $contentElement->allPrimaryKeys,
				'fallbackColumns' => $contentElement->allFallbackColumns,
				'xml' => $contentElement->contentElementXml,
				'path' => $contentElement->contentElementXmlPath,
				'formLinks' => $contentElement->getEditForms(),
				'state' => 1,
			);

			self::loadContentElements($option);
		}
	}

	/**
	 * Checks if this is edit form and restricts table from translations
	 *
	 * @param   array  $translationTables  List of translation tables
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function removeFromEditForm($translationTables)
	{
		$input = JFactory::getApplication()->input;
		$option = $input->getString('option', '');
		$view = $input->getString('view', '');
		$layout = $input->getString('layout', '');
		$task = $input->getString('layout', '');

		if ($layout == 'edit' || $task == 'edit')
		{
			foreach ($translationTables as $tableKey => $translationTable)
			{
				if (!empty($translationTable->formLinks))
				{
					foreach ($translationTable->formLinks as $formLink)
					{
						$formLinks = explode('#', $formLink);

						if (count($formLinks) > 1 && $option == $formLinks[0] && in_array($view, array('form', $formLinks[1])))
						{
							unset($translationTables[$tableKey]);
							break;
						}
					}
				}
			}
		}

		return $translationTables;
	}

	/**
	 * Add a filesystem path where Translation system should search for Params files.
	 * You may either pass a string or an array of paths.
	 *
	 * @param   mixed  $path  A filesystem path or array of filesystem paths to add.
	 *
	 * @return  array  An array of filesystem paths to find Params in.
	 *
	 * @since   1.0
	 */
	public static function addIncludePath($path = null)
	{
		// Convert the passed path(s) to add to an array.
		settype($path, 'array');

		// If we have new paths to add, do so.
		if (!empty($path))
		{
			// Check and add each individual new path.
			foreach ($path as $dir)
			{
				// Sanitize path.
				$dir = trim($dir);

				// Add to the front of the list so that custom paths are searched first.
				if (!in_array($dir, self::$includePaths))
				{
					array_unshift(self::$includePaths, $dir);
				}
			}
		}

		return self::$includePaths;
	}

	/**
	 * Loads form for Params field
	 *
	 * @param   array                       $column          Content element column
	 * @param   RTranslationContentElement  $contentElement  Content element
	 * @param   mixed                       $data            The data expected for the form.
	 * @param   string                      $controlName     Name of the form control group
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function loadParamsForm($column, $contentElement, $data, $controlName = '')
	{
		if (version_compare(JVERSION, '3.0', '<') && !empty($column['formname25']))
		{
			$formName = !empty($column['formname']) ? $column['formname25'] : $column['name'];
		}
		else
		{
			$formName = !empty($column['formname']) ? $column['formname'] : $column['name'];
		}

		// Handle the optional arguments.
		$options = array();
		$options['control'] = $controlName;
		$options['load_data'] = true;
		$formData = array();

		if (!empty($data->{$column['name']}))
		{
			$registry = new JRegistry;
			$registry->loadString($data->{$column['name']});
			$formData[$column['name']] = $registry->toArray();
		}

		// Load common and local language files.
		$lang = JFactory::getLanguage();

		// Load language file
		$lang->load($contentElement->extension, JPATH_BASE, null, false, false)
		|| $lang->load($contentElement->extension, JPATH_BASE . "/components/" . $contentElement->extension, null, false, false)
		|| $lang->load($contentElement->extension, JPATH_BASE, $lang->getDefault(), false, false)
		|| $lang->load($contentElement->extension, JPATH_BASE . "/components/" . $contentElement->extension, $lang->getDefault(), false, false);

		// Get the form.
		RForm::addFormPath(JPATH_BASE . '/components/' . $contentElement->extension . '/models/forms');
		RForm::addFormPath(JPATH_BASE . '/administrator/components/' . $contentElement->extension . '/models/forms');
		RForm::addFieldPath(JPATH_BASE . '/components/' . $contentElement->extension . '/models/fields');
		RForm::addFieldPath(JPATH_BASE . '/administrator/components/' . $contentElement->extension . '/models/fields');

		if (!empty($column['formpath']))
		{
			RForm::addFormPath(JPATH_BASE . $column['formpath']);
		}

		if (!empty($column['fieldpath']))
		{
			RForm::addFieldPath(JPATH_BASE . $column['fieldpath']);
		}

		$xpath = !empty($column['xpath']) ? $column['xpath'] : false;

		try
		{
			$form = RForm::getInstance('com_redcore.params_' . $column['name'] . $controlName, $formName, $options, false, $xpath);

			// Allow for additional modification of the form, and events to be triggered.
			// We pass the data because plugins may require it.
			self::preprocessForm($form, $data, 'content', $column, $contentElement);

			// Load the data into the form after the plugins have operated.
			$form->bind($formData);
		}
		catch (Exception $e)
		{
			return false;
		}

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param   JForm                       $form            A JForm object.
	 * @param   mixed                       $data            The data expected for the form.
	 * @param   string                      $group           The name of the plugin group to import (defaults to "content").
	 * @param   array                       $column          Content element column
	 * @param   RTranslationContentElement  $contentElement  Content element
	 *
	 * @return  void
	 *
	 * @see     JFormField
	 * @since   12.2
	 * @throws  Exception if there is an error in the form event.
	 */
	public static function preprocessForm(JForm $form, $data, $group = 'content', $column = array(), $contentElement = null)
	{
		if (strtolower($contentElement->name) == 'modules')
		{
			$form = self::preprocessFormModules($form, $data);
		}
		elseif (strtolower($contentElement->name) == 'menus')
		{
			$form = self::preprocessFormMenu($form, $data);
		}
		elseif (strtolower($contentElement->name) == 'plugins')
		{
			$form = self::preprocessFormPlugins($form, $data);
		}

		// Import the appropriate plugin group.
		JPluginHelper::importPlugin($group);

		// Get the dispatcher.
		$dispatcher = RFactory::getDispatcher();

		// Trigger the form preparation event.
		$results = $dispatcher->trigger('onContentPrepareForm', array($form, $data));

		// Check for errors encountered while preparing the form.
		if (count($results) && in_array(false, $results, true))
		{
			// Get the last error.
			$error = $dispatcher->getError();

			if (!($error instanceof Exception))
			{
				throw new Exception($error);
			}
		}
	}

	/**
	 * Method to preprocess the form for modules
	 *
	 * @param   JForm  $form  A form object.
	 * @param   mixed  $data  The data expected for the form.
	 *
	 * @return  JForm
	 *
	 * @since   1.6
	 * @throws  Exception if there is an error loading the form.
	 */
	public static function preprocessFormModules(JForm $form, $data)
	{
		jimport('joomla.filesystem.path');

		$lang     = JFactory::getLanguage();
		$clientId = $data->client_id;
		$module   = $data->module;

		$client   = JApplicationHelper::getClientInfo($clientId);
		$formFile = JPath::clean($client->path . '/modules/' . $module . '/' . $module . '.xml');

		// Load the core and/or local language file(s).
		$lang->load($module, $client->path, null, false, true)
		||	$lang->load($module, $client->path . '/modules/' . $module, null, false, true);

		if (file_exists($formFile))
		{
			// Get the module form.
			if (!$form->loadFile($formFile, false, '//config'))
			{
				throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
			}
		}

		return $form;
	}

	/**
	 * Method to preprocess the form for plugins
	 *
	 * @param   JForm  $form  A form object.
	 * @param   mixed  $data  The data expected for the form.
	 *
	 * @return  JForm
	 *
	 * @since   1.6
	 * @throws  Exception if there is an error loading the form.
	 */
	public static function preprocessFormPlugins(JForm $form, $data)
	{
		jimport('joomla.filesystem.path');

		$lang     = JFactory::getLanguage();
		$extension = 'plg_' . $data->folder . '_' . $data->element;

		$formFile = JPath::clean(JPATH_PLUGINS . '/' . $data->folder . '/' . $data->element . '/' . $data->element . '.xml');

		// Load the core and/or local language file(s).
		$lang->load(strtolower($extension), JPATH_ADMINISTRATOR, null, false, true)
			|| $lang->load(strtolower($extension), JPATH_PLUGINS . '/' . $data->folder . '/' . $data->element, null, false, true);
		$lang->load(strtolower($extension . '.sys'), JPATH_ADMINISTRATOR, null, false, true)
			|| $lang->load(strtolower($extension . '.sys'), JPATH_PLUGINS . '/' . $data->folder . '/' . $data->element, null, false, true);

		if (file_exists($formFile))
		{
			// Get the module form.
			if (!$form->loadFile($formFile, false, '//config'))
			{
				throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
			}
		}

		return $form;
	}

	/**
	 * Method to preprocess the form for modules
	 *
	 * @param   JForm  $form  A form object.
	 * @param   mixed  $data  The data expected for the form.
	 *
	 * @return  JForm
	 *
	 * @since   1.6
	 * @throws  Exception if there is an error loading the form.
	 */
	public static function preprocessFormMenu(JForm $form, $data)
	{
		jimport('joomla.filesystem.path');
		$link = $data->link;
		$type = $data->type;
		$formFile = false;

		// Initialise form with component view params if available.
		if ($type == 'component')
		{
			$link = htmlspecialchars_decode($link);

			// Parse the link arguments.
			$args = array();
			parse_str(parse_url(htmlspecialchars_decode($link), PHP_URL_QUERY), $args);

			// Confirm that the option is defined.
			$option = '';
			$base = '';

			if (isset($args['option']))
			{
				// The option determines the base path to work with.
				$option = $args['option'];
				$base = JPATH_SITE . '/components/' . $option;
			}

			// Confirm a view is defined.
			$formFile = false;

			if (isset($args['view']))
			{
				$view = $args['view'];

				// Determine the layout to search for.
				if (isset($args['layout']))
				{
					$layout = $args['layout'];
				}
				else
				{
					$layout = 'default';
				}

				$formFile = false;

				// Check for the layout XML file. Use standard xml file if it exists.
				$tplFolders = array(
					$base . '/views/' . $view . '/tmpl',
					$base . '/view/' . $view . '/tmpl'
				);
				$path = JPath::find($tplFolders, $layout . '.xml');

				if (is_file($path))
				{
					$formFile = $path;
				}

				// If custom layout, get the xml file from the template folder
				// template folder is first part of file name -- template:folder
				if (!$formFile && (strpos($layout, ':') > 0))
				{
					$temp = explode(':', $layout);
					$templatePath = JPATH::clean(JPATH_SITE . '/templates/' . $temp[0] . '/html/' . $option . '/' . $view . '/' . $temp[1] . '.xml');

					if (is_file($templatePath))
					{
						$formFile = $templatePath;
					}
				}
			}

			// Now check for a view manifest file
			if (!$formFile)
			{
				if (isset($view))
				{
					$metadataFolders = array(
						$base . '/view/' . $view,
						$base . '/views/' . $view
					);
					$metaPath = JPath::find($metadataFolders, 'metadata.xml');

					if (is_file($path = JPath::clean($metaPath)))
					{
						$formFile = $path;
					}
				}
				else
				{
					// Now check for a component manifest file
					$path = JPath::clean($base . '/metadata.xml');

					if (is_file($path))
					{
						$formFile = $path;
					}
				}
			}

			$lang     = JFactory::getLanguage();
			$lang->load($option, JPATH_BASE, null, false, false)
			|| $lang->load($option, JPATH_BASE . "/components/" . $option, null, false, false)
			|| $lang->load($option, JPATH_BASE, $lang->getDefault(), false, false)
			|| $lang->load($option, JPATH_BASE . "/components/" . $option, $lang->getDefault(), false, false);
		}

		if ($formFile)
		{
			// If an XML file was found in the component, load it first.
			// We need to qualify the full path to avoid collisions with component file names.

			if ($form->loadFile($formFile, true, '/metadata') == false)
			{
				throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
			}
		}

		// Now load the component params.
		// TODO: Work out why 'fixing' this breaks JForm
		if ($isNew = false)
		{
			$path = JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $option . '/config.xml');
		}
		else
		{
			$path = 'null';
		}

		if (is_file($path))
		{
			// Add the component params last of all to the existing form.
			if (!$form->load($path, true, '/config'))
			{
				throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
			}
		}

		// Load the specific type file
		if (!$form->loadFile('item_' . $type, false, false))
		{
			throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
		}

		return $form;
	}

	/**
	 * Method to reset plugin translation keys
	 *
	 * @return  void
	 */
	public static function resetPluginTranslation()
	{
		$user = JFactory::getUser();
		$levels = implode(',', $user->getAuthorisedViewLevels());

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('folder AS type, element AS name, params')
			->from('#__extensions')
			->where('enabled = 1')
			->where('type =' . $db->quote('plugin'))
			->where('state IN (0,1)')
			->where('access IN (' . $levels . ')')
			->order('ordering');

		$plugins = $db->setQuery($query)->loadObjectList();

		foreach ($plugins as $plugin)
		{
			$joomlaPlugin = JPluginHelper::getPlugin($plugin->type, $plugin->name);
			$joomlaPlugin->params = $plugin->params;
		}
	}

	/**
	 * Method to check if the current application instance is an administrator instance
	 * and that this is not an API call.
	 *
	 * @return bool true if this is admin and is not an API call
	 *
	 * @throws Exception
	 */
	public static function isAdmin()
	{
		$app = JFactory::getApplication();
		$isApi = ($app->input->get('api') != null);

		return ($app->isAdmin() && !$isApi);
	}

	/**
	 * Checks to see if the language exists and then load it
	 *
	 * @param   string  $language      Language name
	 * @param   bool    $loadLanguage  Loads the language if it exists
	 *
	 * @return  boolean  Returns true if language exists and we have switched to new language
	 */
	public static  function setLanguage($language, $loadLanguage = true)
	{
		$languageObject = JFactory::getLanguage();
		$languages = JLanguageHelper::getLanguages('sef');
		$languageKeys = explode('-', $language);

		if (!empty($languageKeys[0]) && !empty($languages[$languageKeys[0]]->lang_code))
		{
			JFactory::getApplication()->input->set('lang', $language);
			$languageObject->setLanguage($languages[$languageKeys[0]]->lang_code);

			if ($loadLanguage)
			{
				$languageObject->load();
			}

			return true;
		}

		return false;
	}

	/**
	 * Checks if the current page is a translatable form.
	 *
	 * @return void
	 */
	public static function isTranslatableForm()
	{
		$input = JFactory::getApplication()->input;
		$option = $input->getString('option', '');
		$view = $input->getString('view', '');
		$layout = $input->getString('layout', '');
		$task = $input->getString('task', '');

		$translationTables = self::getInstalledTranslationTables();

		if ($layout == 'edit' || $task == 'edit')
		{
			// Go through all installed translation tables
			foreach ($translationTables as $tableKey => $translationTable)
			{
				if (!empty($translationTable->formLinks))
				{
					// Go through all form links
					foreach ($translationTable->formLinks as $formLink)
					{
						$formLinks = explode('#', $formLink);

						// Check whether the form link values matches the current page
						if ($formLinks[0] == $option && $formLinks[1] == $view)
						{
							// Get id of item based on the name of the primary key gotten from the XML file.
							$itemid = $input->getInt($formLinks[2], '');

							// If the item doesn't have an ID, tell the user that they have to save the item first.
							if (empty($itemid))
							{
								self::renderTranslationModal(false, false, false);

								return;
							}

							if ($option == 'com_reditem')
							{
								// Call specific method for checking redITEM translation elements
								self::isReditemElementTranslatable($translationTable->table, $translationTable->primaryKeys, $itemid, $translationTable->name);
							}
							else
							{
								// Render modal button & window in the toolbar
								$linkname = JText::_('LIB_REDCORE_TRANSLATION_NAME_BUTTON') . ' ' . $translationTable->name;
								$contentelement = str_replace('#__', '', $translationTable->table);

								self::renderTranslationModal($itemid, $linkname, $contentelement);
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Checks if a translation element for redITEM should be translatable on the current page.
	 * It's necessary because of the way redITEM's custom fields are set up.
	 * If the database structure of the redITEM component changes, the code will have to be changed too.
	 *
	 * @param   string  $table        The database table of the translation element
	 * @param   array   $primaryKeys  The name of the columns of the translation element's reference IDs
	 * @param   int     $itemid       The id of the current item being shown
	 * @param   string  $elementName  Name of the translation element
	 *
	 * @return  void
	 */
	public static function isReditemElementTranslatable($table, $primaryKeys, $itemid, $elementName)
	{
		// Check whether there's a relation between the current item and the translation element
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		->select($db->qn($primaryKeys[0]))
		->from($db->qn($table))
		->where($db->qn($primaryKeys[0]) . '=' . $db->q($itemid));

		$db->setQuery($query);
		$results = $db->loadObjectList();

		// If there is, render a modal button & window in the toolbar
		if (!empty($results))
		{
			$linkname = JText::_('LIB_REDCORE_TRANSLATION_NAME_BUTTON') . ' ' . $elementName;
			$contentelement = str_replace('#__', '', $table);

			self::renderTranslationModal($itemid, $linkname, $contentelement);
		}
	}

	/**
	 * Renders a modal button & window for a translation element 
	 *
	 * @param   string  $itemid          The id of the current item being shown
	 * @param   array   $linkname        The text to be shown on the modal button
	 * @param   int     $contentelement  The current translation element
	 *                             
	 * @return  void
	 */
	public static function renderTranslationModal($itemid, $linkname, $contentelement)
	{
		echo RLayoutHelper::render(
			'modal.iframe-full-page',
			array(
				'options' => array(
					'id' => $itemid,
					'header' => '',
					'linkName' => $linkname,
					'link' => JRoute::_('index.php?option=com_redcore&view=translation&task=translation.edit&layout=modal-edit&contentelement='
										. $contentelement
										. '&id='
										. $itemid
										. '&tmpl=component'),
					'linkClass' => 'btn btn-primary',
				)
			)
		);
	}
}
