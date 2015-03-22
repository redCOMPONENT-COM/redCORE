<?php
/**
 * @package     Redcore
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * redCORE Soap Webservice Dynamic Class
 *
 * @package     Redcore
 * @subpackage  Soap
 * @since       1.4
 */
class RApiSoapOperationOperation
{
	/**
	 * Webservice object
	 *
	 * @var  RApiHalHal
	 */
	protected $webservice = null;

	/**
	 * Constructor.
	 *
	 * @param   RApiHalHal  $webservice  Webservice object
	 * @param   array       $config      An optional associative array of configuration settings.
	 */
	public function __construct($webservice, $config = array())
	{
		$this->webservice = $webservice;
	}

	/**
	 * Read list
	 *
	 * @param   int     $limitStart         Start position number of the fetched data
	 * @param   int     $limit              Limit the number of fetched rows. Set 0 for all rows
	 * @param   string  $filterSearch       Search Data with specific filter
	 * @param   array   $filters            Search Data with specific filter
	 * @param   string  $ordering           Ordering
	 * @param   string  $orderingDirection  Ordering direction ASC or DESC
	 * @param   string  $language           Language Tag name (ex: en)
	 *
	 * @return  array
	 */
	public function readList(
		$limitStart = 0, $limit = 20, $filterSearch = null, $filters = array(), $ordering = null, $orderingDirection = null, $language = null)
	{
		// We are setting the operation of the webservice to Read
		$this->setOperation('read');
		$dataGet = $this->webservice->options->get('dataGet', array());

		if (is_object($dataGet))
		{
			$dataGet = JArrayHelper::fromObject($dataGet);
		}

		if ($limitStart != 0)
		{
			$dataGet['list']['limitstart'] = (int) $limitStart;
		}

		if ($limit != 20)
		{
			$dataGet['list']['limit'] = (int) $limit;
		}

		if (!is_null($filterSearch))
		{
			$dataGet['filter']['search'] = $filterSearch;
		}

		if (!empty($filters) && is_array($filters))
		{
			foreach ($filters as $key => $value)
			{
				$dataGet['filter'][$key] = $value;
			}
		}

		if (!is_null($ordering))
		{
			$dataGet['filter']['order'] = $ordering;
		}

		if (!is_null($orderingDirection))
		{
			$dataGet['filter']['order_Dir'] = $orderingDirection;
		}

		// Handle different language switch
		$this->setLanguage($language);

		$this->webservice->options->set('dataGet', $dataGet);
		$this->webservice->options->set('task', '');
		$this->webservice->execute();

		return $this->webservice->hal->toArray();
	}

	/**
	 * Read item
	 *
	 * @param   array   $id        ID key(s) of the item. If multiple keys then they need to be in a array format (ex: array('id' => 4, 'sub_id' = 14))
	 * @param   string  $language  Language Tag name (ex: en)
	 *
	 * @return  array
	 */
	public function readItem($id = array(), $language = null)
	{
		// We are setting the operation of the webservice to Read
		$this->setOperation('read');
		$dataGet = $this->webservice->options->get('dataGet', array());
		$primaryKeysFromFields = $this->webservice->getPrimaryKeysFromFields($this->webservice->configuration->operations->read->item);

		// If there are no primary keys defined we will use id field as default
		if (empty($primaryKeysFromFields))
		{
			$primaryKeysFromFields['id'] = array('transform' => 'int');
		}

		if (!is_array($id))
		{
			if (count($primaryKeysFromFields) == 1)
			{
				$id = array();
				$id[key($primaryKeysFromFields)] = $id;
			}
			else
			{
				$id = array('id' => $id);
			}
		}

		foreach ($primaryKeysFromFields as $primaryKey => $primaryKeyField)
		{
			if (isset($id[$primaryKey]) && $id[$primaryKey] != '')
			{
				$dataGet->{$primaryKey} = $this->webservice->transformField($primaryKeyField['transform'], $id[$primaryKey], false);
			}
		}

		// Handle different language switch
		$this->setLanguage($language);

		$this->webservice->options->set('dataGet', $dataGet);
		$this->webservice->options->set('task', '');
		$this->webservice->execute();

		return $this->webservice->hal->toArray();
	}

	/**
	 * Create operation
	 *
	 * @param   array  $data  Data array passed for the item
	 *
	 * @return  array
	 */
	public function create($data = array())
	{
		// We are setting the operation of the webservice to create
		$this->webservice->options->set('task', '');
		$this->setOperation('create');
		$this->webservice->options->set('data', $data);
		$this->webservice->execute();

		return $this->webservice->hal->toArray();
	}

	/**
	 * Update operation
	 *
	 * @param   array  $data  Data array passed for the item
	 *
	 * @return  array
	 */
	public function update($data = array())
	{
		// We are setting the operation of the webservice to update
		$this->webservice->options->set('task', '');
		$this->setOperation('update');
		$this->webservice->options->set('data', $data);
		$this->webservice->execute();

		return $this->webservice->hal->toArray();
	}

	/**
	 * Delete operation
	 *
	 * @param   array  $data  Data array passed for the item
	 *
	 * @return  array
	 */
	public function delete($data = array())
	{
		// We are setting the operation of the webservice to delete
		$this->webservice->options->set('task', '');
		$this->setOperation('delete');
		$this->webservice->options->set('data', $data);
		$this->webservice->execute();

		return $this->webservice->hal->toArray();
	}

	/**
	 * We use this method to counter all task related methods and point them to the same method
	 *
	 * @param   string  $method  Method name
	 * @param   array   $args    Arrays passed to the method
	 *
	 * @return  mixed
	 */
	public function __call($method, $args)
	{
		if (strpos($method, 'task_') === 0)
		{
			$taskName = substr($method, 5);

			return $this->task($taskName, $args);
		}
	}

	/**
	 * Triggers specific task operation on the webservice
	 *
	 * @param   string  $taskName  Task name
	 * @param   array   $data      Data Array passed to the task method
	 *
	 * @return  array
	 */
	private function task($taskName, $data)
	{
		// We are setting the operation of the webservice to task
		$this->webservice->options->set('task', $taskName);
		$this->setOperation('task');
		$this->webservice->options->set('data', $data);
		$this->webservice->execute();

		return $this->webservice->hal->toArray();
	}

	/**
	 * Set operation of the webservice
	 *
	 * @param   string  $operationName  Operation name
	 *
	 * @return  void
	 */
	protected function setOperation($operationName)
	{
		if ($operationName == 'task')
		{
			$task = $this->webservice->options->get('task', '');

			// If task is pointing to some other operation like apply, update or delete
			if (!empty($task) && !empty($this->webservice->configuration->operations->task->{$task}['useOperation']))
			{
				$operation = strtoupper((string) $this->webservice->configuration->operations->task->{$task}['useOperation']);

				if (in_array($operation, array('CREATE', 'READ', 'UPDATE', 'DELETE', 'DOCUMENTATION')))
				{
					$operationName = $operation;
				}
			}
		}

		$this->webservice->operation = strtolower($operationName);
	}

	/**
	 * Set language of the site
	 *
	 * @param   string  $language  Language name
	 *
	 * @return  void
	 */
	protected function setLanguage($language)
	{
		$languageObject = JFactory::getLanguage();
		$languages = JLanguageHelper::getLanguages('sef');

		if (!empty($language) && isset($languages[$language]))
		{
			$languageObject->setLanguage($languages[$language]->lang_code);
		}
		else
		{
			$languageObject->setLanguage($languages[RTranslationHelper::getSiteLanguage()]->lang_code);
		}
	}
}
