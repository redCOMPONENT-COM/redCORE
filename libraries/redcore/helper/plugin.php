<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Helpers
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Plugin Helper file
 *
 * @package     Redcore.Backend
 * @subpackage  Helpers
 * @since       1.0
 */
class RHelperPlugin extends JObject
{
	/**
	 * The extension/plugin id in the #__extensions table
	 *
	 * @var  integer
	 */
	public $extensionId = null;

	/**
	 * The extension/plugin Folder
	 *
	 * @var  String
	 */
	public $extensionFolder = null;

	/**
	 * The extension/plugin Element name
	 *
	 * @var  String
	 */
	public $extensionElement = null;

	/**
	 * The Application Name is used to fetch specific application related data and to prepare data for save in specific format
	 * applicationName is set to default URI option value if not provided
	 *
	 * @var  String
	 */
	public $applicationName = null;

	/**
	 * Plugin data from XML file
	 *
	 * @var  JRegistry
	 */
	public $xmlData = null;

	/**
	 * Plugin parameters
	 *
	 * @var  JRegistry
	 */
	public $params = null;

	/**
	 * Custom Plugin configuration options from field custom_data
	 *
	 * @var  JRegistry
	 */
	public $configurationData = null;

	/**
	 * Plugin item data
	 *
	 * @var  Object
	 */
	public $item = null;

	/**
	 * Constructor
	 *
	 * @param   int     $extensionId       Extension Id
	 * @param   string  $extensionFolder   Extension Folder name
	 * @param   string  $extensionElement  Extension Element name
	 * @param   null    $applicationName   Application name will set to URI option value if not provided
	 *
	 * @internal param string $subject Subject
	 * @internal param array $config Configuration
	 *
	 */
	public function __construct($extensionId = 0, $extensionFolder = '', $extensionElement = '', $applicationName = null)
	{
		$app = JFactory::getApplication();
		$this->extensionId = (int) $extensionId;
		$this->extensionFolder = $extensionFolder;
		$this->extensionElement = $extensionElement;
		$this->applicationName = $applicationName;

		if (!isset($this->applicationName) || $this->applicationName == '')
		{
			$this->applicationName = $app->input->get('option', 'com_redshop', 'cmd');
		}

		if ($this->extensionId == 0 && ($this->extensionFolder == '' || $this->extensionElement == ''))
		{
			return;
		}

		$this->getPluginData();
	}

	/**
	 * Load Plugin Item from Extensions table
	 *
	 * @param   int  $extensionId  Extension ID which will be used to load Extension Item
	 *
	 * @return mixed
	 */
	public function loadPluginItem($extensionId = 0)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__extensions'));

		if ($extensionId > 0)
		{
			$query->where($db->quoteName('extension_id') . ' = ' . $extensionId);
		}
		elseif ($this->extensionId > 0)
		{
			$query->where($db->quoteName('extension_id') . ' = ' . $this->extensionId);
		}
		else
		{
			$query->where($db->quoteName('folder') . ' = ' . $db->quote($this->extensionFolder))
				->where($db->quoteName('element') . ' = ' . $db->quote($this->extensionElement));
		}

		$db->setQuery($query);
		$this->item = $db->loadObject();

		return $this->item;
	}

	/**
	 * Set up variables for easy use in Plugin calls
	 *
	 * @param   int  $extensionId  Extension ID which will be used to load Extension Item if it is not loaded already
	 *
	 * @return  object  The plugin / extension data
	 */
	public function getPluginData($extensionId = 0)
	{
		if (!isset($this->item))
		{
			$this->loadPluginItem($extensionId);
		}

		if (isset($this->item))
		{
			$this->extensionId = $this->item->extension_id;
			$this->extensionFolder = $this->item->folder;
			$this->extensionElement = $this->item->element;

			$this->xmlData = new JRegistry;
			$this->xmlData->loadString($this->item->manifest_cache);
			$this->item->xmlData = $this->xmlData;

			$this->params = new JRegistry;
			$this->params->loadString($this->item->params);
			$this->item->params = $this->params;

			$this->configurationData = new JRegistry;
			$this->configurationData->loadString($this->item->custom_data);
			$this->configurationData = $this->configurationData->get($this->applicationName, null);

			if (!$this->configurationData)
			{
				$this->configurationData = $this->getDefaultFormValues();
			}

			$this->item->configurationData = $this->configurationData;
		}

		return $this->item;
	}

	/**
	 * This function will load default values from Plugin form xml file
	 *
	 * @return JRegistry
	 */
	protected function getDefaultFormValues()
	{
		$configurationData = new JRegistry;

		if (JFile::exists(JPATH_SITE . '/plugins/' . $this->item->folder . '/' . $this->item->element . '/forms/configuration.xml'))
		{
			$form = JForm::getInstance(
				'configuration',
				JPATH_SITE . '/plugins/' . $this->item->folder . '/' . $this->item->element . '/forms/configuration.xml'
			);

			if (isset($form))
			{
				$fields = array();

				foreach ($form->getFieldsets() as $name => $fieldset)
				{
					foreach ($form->getFieldset($name) as $field)
					{
						$fields[$field->fieldname] = $field->value;
					}
				}

				$configurationData->loadArray($fields);
			}
		}

		return $configurationData;
	}

	/**
	 * Loads the plugin language file
	 *
	 * @param   string  $extension  The extension for which a language file should be loaded
	 * @param   string  $basePath   The basepath to use
	 *
	 * @return  boolean  True, if the file has successfully loaded.
	 *
	 * @since   11.1
	 */
	public function loadLanguage($extension = '', $basePath = JPATH_ADMINISTRATOR)
	{
		if (empty($extension))
		{
			$extension = 'plg_' . $this->item->folder . '_' . $this->item->element;
		}

		$lang = JFactory::getLanguage();

		return $lang->load(strtolower($extension), $basePath, null, false, false)
		|| $lang->load(strtolower($extension), JPATH_PLUGINS . '/' . $this->item->folder . '/' . $this->item->element, null, false, false)
		|| $lang->load(strtolower($extension), $basePath, $lang->getDefault(), false, false)
		|| $lang->load(strtolower($extension), JPATH_PLUGINS . '/' . $this->item->folder . '/' . $this->item->element, $lang->getDefault(), false, false);
	}

	/**
	 * Return form with loaded additional configuration options for the plugin
	 *
	 * @param   JForm  $form  Form object
	 *
	 * @return mixed
	 */
	public function getConfigurationForm($form)
	{
		JForm::addFieldPath(JPATH_SITE . '/plugins/' . $this->item->folder . '/' . $this->item->element . '/fields');

		if (JFile::exists(JPATH_SITE . '/plugins/' . $this->item->folder . '/' . $this->item->element . '/forms/configuration.xml'))
		{
			$form->loadFile(JPATH_SITE . '/plugins/' . $this->item->folder . '/' . $this->item->element . '/forms/configuration.xml', false);

			if (isset($this->configurationData))
			{
				foreach ($this->configurationData as $key => $val)
				{
					$form->setValue($key, 'configuration', $val);
				}
			}
		}

		return $form;
	}

	/**
	 * Return form with loaded additional options for the plugin
	 *
	 * @param   Array  $data  Filtered Array of posted values
	 *
	 * @internal param \JForm $form Form object
	 *
	 * @return mixed
	 */
	public function setConfigurationDataForSave($data)
	{
		if (!isset($this->item) || !isset($data['extension_id']) || (int) $data['extension_id'] == 0)
		{
			return $data;
		}

		$app = JFactory::getApplication();
		$postData = $app->input->get('jform', array(), 'array');

		if (isset($postData['configuration']) && is_array($postData['configuration']))
		{
			$itemConfiguration = json_decode($this->item->custom_data, true);
			$itemConfiguration[$this->applicationName] = $postData['configuration'];
			$registry = new JRegistry;
			$registry->loadArray($itemConfiguration);
			$data['custom_data'] = (string) $registry;
		}

		return $data;
	}
}
