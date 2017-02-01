<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Webservice Controller
 *
 * @package     Redcore.Backend
 * @subpackage  Controllers
 * @since       1.4
 */
class RedcoreControllerWebservice extends RControllerForm
{
	/**
	 * Method to get new Task HTML
	 *
	 * @return  void
	 */
	public function ajaxGetTask()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$taskName = $input->getString('taskName', '');

		if (empty($taskName))
		{
			$app->close();
		}

		$model = $this->getModel();
		$model->formData['task-' . $taskName] = $model->bindPathToArray('//operations/taskResources', $model->defaultXmlFile);
		$model->setFieldsAndResources('task-' . $taskName, '//operations/taskResources', $model->defaultXmlFile);

		echo RLayoutHelper::render(
			'webservice.operation',
			array(
				'view' => $model,
				'model' => $model,
				'options' => array(
					'operation' => 'task-' . $taskName,
					'form'      => $model->getForm($model->formData, false),
					'tabActive' => ' active in ',
					'fieldList' => array('defaultValue', 'isRequiredField', 'isPrimaryField'),
				)
			)
		);

		$app->close();
	}

	/**
	 * Ajax webservice add complex type function.
	 *
	 * @return void
	 */
	public function ajaxAddComplexType()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$typeName = $input->getString('typeName', '');

		if (empty($typeName))
		{
			$app->close();
		}

		/** @var RedcoreModelWebservice $model */
		$model = $this->getModel();
		$xml = simplexml_load_string(str_replace('"operation"', '"type-' . $typeName . '"', $model->loadFormComplexTypeXml()));

		$model->formData['type-' . $typeName] = $model->bindPathToArray('//complexArrays/' . $typeName, $xml);
		$model->setPropertyByXpath('fields', 'type-' . $typeName, '//complexArrays/' . $typeName . '/fields/field', $xml);

		$view = $this->getView('Webservice', 'html');
		$view->setModel($model, true);

		echo RLayoutHelper::render(
			'webservice.complextype',
			array(
				'view' => $model,
				'model' => $model,
				'options' => array(
					'operation' => 'type-' . $typeName,
					'form'      => $model->getForm($model->formData, false),
					'tabActive' => ' active in ',
					'fieldList' => array('defaultValue', 'isRequiredField', 'isPrimaryField'),
				)
			)
		);

		$app->close();
	}

	/**
	 * Method to get new Field HTML
	 *
	 * @return  void
	 */
	public function ajaxGetField()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$operation = $input->getString('operation', 'read');
		$fieldList = $input->getString('fieldList', '');
		$fieldList = explode(',', $fieldList);
		$view = $this->getView('webservice', 'html');
		$model = $this->getModel('Webservice');
		$id = $input->getInt('id', null);
		$model->getItem($id);
		$view->setModel($model, true);

		echo RLayoutHelper::render(
			'webservice.fields.field',
			array(
				'view' => $view,
				'model' => $model,
				'options' => array(
					'operation' => $operation,
					'fieldList' => $fieldList,
				)
			)
		);

		$app->close();
	}

	/**
	 * Method to get new Fields from Database Table in HTML
	 *
	 * @return  void
	 */
	public function ajaxGetFieldFromDatabase()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$operation = $input->getString('operation', 'read');
		$fieldList = $input->getString('fieldList', '');
		$fieldList = explode(',', $fieldList);
		$tableName = $input->getCmd('tableName', '');
		$model = $this->getModel();

		$id = $input->getInt('id', null);
		$model->getItem($id);

		if (empty($tableName))
		{
			$app->close();
		}

		$db = JFactory::getDbo();
		$columns = $db->getTableColumns('#__' . $tableName, false);

		if (!$columns)
		{
			$app->close();
		}

		foreach ($columns as $columnKey => $column)
		{
			$form = array(
				'name' => $column->Field,
				'transform' => RApiHalHelper::getTransformElementByDbType($column->Type),
				'defaultValue' => $column->Default,
				'isPrimaryField' => $column->Key == 'PRI' ? 'true' : 'false',
				'description' => $column->Comment,
			);

			echo RLayoutHelper::render(
				'webservice.fields.field',
				array(
					'view' => $this,
					'model' => $model,
					'options' => array(
						'operation' => $operation,
						'fieldList' => $fieldList,
						'form' => $form,
					)
				)
			);
		}

		$app->close();
	}

	/**
	 * Method to get new Resources from Database Table in HTML
	 *
	 * @return  void
	 */
	public function ajaxGetResourceFromDatabase()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$operation = $input->getString('operation', 'read');
		$fieldList = $input->getString('fieldList', '');
		$fieldList = explode(',', $fieldList);
		$tableName = $input->getCmd('tableName', '');
		$model = $this->getModel();

		$id = $input->getInt('id', null);
		$model->getItem($id);

		if (empty($tableName))
		{
			$app->close();
		}

		$db = JFactory::getDbo();
		$columns = $db->getTableColumns('#__' . $tableName, false);

		if (!$columns)
		{
			$app->close();
		}

		foreach ($columns as $columnKey => $column)
		{
			$form = array(
				'displayName' => $column->Field,
				'transform' => RApiHalHelper::getTransformElementByDbType($column->Type),
				'resourceSpecific' => 'rcwsGlobal',
				'fieldFormat' => '{' . $column->Field . '}',
				'description' => $column->Comment,
			);

			echo RLayoutHelper::render(
				'webservice.resources.resource',
				array(
					'view' => $this,
					'model' => $model,
					'options' => array(
						'operation' => $operation,
						'fieldList' => $fieldList,
						'form' => $form,
					)
				)
			);
		}

		$app->close();
	}

	/**
	 * Method to get new Field HTML
	 *
	 * @return  void
	 */
	public function ajaxGetConnectWebservice()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$operation = $input->getString('operation', 'read');
		$fieldList = $input->getString('fieldList', '');
		$webserviceId = $input->getString('webserviceId', '');

		if (empty($webserviceId))
		{
			$app->close();
		}

		$model = $this->getModel();
		$item = $model->getItem($webserviceId);

		$link = '/index.php?option=' . $item->name;
		$link .= '&amp;webserviceVersion=' . $item->version;
		$link .= '&amp;webserviceClient=' . $item->client;
		$link .= '&amp;id={' . $item->name . '_id}';

		$form = array(
			'displayName' => $item->name,
			'linkTitle' => $item->title,
			'transform' => 'string',
			'resourceSpecific' => 'rcwsGlobal',
			'displayGroup' => '_links',
			'linkTemplated' => 'true',
			'fieldFormat' => $link,
			'description' => JText::sprintf('COM_REDCORE_WEBSERVICE_RESOURCE_ADD_CONNECTION_DESCRIPTION_LABEL', $item->name, '{' . $item->name . '_id}'),
		);

		echo RLayoutHelper::render(
			'webservice.resources.resource',
			array(
				'view' => $this,
				'model' => $model,
				'options' => array(
					'operation' => $operation,
					'fieldList' => $fieldList,
					'form' => $form,
				)
			)
		);

		$app->close();
	}

	/**
	 * Method to get new Field HTML
	 *
	 * @return  void
	 */
	public function ajaxGetResource()
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$model = $this->getModel();
		$id = $input->getInt('id', null);
		$model->getItem($id);

		$operation = $input->getString('operation', 'read');
		$fieldList = $input->getString('fieldList', '');

		echo RLayoutHelper::render(
			'webservice.resources.resource',
			array(
				'view' => $this,
				'model' => $model,
				'options' => array(
					'operation' => $operation,
					'fieldList' => $fieldList,
				)
			)
		);

		$app->close();
	}
}
