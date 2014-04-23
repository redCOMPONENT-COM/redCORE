<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
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
	public function getItem($pk = null)
	{
		$ids = JFactory::getApplication()->input->getString('id', '');
		$id = JFactory::getApplication()->input->getString('rctranslations_id', '');
		$table = RedcoreHelpersTranslation::getTranslationTable();
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

			$item->rctranslations_state = 1;
			$item->rctranslations_modified = '';
			$item->rctranslations_language = JFactory::getApplication()->input->getString('language', '');
			$item->id = 0;
		}
		else
		{
			if (!empty($item->translation->t_rctranslations_originals))
			{
				$registry = new JRegistry;
				$registry->loadString($item->translation->t_rctranslations_originals);
				$item->translation->t_rctranslations_originals = $registry->toArray();
			}

			$item->rctranslations_state = $item->translation->rctranslations_state;
			$item->rctranslations_modified = $item->translation->rctranslations_modified;
			$item->rctranslations_language = $item->translation->rctranslations_language;
			$item->id = $item->translation->rctranslations_id;
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
		$translation = JFactory::getApplication()->input->get('translation', array(), 'array');
		$original = JFactory::getApplication()->input->get('original', array(), 'array');
		$id = JFactory::getApplication()->input->getString('rctranslations_id', '');
		$data = array_merge($data, $translation);

		$dispatcher = RFactory::getDispatcher();
		$translationTable = RedcoreHelpersTranslation::getTranslationTable();
		/** @var RedcoreTableTranslation $table */
		$table = $this->getTable();
		$data['rctranslations_originals'] = array();

		foreach ($translationTable->primaryKeys as $primaryKey)
		{
			$data['rctranslations_originals'][$primaryKey] = md5($data[$primaryKey]);
		}

		$isNew = true;

		// Load the row if saving an existing item.
		$table->load((int) $id);

		if ($table->rctranslations_modified)
		{
			$isNew = false;
		}


		foreach ($translationTable->columns as $column)
		{
			if (empty($data['rctranslations_originals'][$column]))
			{
				$data['rctranslations_originals'][$column] = md5($original[$column]);
			}
		}

		$data['rctranslations_originals'] = json_encode($data['rctranslations_originals']);

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
