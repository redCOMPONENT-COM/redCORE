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
			$db           = JFactory::getDbo();
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
	 * Get list of all translation tables with columns. It is here for backward compatibility below version 1.8.3
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function getInstalledTranslationTables()
	{
		return RTranslationTable::getInstalledTranslationTables();
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
		$input  = JFactory::getApplication()->input;
		$option = $input->getString('option', '');
		$view   = $input->getString('view', '');
		$layout = $input->getString('layout', '');
		$task   = $input->getString('layout', '');

		if ($layout == 'edit' || $task == 'edit')
		{
			foreach ($translationTables as $tableKey => $translationTable)
			{
				if (!empty($translationTable->formLinks))
				{
					foreach ($translationTable->formLinks as $formLink)
					{
						$id = $input->getString($formLink['identifier'], '');

						if ($option == $formLink['option'] && $view == $formLink['view'] && $layout == $formLink['layout'] && $id)
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
	 * @param   array                       $column            Content element column
	 * @param   RTranslationContentElement  $translationTable  Translation table
	 * @param   mixed                       $data              The data expected for the form.
	 * @param   string                      $controlName       Name of the form control group
	 * @param   string                      $basepath          Base path to use when loading files
	 *
	 * @return  array  Array or table with columns columns
	 */
	public static function loadParamsForm($column, $translationTable, $data, $controlName = '', $basepath = JPATH_BASE)
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
		$options              = array();
		$options['control']   = $controlName;
		$options['load_data'] = true;
		$formData             = array();

		if (!empty($data->{$column['name']}))
		{
			$registry = new JRegistry;
			$registry->loadString($data->{$column['name']});
			$formData[$column['name']] = $registry->toArray();
		}

		// Load common and local language files.
		$lang = JFactory::getLanguage();

		// Load language file
		$lang->load($translationTable->extension_name, $basepath, null, false, false)
		|| $lang->load($translationTable->extension_name, $basepath . "/components/" . $translationTable->extension_name, null, false, false)
		|| $lang->load($translationTable->extension_name, $basepath, $lang->getDefault(), false, false)
		|| $lang->load(
			$translationTable->extension_name, $basepath . "/components/" . $translationTable->extension_name, $lang->getDefault(), false, false
		);

		// Get the form.
		RForm::addFormPath($basepath . '/components/' . $translationTable->extension_name . '/models/forms');
		RForm::addFormPath($basepath . '/administrator/components/' . $translationTable->extension_name . '/models/forms');
		RForm::addFieldPath($basepath . '/components/' . $translationTable->extension_name . '/models/fields');
		RForm::addFieldPath($basepath . '/administrator/components/' . $translationTable->extension_name . '/models/fields');

		if (!empty($column['formpath']))
		{
			RForm::addFormPath($basepath . $column['formpath']);
		}

		if (!empty($column['fieldpath']))
		{
			RForm::addFieldPath($basepath . $column['fieldpath']);
		}

		$xpath = !empty($column['xpath']) ? $column['xpath'] : false;

		try
		{
			$form = RForm::getInstance('com_redcore.params_' . $column['name'] . $controlName, $formName, $options, false, $xpath);

			// Allow for additional modification of the form, and events to be triggered.
			// We pass the data because plugins may require it.
			self::preprocessForm($form, $data, 'content', $column, $translationTable);

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
	 * @param   JForm                          $form              A JForm object.
	 * @param   mixed                          $data              The data expected for the form.
	 * @param   string                         $group             The name of the plugin group to import (defaults to "content").
	 * @param   array                          $column            Content element column
	 * @param   RedcoreTableTranslation_Table  $translationTable  Translation table
	 *
	 * @return  void
	 *
	 * @see     JFormField
	 * @since   12.2
	 * @throws  Exception if there is an error in the form event.
	 */
	public static function preprocessForm(JForm $form, $data, $group = 'content', $column = array(), $translationTable = null)
	{
		$tableName = str_replace('#__', '', $translationTable->name);

		if ($tableName == 'modules')
		{
			$form = self::preprocessFormModules($form, $data);
		}
		elseif ($tableName == 'menus')
		{
			$form = self::preprocessFormMenu($form, $data);
		}
		elseif ($tableName == 'plugins')
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

		$lang      = JFactory::getLanguage();
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
		$link     = $data->link;
		$type     = $data->type;
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
			$base   = '';

			if (isset($args['option']))
			{
				// The option determines the base path to work with.
				$option = $args['option'];
				$base   = JPATH_SITE . '/components/' . $option;
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
				$path       = JPath::find($tplFolders, $layout . '.xml');

				if (is_file($path))
				{
					$formFile = $path;
				}

				// If custom layout, get the xml file from the template folder
				// template folder is first part of file name -- template:folder
				if (!$formFile && (strpos($layout, ':') > 0))
				{
					$temp         = explode(':', $layout);
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
					$metaPath        = JPath::find($metadataFolders, 'metadata.xml');

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

			$lang = JFactory::getLanguage();
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
		$user   = JFactory::getUser();
		$levels = implode(',', $user->getAuthorisedViewLevels());

		$db    = JFactory::getDbo();
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
			$joomlaPlugin         = JPluginHelper::getPlugin($plugin->type, $plugin->name);
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
		$app   = JFactory::getApplication();
		$isApi = ($app->input->get('api') != null);

		return ((version_compare(JVERSION, '3.7', '<') ? $app->isAdmin() : $app->isClient('administrator')) && !$isApi);
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
		$languages    = JLanguageHelper::getLanguages('sef');
		$languageKeys = explode('-', $language);

		if (!empty($languageKeys[0]) && !empty($languages[$languageKeys[0]]->lang_code))
		{
			JFactory::getApplication()->input->set('lang', $language);
			$languageObject = new JLanguage($languages[$languageKeys[0]]->lang_code);

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
	 * @param   bool  $isAdmin  Informs us whether we are on frontend or backend
	 *
	 * @return  void
	 */
	public static function isTranslatableForm($isAdmin)
	{
		// Current page values
		$input  = JFactory::getApplication()->input;
		$option = $input->getString('option', '');
		$view   = $input->getString('view', '');
		$layout = $input->getString('layout', '');
		$task   = $input->getString('task', '');

		$translationTables = self::getInstalledTranslationTables();

		foreach ($translationTables as $tableKey => $translationTable)
		{
			if (!isset($translationTable->formLinks))
			{
				continue;
			}

			foreach ($translationTable->formLinks as $formLink)
			{
				// Form values
				$tableAdmin     = !empty($formLink['admin']) ? $formLink['admin'] : 'false';
				$tableOption    = $formLink['option'];
				$tableView      = $formLink['view'];
				$tableLayout    = !empty($formLink['layout']) ? $formLink['layout'] : 'edit';
				$tableID        = isset($formLink['identifier']) ? $formLink['identifier'] : 'id';
				$showButton     = !empty($formLink['showbutton']) ? $formLink['showbutton'] : 'true';
				$htmlposition   = !empty($formLink['htmlposition']) ? $formLink['htmlposition'] : '.btn-toolbar:first';
				$checkPrimaryId = !empty($formLink['checkoriginalid']) ? $formLink['checkoriginalid'] : 'false';
				$results        = null;

				// Check if the form's frontend/backend options matches the current page
				$tableAdmin = $tableAdmin === 'true' ? true : false;

				if ($isAdmin != $tableAdmin)
				{
					continue;
				}

				// Check whether form values matches the current page
				if ($option == $tableOption && $view == $tableView && $layout == $tableLayout)
				{
					// Get id of item based on the form identifier.
					$itemID = $input->getInt($tableID, '');

					// If the item doesn't have an ID, tell the user that they have to save the item first.
					if (empty($itemID))
					{
						self::renderTranslationModal(false, false, false, false);

						return;
					}

					if ($checkPrimaryId == 'true')
					{
						// Check whether there's a relation between the current item and the translation element
						$db    = JFactory::getDbo();
						$query = $db->getQuery(true)
							->select($db->qn($translationTable->primaryKeys[0]))
							->from($db->qn($translationTable->table))
							->where($db->qn($translationTable->primaryKeys[0]) . '=' . $db->q($itemID));

						$db->setQuery($query);
						$results = $db->loadObjectList();

						$checkPrimaryId = !empty($results) ? 'false' : 'true';
					}

					// If there is, render a modal button & window in the toolbar
					if ($checkPrimaryId == 'false' && $showButton == 'true')
					{
						$linkname       = JText::_('LIB_REDCORE_TRANSLATION_NAME_BUTTON') . ' ' . $translationTable->title;
						$contentelement = str_replace('#__', '', $translationTable->table);

						self::renderTranslationModal($itemID, $linkname, $contentelement, $htmlposition);
					}
				}
			}
		}
	}

	/**
	 * Renders a modal button & window for a translation element
	 *
	 * @param   string  $itemID          The id of the current item being shown
	 * @param   array   $linkname        The text to be shown on the modal button
	 * @param   int     $contentelement  The current translation element
	 * @param   string  $htmlposition    The position on the page where the button should be moved to
	 *
	 * @return  void
	 */
	public static function renderTranslationModal($itemID, $linkname, $contentelement, $htmlposition)
	{
		echo RLayoutHelper::render(
			'modal.iframe-full-page',
			array(
				'id' => $itemID,
				'header' => '',
				'linkName' => $linkname,
				'link' => JRoute::_('index.php?option=com_redcore&view=translation&task=translation.display&layout=modal-edit&translationTableName='
									. $contentelement
									. '&id='
									. $itemID
					. '&tmpl=component'
				),
				'linkClass' => 'btn btn-primary',
				'contentElement' => $contentelement,
				'htmlposition' => $htmlposition,
			)
		);
	}

	/**
	 * Gets translation item status
	 *
	 * @param   object  $item     Translate item object
	 * @param   array   $columns  List of columns used in translation
	 *
	 * @return  string  Translation Item status
	 */
	public static function getTranslationItemStatus($item, $columns)
	{
		if (empty($item->rctranslations_language))
		{
			return array('badge' => 'label label-danger', 'status' => 'JNONE');
		}
		elseif ($item->rctranslations_state != 1)
		{
			return array('badge' => 'label label-danger', 'status' => 'JUNPUBLISHED');
		}
		else
		{
			$originalValues = new JRegistry;

			if (is_array($item->rctranslations_originals))
			{
				$originalValues->loadArray($item->rctranslations_originals);
			}
			else
			{
				$originalValues->loadString((string) $item->rctranslations_originals);
			}

			$translationStatus = array('badge' => 'label label-success', 'status' => 'COM_REDCORE_TRANSLATIONS_STATUS_TRANSLATED');

			foreach ($columns as $column)
			{
				if (md5($item->$column) != $originalValues->get($column))
				{
					$translationStatus = array('badge' => 'label label-warning', 'status' => 'COM_REDCORE_TRANSLATIONS_STATUS_CHANGED');
					break;
				}
			}

			return $translationStatus;
		}
	}

	/**
	 * Gets translation item id and returns it
	 *
	 * @param   int     $itemid                Item id
	 * @param   string  $langCode              Language code
	 * @param   string  $pk                    Primary key name
	 * @param   string  $translationTableName  Name of the translation table
	 *
	 * @return  int     Translations item id
	 */
	public static function getTranslationItemId($itemid, $langCode, $pk, $translationTableName)
	{
		$ids = explode('###', $itemid);

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('rctranslations_id')
			->from($db->qn(RTranslationTable::getTranslationsTableName($translationTableName, '#__')))
			->where('rctranslations_language=' . $db->q($langCode));

		foreach ($pk as $key => $primaryKey)
		{
			$query->where($db->qn($primaryKey) . ' = ' . $db->q($ids[$key]));
		}

		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Checks if an array of data has any data
	 *
	 * @param   array  $data      Array of data to be checked
	 * @param   array  $excludes  Array of keys to be excluded from validation
	 *
	 * @return  boolean  True if the array contains data
	 */
	public static function validateEmptyTranslationData($data, $excludes = null)
	{
		// Remove excluded keys from array
		foreach ($excludes as $exclude)
		{
			unset($data[$exclude]);
		}

		// Check if the rest of the keys in the array are empty
		if (array_filter($data, 'strlen'))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Adds array to a JForm input
	 *
	 * @param   string  $form   Form to be modified
	 * @param   string  $index  Index of the array
	 *
	 * @return  string  Modified form
	 */
	public static function arrayifyTranslationJForm($form, $index)
	{
		$pattern     = '/name="jform/';
		$replacement = 'name="jform[' . $index . ']';
		$form        = preg_replace($pattern, $replacement, $form);

		return $form;
	}

	/**
	 * Returns an array of all content language codes (fx. en-GB)
	 *
	 * @return array  All content language codes
	 */
	public static function getAllContentLanguageCodes()
	{
		$contentLanguages = JLanguageHelper::getLanguages();

		$languageCodes = array();

		foreach ($contentLanguages as $language)
		{
			$languageCodes[] = $language->lang_code;
		}

		return $languageCodes;
	}
}
