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
 * Plugin Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.0
 */
class RedcoreModelRplugin extends RModelAdmin
{
	/**
	 * Auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 */
	protected function populateState()
	{
		// Execute the parent method.
		parent::populateState();

		$app = JFactory::getApplication();

		// Element
		$element = $app->input->get('element');
		$this->setState('element', $element);

		// Folder
		$folder = $app->input->get('folder');
		$this->setState('folder', $folder);

		// Context
		$context = $app->input->get('context');
		$this->setState('context', $context);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

		/** @var RedcoreTableRplugin $table */
		$table = $this->getTable();

		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}

			$extensionId = $table->extension_id;

			/** @var JTableExtension $extensionTable */
			$extensionTable = JTable::getInstance('Extension', 'JTable');

			if ($extensionTable->load($extensionId))
			{
				$this->setError($extensionTable->getError());
				return false;
			}

			$element = $extensionTable->element;
			$folder = $extensionTable->folder;
		}

		else
		{
			$element = $this->getState('element');
			$folder = $this->getState('folder');
			$context = $this->getState('context');
			$extensionId = RPluginHelper::getPluginExtensionId($folder, $element);

			$table->load(
				array(
					'extension_id' => $extensionId,
					'context' => $context
				)
			);
		}

		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);
		$item = JArrayHelper::toObject($properties, 'JObject');

		if (property_exists($item, 'params'))
		{
			$registry = new JRegistry;
			$registry->loadString($item->params);
			$item->params = $registry->toArray();
		}

		// Get the plugin XML.
		$path = JPath::clean(JPATH_PLUGINS . '/' . $folder . '/' . $element . '/' . $element . '.xml');

		if (file_exists($path))
		{
			$item->xml = simplexml_load_file($path);
		}
		else
		{
			$item->xml = null;
		}

		return $item;
	}

	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param   JForm  $form   A JForm object.
	 * @param   mixed  $data   The data expected for the form.
	 * @param   string $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @throws  Exception if there is an error in the form event.
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		jimport('joomla.filesystem.path');

		$folder = $this->getState('folder');
		$element = $this->getState('element');
		$lang = JFactory::getLanguage();

		// Load the core and/or local language sys file(s) for the ordering field.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('element')
			->from('#__extensions')
			->where('type = ' . $db->q('plugin'))
			->where('folder= ' . $db->q($folder));

		$db->setQuery($query);
		$elements = $db->loadColumn();

		foreach ($elements as $elementa)
		{
			$lang->load('plg_' . $folder . '_' . $elementa . '.sys', JPATH_ADMINISTRATOR, null, false, false)
			|| $lang->load('plg_' . $folder . '_' . $elementa . '.sys', JPATH_PLUGINS . '/' . $folder . '/' . $elementa, null, false, false)
			|| $lang->load('plg_' . $folder . '_' . $elementa . '.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
			|| $lang->load('plg_' . $folder . '_' . $elementa . '.sys', JPATH_PLUGINS . '/' . $folder . '/' . $elementa, $lang->getDefault(), false, false);
		}

		if (empty($folder) || empty($element))
		{
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_('index.php?option=com_redcore', false));
		}

		$formFile = JPath::clean(JPATH_PLUGINS . '/' . $folder . '/' . $element . '/' . $element . '.xml');
		if (!file_exists($formFile))
		{
			throw new Exception(JText::sprintf('COM_PLUGINS_ERROR_FILE_NOT_FOUND', $element . '.xml'));
		}

		// Load the core and/or local language file(s).
		$lang->load('plg_' . $folder . '_' . $element, JPATH_ADMINISTRATOR, null, false, false)
		|| $lang->load('plg_' . $folder . '_' . $element, JPATH_PLUGINS . '/' . $folder . '/' . $element, null, false, false)
		|| $lang->load('plg_' . $folder . '_' . $element, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		|| $lang->load('plg_' . $folder . '_' . $element, JPATH_PLUGINS . '/' . $folder . '/' . $element, $lang->getDefault(), false, false);

		if (file_exists($formFile))
		{
			// Get the plugin form.
			if (!$form->loadFile($formFile, false, '//config'))
			{
				throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
			}
		}

		// Attempt to load the xml file.
		if (!$xml = simplexml_load_file($formFile))
		{
			throw new Exception(JText::_('JERROR_LOADFILE_FAILED'));
		}

		// Get the help data from the XML file if present.
		$help = $xml->xpath('/extension/help');
		if (!empty($help))
		{
			$helpKey = trim((string) $help[0]['key']);
			$helpURL = trim((string) $help[0]['url']);

			$this->helpKey = $helpKey ? $helpKey : $this->helpKey;
			$this->helpURL = $helpURL ? $helpURL : $this->helpURL;
		}

		// Trigger the default form events.
		parent::preprocessForm($form, $data, $group);
	}
}
