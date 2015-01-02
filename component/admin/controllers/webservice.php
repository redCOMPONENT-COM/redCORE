<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
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
		$model = $this->getModel();
		$model->formData['task-' . $taskName] = $model->bindPathToArray('//operations/taskResources', $model->defaultXmlFile);
		$model->setFieldsAndResources('task-' . $taskName, '//operations/taskResources', $model->defaultXmlFile);

		if (!empty($taskName))
		{
			echo RLayoutHelper::render(
				'webservice.operation',
				array(
					'view' => $model,
					'options' => array(
						'operation' => 'task-' . $taskName,
						'form'      => $model->getForm($model->formData, false),
						'tabActive' => ' active in ',
						'fieldList' => array('defaultValue', 'isRequiredField', 'isPrimaryField'),
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
	public function ajaxGetField()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$operation = $input->getString('operation', 'read');
		$fieldList = $input->getString('fieldList', '');
		$fieldList = explode(',', $fieldList);

		echo RLayoutHelper::render(
			'webservice.fields.field',
			array(
				'view' => $this,
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

		if (!empty($tableName))
		{
			$db = JFactory::getDbo();
			$columns = $db->getTableColumns('#__' . $tableName, false);

			if ($columns)
			{
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
							'options' => array(
								'operation' => $operation,
								'fieldList' => $fieldList,
								'form' => $form,
							)
						)
					);
				}
			}
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

		if (!empty($tableName))
		{
			$db = JFactory::getDbo();
			$columns = $db->getTableColumns('#__' . $tableName, false);

			if ($columns)
			{
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
							'options' => array(
								'operation' => $operation,
								'fieldList' => $fieldList,
								'form' => $form,
							)
						)
					);
				}
			}
		}

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

		$operation = $input->getString('operation', 'read');
		$fieldList = $input->getString('fieldList', '');

		echo RLayoutHelper::render(
			'webservice.resources.resource',
			array(
				'view' => $this,
				'options' => array(
					'operation' => $operation,
					'fieldList' => $fieldList,
				)
			)
		);

		$app->close();
	}
}
