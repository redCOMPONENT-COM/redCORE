<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
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

		if (empty($form))
		{
			/** @var RForm $form */
			$form = $this->loadForm(
				'com_redcore.translations',
				'config',
				array('control' => 'jform', 'load_data' => $loadData),
				false,
				'/config'
			);
		}
		else
		{
			$form->loadFile('translations', false, '/config');
		}

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
	public function getTable($name = null, $prefix = '', $config = array())
	{
		$name = !empty($name) ? $name : 'Extension';
		$prefix = !empty($prefix) ? $prefix : 'JTable';

		return parent::getTable($name, $prefix, $config);
	}

	/**
	 * Get the component information.
	 *
	 * @param   string  $option  Option name
	 *
	 * @return  object
	 */
	public function getComponent($option)
	{
		$this->loadExtensionLanguage($option, $option);
		$this->loadExtensionLanguage($option, $option . '.sys');
		$component = JComponentHelper::getComponent($option);
		$component->option = $option;
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
		$option = JFactory::getApplication()->input->getString('component');
		$isNew = true;

		// Save the rules.
		if (isset($data['params']) && isset($data['params']['rules']))
		{
			$rules = new JAccessRules($data['params']['rules']);
			$asset = JTable::getInstance('asset');

			if (!$asset->loadByName($option))
			{
				$root = JTable::getInstance('asset');
				$root->loadByName('root.1');
				$asset->name = $option;
				$asset->title = $option;
				$asset->setLocation($root->id, 'last-child');
			}

			$asset->rules = (string) $rules;

			if (!$asset->check() || !$asset->store())
			{
				$this->setError($asset->getError());

				return false;
			}

			// We don't need this anymore
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

				if ($table->load(array('element' => $option, 'type' => 'component')))
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
	 * @param   string   $extensionType      Extension type
	 * @param   array    $extensionElements  Extension element search type
	 * @param   array    $extensionFolder    Folder user when searching for plugin
	 * @param   boolean  $loadLanguage       Load language file for that extension
	 *
	 * @return  array  List of objects
	 */
	public function getInstalledExtensions(
		$extensionType = 'module',
		$extensionElements = array('%redcore%'),
		$extensionFolder = array('redcore'),
		$loadLanguage = true)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select('e.name, e.type, e.element')
			->from('#__extensions AS e')
			->where('e.type = ' . $db->q($extensionType))
			->where('e.client_id = 0')
			->order('e.name');

		$folders = is_array($extensionFolder) ? $extensionFolder : array($extensionFolder);
		$isRedCore = false;

		foreach ($folders as $key => $folder)
		{
			$folders[$key] = $db->q($folder);

			if ($folder == 'redcore')
			{
				$isRedCore = true;
			}
		}

		if ($isRedCore)
		{
			$folders[] = $db->q('redpayment');
		}

		if ($extensionType == 'module')
		{
			$query->leftJoin('#__modules as m ON m.module = e.element')
				->select('m.published as enabled, m.module as moduleName')
				->group('e.element');
		}
		else
		{
			$query->select('e.enabled, e.folder');
		}

		$elements = array();

		foreach ($extensionElements as $group => $extensionElement)
		{
			if ($extensionType == 'plugin')
			{
				$elements[] = '(e.element LIKE ' . $db->q($extensionElement) . ' AND e.folder = ' . $db->quote($group) . ')';
			}
			else
			{
				$elements[] = 'e.element LIKE ' . $db->q($extensionElement);
			}
		}

		$extensionsSearch = implode(' OR ', $elements);

		if (!empty($elements) && $extensionType == 'plugin')
		{
			$extensionsSearch .= ' OR ';
		}

		$query->where('(' . $extensionsSearch . ($extensionType == 'plugin' ? ' e.folder IN (' . implode(',', $folders) . ')' : '') . ')');
		$db->setQuery($query);

		$extensions = $db->loadObjectList();

		if ($loadLanguage && !empty($extensions))
		{
			// Load common and local language files.
			$lang = JFactory::getLanguage();

			foreach ($extensions as $extension)
			{
				if ($extensionType == 'plugin')
				{
					$extensionName = strtolower('plg_' . $extension->folder . '_' . $extension->element);
					$lang->load(strtolower($extensionName), JPATH_ADMINISTRATOR, null, false, true)
					|| $lang->load(strtolower($extensionName), JPATH_PLUGINS . '/' . $extension->folder . '/' . $extension->element, null, false, true);
				}
				else
				{
					$lang->load($extension->moduleName, JPATH_SITE, null, false, true) ||
					$lang->load($extension->moduleName,  JPATH_SITE . "/modules/" . $extension->moduleName, null, false, true);
				}
			}
		}

		return $extensions;
	}
}
