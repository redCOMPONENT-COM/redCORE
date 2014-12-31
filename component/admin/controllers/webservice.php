<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
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
