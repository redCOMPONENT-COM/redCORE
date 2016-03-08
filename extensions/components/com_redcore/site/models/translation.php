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
 * Translation Model
 *
 * @package     Redcore.Frontend
 * @subpackage  Models
 * @since       1.0
 */
class RedcoreModelTranslation extends RModelAdmin
{
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 */
	public function getItem($pk = null, $tid = null)
	{
		$ids = JFactory::getApplication()->input->getString('id', '');
		$id = JFactory::getApplication()->input->getString('rctranslations_id', '');
		$table = RedcoreHelpersTranslation::getTranslationTable();

		if (empty($table))
		{
			// Translation table does not exist we are redirecting to manager
			JFactory::getApplication()->redirect('index.php?option=com_redcore&view=translations&contentelement=&layout=manage');
		}

		$contentElement = RTranslationHelper::getContentElement($table->option, $table->xml);
		$db	= $this->getDbo();
		$query = $db->getQuery(true);
		$item = new stdClass;

		$ids = explode('###', $ids);

		$query->select('*')
			->from($db->qn($table->table));

		foreach ($table->primaryKeys as $key => $primaryKey)
		{
			$query->where($db->qn($primaryKey) . ' = ' . $db->q($ids[$key]));
		}

		$db->setQuery($query);
		$item->original = $db->loadObject();

		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn(RTranslationTable::getTranslationsTableName($table->table, '')))
			->where('rctranslations_id = ' . $db->q($id));

		$db->setQuery($query);
		$item->translation = $db->loadObject();

		if (empty($item->translation))
		{
			$item->translation = new stdClass;

			foreach ($table->columns as $column)
			{
				$item->translation->{$column} = null;
			}

			foreach ($table->primaryKeys as $primaryKey)
			{
				if (!empty($item->original->{$primaryKey}))
				{
					$item->translation->{$primaryKey} = $item->original->{$primaryKey};
				}
			}

			$item->rctranslations_state = 1;
			$item->rctranslations_modified = '';
			$item->rctranslations_language = JFactory::getApplication()->input->getString('language', '');
			$item->id = 0;
		}
		else
		{
			if (!empty($item->translation->rctranslations_originals))
			{
				$registry = new JRegistry;
				$registry->loadString($item->translation->rctranslations_originals);
				$item->translation->rctranslations_originals = $registry->toArray();
			}

			$item->original->rctranslations_state = $item->rctranslations_state = $item->translation->rctranslations_state;
			$item->original->rctranslations_modified = $item->rctranslations_modified = $item->translation->rctranslations_modified;
			$item->original->rctranslations_language = $item->rctranslations_language = $item->translation->rctranslations_language;
			$item->original->rctranslations_originals = $item->rctranslations_originals = $item->translation->rctranslations_originals;
			$item->id = $item->translation->rctranslations_id;
		}

		$fieldsXml = $contentElement->getTranslateFields();

		foreach ($fieldsXml as $field)
		{
			if ((string) $field['translate'] == '0')
			{
				$item->translation->{(string) $field['name']} = $item->original->{(string) $field['name']};
			}

			if ((string) $field['type'] == 'params'
				&& (empty($item->translation->{(string) $field['name']}) || $item->translation->{(string) $field['name']} == '{}'))
			{
				$item->translation->{(string) $field['name']} = $item->original->{(string) $field['name']};
			}

			if ((string) $field['type'] == 'readonlytext'
				&& (empty($item->translation->{(string) $field['name']}) || $item->translation->{(string) $field['name']} == '{}'))
			{
				$item->translation->{(string) $field['name']} = $item->original->{(string) $field['name']};
			}
		}

		return $item;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 */
	public function save($data)
	{
		$translationTable = RedcoreHelpersTranslation::getTranslationTable();
		$contentElement = RTranslationHelper::getContentElement($translationTable->option, $translationTable->xml);
		$translation = JFactory::getApplication()->input->get('translation', array(), 'array');
		$original = JFactory::getApplication()->input->get('original', array(), 'array');
		$id = !empty($data['rctranslations_id']) ? (int) $data['rctranslations_id'] : 0;

		//Check if the form is completely empty, and return an error if it is.
		$dataFilled = RedcoreHelpersTranslation::validateEmptyTranslationData($translation, $translationTable->primaryKeys);

		if(!$dataFilled)
		{
			$this->setError(JText::_('COM_REDCORE_TRANSLATIONS_SAVE_ERROR_EMPTY'));
			return false;
		}

		$data = array_merge($data, $translation);
		
		$fieldsXml = $contentElement->getTranslateFields();

		foreach ($fieldsXml as $field)
		{
			if ((string) $field['type'] == 'params' && (string) $field['translate'] == '1')
			{
				$fieldName = (string) $field['name'];
				$original[$fieldName] = $original['params_' . $fieldName];
				$paramsChanged = false;

				if (!empty($data[$fieldName]))
				{
					$registry = new JRegistry;
					$registry->loadString($original[$fieldName]);
					$originalParams = $registry->toArray();

					foreach ($data[$fieldName] as $paramKey => $paramValue)
					{
						if ((!isset($originalParams[$paramKey]) && $paramValue != '') || $originalParams[$paramKey] != $paramValue)
						{
							$paramsChanged = true;

							break;
						}
					}

					if ($paramsChanged)
					{
						$data[$fieldName] = json_encode($data[$fieldName]);
					}
					else
					{
						$data[$fieldName] = '';
					}
				}
			}
		}

		$dispatcher = RFactory::getDispatcher();
		/** @var RedcoreTableTranslation $table */
		$table = $this->getTable();

		if (empty($id))
		{
			$db	= $this->getDbo();
			$query = $db->getQuery(true)
				->select('rctranslations_id')
				->from($db->qn(RTranslationTable::getTranslationsTableName($translationTable->table, '')))
				->where('rctranslations_language = ' . $db->q($data['rctranslations_language']));

			foreach ($translationTable->primaryKeys as $primaryKey)
			{
				if (!empty($data[$primaryKey]))
				{
					$query->where($db->qn($primaryKey) . ' = ' . $db->q($data[$primaryKey]));
				}
			}

			$db->setQuery($query);
			$id = $db->loadResult();
		}

		foreach ($translationTable->primaryKeys as $primaryKey)
		{
			$original[$primaryKey] = $data[$primaryKey];
		}

		$isNew = true;

		// Load the row if saving an existing item.
		$table->load((int) $id);

		if ($table->rctranslations_modified)
		{
			$isNew = false;
		}

		$data['rctranslations_originals'] = RTranslationTable::createOriginalValueFromColumns($original, $translationTable->columns);

		// We run posthandler methods
		foreach ($fieldsXml as $field)
		{
			$postHandler = (string) $field['posthandler'];
			$fieldName = (string) $field['name'];

			if (!empty($postHandler) && (string) $field['translate'] == '1')
			{
				$postHandlerFunctions = explode(',', $postHandler);

				foreach ($postHandlerFunctions as $postHandlerFunction)
				{
					$postHandlerFunctionArray = explode('::', $postHandlerFunction);

					if (empty($postHandlerFunctionArray[1]))
					{
						$postHandlerFunctionArray[1] = $postHandlerFunctionArray[0];
						$postHandlerFunctionArray[0] = 'RTranslationContentHelper';
						$postHandlerFunction = 'RTranslationContentHelper::' . $postHandlerFunction;
					}

					if (method_exists($postHandlerFunctionArray[0], $postHandlerFunctionArray[1]))
					{
						call_user_func_array(
							array(
								$postHandlerFunctionArray[0],
								$postHandlerFunctionArray[1]),
								array($field, &$data[$fieldName], &$data, $translationTable)
						);
					}
				}
			}
		}

		// Bind the data.
		if (!$table->bind($data))
		{
			$this->setError($table->getError());

			return false;
		}

		// Prepare the row for saving
		$this->prepareTable($table);

		// Check the data.
		if (!$table->check())
		{
			$this->setError($table->getError());

			return false;
		}

		// Trigger the onContentBeforeSave event.
		$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, &$table, $isNew));

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

		// Trigger the onContentAfterSave event.
		$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, &$table, $isNew));

		$this->setState($this->getName() . '.id', $table->rctranslations_id);

		// Clear the cache
		$this->cleanCache();

		return true;
	}
}
