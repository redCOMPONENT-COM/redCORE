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
	 * @param   object  $data  $limitStart, $limit, $filterSearch,
	 *                         $filters, $ordering, $orderingDirection, $language
	 *
	 * @return  array
	 */
	public function readList($data)
	{
		// We are setting the operation of the webservice to Read
		$this->setOperation('read');
		$dataGet = $this->webservice->options->get('dataGet', array());

		if (is_object($dataGet))
		{
			$dataGet = JArrayHelper::fromObject($dataGet);
		}

		$dataGet['list']['limitstart'] = (isset($data->limitStart) ? (int) $data->limitStart : 0);
		$dataGet['list']['limit'] = (isset($data->limit) ? (int) $data->limit : 20);
		$dataGet['filter']['search'] = (isset($data->filterSearch) ? (string) $data->filterSearch : '');

		$filters = RApiHalHelper::getFilterFields($this->webservice->configuration->operations->read->list, true);

		foreach ($filters as $filter)
		{
			$dataGet['filter'][$filter] = isset($data->filters->$filter) ? $data->filters->$filter : '';
		}

		$dataGet['list']['ordering'] = (isset($data->ordering) ? (string) $data->ordering : '');
		$dataGet['list']['direction'] = (isset($data->orderingDirection) ? (string) $data->orderingDirection : '');

		// Handle different language switch
		$this->setLanguage((isset($data->language) ? (string) $data->language : ''));

		$this->webservice->options->set('dataGet', $dataGet);
		$this->webservice->options->set('task', '');
		$this->webservice->options->set('filterOutResourcesGroups', array('_links', '_messages'));
		$this->webservice->options->set('filterResourcesSpecific', 'listItem');
		$this->webservice->execute();

		$arr = $this->webservice->hal->toArray();

		$outputResources = RApiSoapHelper::getOutputResources($this->webservice->configuration->operations->read->list, 'listItem', true);

		if ($arr['_embedded'] && $arr['_embedded']['item'])
		{
			$response = RApiSoapHelper::selectListResources($outputResources, $arr['_embedded']['item']);
		}
		else
		{
			$response = array();
		}

		$final = new stdClass;
		$final->list = $response;

		return $final;
	}

	/**
	 * Read item
	 *
	 * @param   object  $data  Primary keys and $language
	 *
	 * @return  array
	 */
	public function readItem($data)
	{
		// We are setting the operation of the webservice to Read
		$this->setOperation('read');
		$dataGet = $this->webservice->options->get('dataGet', array());
		$primaryKeysFromFields = RApiHalHelper::getFieldsArray($this->webservice->configuration->operations->read->item, true);

		// If there are no primary keys defined we will use id field as default
		if (empty($primaryKeysFromFields))
		{
			$primaryKeysFromFields['id'] = array('transform' => 'int');
		}

		foreach ($primaryKeysFromFields as $primaryKey => $primaryKeyField)
		{
			$keyData = '';

			if (isset($data->$primaryKey) && $data->$primaryKey != '')
			{
				$keyData = $data->$primaryKey;
			}

			$dataGet->$primaryKey = $this->webservice->transformField($primaryKeyField['transform'], $keyData, false);
		}

		// Handle different language switch
		$this->setLanguage((string) (isset($data->language) ? $data->language : ''));

		$this->webservice->options->set('dataGet', $dataGet);
		$this->webservice->options->set('task', '');
		$this->webservice->options->set('filterOutResourcesGroups', array('_links', '_messages'));
		$this->webservice->execute();

		$arr = $this->webservice->hal->toArray();
		$outputResources = RApiSoapHelper::getOutputResources($this->webservice->configuration->operations->read->item, '', true);

		$response = RApiSoapHelper::selectListResources($outputResources, array($arr));

		$final = new stdClass;
		$final->item = (empty($response) ? array() : $response[0]);

		$match = true;

		if (RApiHalHelper::isAttributeTrue($this->webservice->configuration->operations->read->item, 'enforcePKs', true))
		{
			foreach ($primaryKeysFromFields as $primaryKey => $primaryKeyField)
			{
				if ($dataGet->$primaryKey != $final->item->$primaryKey)
				{
					$match = false;
				}
			}
		}

		if (!$match)
		{
			$final = array();
		}

		if (!count((array) $final->item))
		{
			$final = array();
		}

		return $final;
	}

	/**
	 * Create operation
	 *
	 * @param   object  $data  Data array passed for the item
	 *
	 * @return  mixed
	 */
	public function create($data)
	{
		// We are setting the operation of the webservice to create
		$this->webservice->options->set('task', '');
		$this->setOperation('create');
		$this->webservice->options->set('data', (array) $data);
		$this->webservice->options->set('filterOutResourcesGroups', array('_links', '_messages'));
		$this->webservice->execute();

		$arr = $this->webservice->hal->toArray();

		if (!isset($arr['result']))
		{
			$arr['result'] = false;
		}

		return $arr;
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
		$this->webservice->options->set('filterOutResourcesGroups', array('_links', '_messages'));
		$this->webservice->execute();

		$arr = $this->webservice->hal->toArray();

		if (!isset($arr['result']))
		{
			$arr['result'] = false;
		}

		return $arr;
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
		$this->webservice->options->set('filterOutResourcesGroups', array('_links', '_messages'));
		$this->webservice->execute();

		$arr = $this->webservice->hal->toArray();

		if (!isset($arr['result']))
		{
			$arr['result'] = false;
		}

		return $arr;
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
	 * @param   object  $data      Data Array passed to the task method
	 *
	 * @return  array
	 */
	private function task($taskName, $data)
	{
		// Correctly load of data coming from SOAP request
		if (is_array($data) && !empty($data))
		{
			$data = $data[0];
		}

		// We are setting the operation of the webservice to task
		$this->webservice->options->set('task', $taskName);
		$this->setOperation('task');
		$this->webservice->options->set('data', (array) $data);
		$this->webservice->options->set('filterOutResourcesGroups', array('_links', '_messages'));
		$this->webservice->execute();

		$arr = $this->webservice->hal->toArray();

		if (!isset($arr['result']))
		{
			$arr['result'] = false;
		}

		return $arr;
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
