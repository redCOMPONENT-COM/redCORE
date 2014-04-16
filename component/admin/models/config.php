<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Config Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.0
 */
class RedcoreModelConfig extends RModelAdmin
{
	/**
	 * Method to get a form object.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$option = JFactory::getApplication()->input->getString('component');

		// Add the search path for the admin component config.xml file.
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/' . $option);

		// Get the form.
		/** @var RForm $form */
		$form = $this->loadForm(
			'com_redcore.config',
			'config',
			array('control' => 'jform', 'load_data' => $loadData),
			false,
			'/config'
		);

		$form->loadFile('translations', false, '/config');

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Get the associated JTable
	 *
	 * @param   string  $name    Table name
	 * @param   string  $prefix  Table prefix
	 * @param   array   $config  Configuration array
	 *
	 * @return  JTable
	 */
	public function getTable($name = 'Extension', $prefix = 'JTable', $config = array())
	{
		return parent::getTable($name, $prefix, $config);
	}

	/**
	 * Get the component information.
	 *
	 * @return  object
	 */
	public function getComponent()
	{
		$option = JFactory::getApplication()->input->getString('component');

		$this->loadExtensionLanguage($option, $option);
		$this->loadExtensionLanguage($option, $option . '.sys');

		$component = JComponentHelper::getComponent($option);

		$component->xml = RComponentHelper::getComponentManifestFile($option);

		return $component;
	}

	/**
	 * Load specific language file.
	 *
	 * @param   string  $option         Option name
	 * @param   string  $extensionFile  Extension File Name
	 *
	 * @return  object
	 */
	public function loadExtensionLanguage($option, $extensionFile)
	{
		// Load common and local language files.
		$lang = JFactory::getLanguage();

		// Load language file
		$lang->load($extensionFile, JPATH_BASE, null, false, false)
		|| $lang->load($extensionFile, JPATH_BASE . "/components/$option", null, false, false)
		|| $lang->load($extensionFile, JPATH_BASE, $lang->getDefault(), false, false)
		|| $lang->load($extensionFile, JPATH_BASE . "/components/$option", $lang->getDefault(), false, false);
	}

	/**
	 * Method to save the configuration data.
	 *
	 * @param   array  $data  An array containing all global config data.
	 *
	 * @return  bool   True on success, false on failure.
	 */
	public function save($data)
	{
		$dispatcher = RFactory::getDispatcher();
		$table = JTable::getInstance('Extension');
		$isNew = true;

		// Save the rules.
		if (isset($data['params']) && isset($data['params']['rules']))
		{
			$rules = new JAccessRules($data['params']['rules']);
			$asset = JTable::getInstance('asset');

			if (!$asset->loadByName($data['option']))
			{
				$root = JTable::getInstance('asset');
				$root->loadByName('root.1');
				$asset->name = $data['option'];
				$asset->title = $data['option'];
				$asset->setLocation($root->id, 'last-child');
			}

			$asset->rules = (string) $rules;

			if (!$asset->check() || !$asset->store())
			{
				$this->setError($asset->getError());

				return false;
			}

			// We don't need this anymore
			unset($data['option']);
			unset($data['params']['rules']);
		}

		// Load the previous Data
		if (!$table->load($data['id']))
		{
			$this->setError($table->getError());

			return false;
		}

		unset($data['id']);

		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());

			return false;
		}

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		// Trigger the onConfigurationBeforeSave event.
		$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, $table, $isNew));

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());

			return false;
		}

		// Store the data.
		if (!$table->store())
		{
			$this->setError($table->getError());

			return false;
		}

		// Clean the component cache.
		$this->cleanCache('_system');

		// Trigger the onConfigurationAfterSave event.
		$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew));

		return true;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The default data is an empty array.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState(
			$this->context . '.data',
			array()
		);

		if (empty($data))
		{
			$option = JFactory::getApplication()->input->getString('component');

			if ($option)
			{
				$table = JTable::getInstance('Extension');

				if ($table->load(array('element' => $option)))
				{
					$data = $this->getItem($table->extension_id);

					$data = $data->params;
				}
			}
		}

		return $data;
	}

	/**
	 * Gets Installed extensions
	 *
	 * @param   string  $extensionType      Extension type
	 * @param   array   $extensionElements  Extension element search type
	 * @param   string  $extensionFolder    Folder user when searching for plugin
	 *
	 * @return  array  List of objects
	 */
	public function getInstalledExtensions($extensionType = 'module', $extensionElements = array('%redcore%'), $extensionFolder = 'redcore')
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select('e.name, e.type, e.element')
			->from('#__extensions AS e')
			->where('e.type = ' . $db->q($extensionType))
			->where('e.client_id = 0')
			->order('e.name');

		if ($extensionType == 'module')
		{
			$query->leftJoin('#__modules as m ON m.module = e.element')
				->select('m.published as enabled')
				->group('e.element');
		}
		else
		{
			$query->select('e.enabled');
		}

		foreach ($extensionElements as $key => $extensionElement)
		{
			$extensionElements[$key] = 'e.element LIKE ' . $db->quote($extensionElement);
		}

		$query->where(implode(' OR ', $extensionElements) . ($extensionType == 'plugin' ? ' OR e.folder = ' . $db->q($extensionFolder) : ''));
		$db->setQuery($query);

		$extensions = $db->loadObjectList();

		return $extensions;
	}

	/**
	 * Loading of related XML files
	 *
	 * @param   string  $extensionName  Extension name
	 *
	 * @return  array  List of objects
	 */
	public function loadContentElements($extensionName = '')
	{
		return RTranslationHelper::getContentElements($extensionName);
	}

	/**
	 * Loading of related XML files
	 *
	 * @param   string  $extensionName    Extension name
	 * @param   array   $contentElements  Content elements
	 *
	 * @return  array  List of objects
	 */
	public function loadMissingContentElements($extensionName, $contentElements = array())
	{
		$translationTables = RTranslationHelper::getInstalledTranslationTables();
		$missingTables = array();

		foreach ($translationTables as $translationTableKey => $translationTable)
		{
			$translationTable->table = str_replace('#__', '', $translationTable->table);

			if ($translationTable->option == $extensionName)
			{
				$foundTable = false;

				foreach ($contentElements as $contentElement)
				{
					if (!empty($contentElement->table) && $contentElement->table == $translationTable->table)
					{
						$foundTable = true;
						break;
					}
				}

				if (!$foundTable)
				{
					$missingTables[$translationTableKey] = $translationTable;
				}
			}
		}

		return $missingTables;
	}
}
