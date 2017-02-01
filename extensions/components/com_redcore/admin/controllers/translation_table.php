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
 * Translation Table Controller
 *
 * @package     Redcore.Backend
 * @subpackage  Controllers
 * @since       1.0
 */
class RedcoreControllerTranslation_Table extends RControllerForm
{
	/**
	 * Method to get new Fields from Database Table in HTML
	 *
	 * @return  void
	 */
	public function ajaxGetColumns()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$tableName = str_replace('#__', '', $input->getString('tableName', ''));

		if (empty($tableName))
		{
			echo JText::_('COM_REDCORE_TRANSLATION_TABLE_NOT_SELECTED');
			$app->close();
		}

		$db = JFactory::getDbo();
		$columns = array();

		$tableList = $db->getTableList();
		$tablePrefix = $db->getPrefix();

		if (in_array($tablePrefix . $tableName, $tableList))
		{
			$columns = $db->getTableColumns('#__' . $tableName, false);
		}
		else
		{
			echo JText::_('COM_REDCORE_TRANSLATION_TABLE_DONT_EXIST');
			$app->close();
		}

		echo JHtml::_(
			'select.genericlist',
			$columns,
			'tableColumnList',
			' class="" ',
			'Field',
			'Field'
		);

		$app->close();
	}

	/**
	 * Method to get new Fields from Database Table in HTML
	 *
	 * @return  void
	 */
	public function ajaxGetColumn()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$tableName = str_replace('#__', '', $input->getString('tableName', ''));
		$columnName = $input->getString('columnName', '');

		/** @var RedcoreTableTranslation_Column $xrefTable */
		$column = RTable::getAdminInstance('Translation_Column', array(), 'com_redcore');

		if (!empty($columnName) && !empty($tableName))
		{
			$db = JFactory::getDbo();
			$tableList = $db->getTableList();
			$tablePrefix = $db->getPrefix();

			if (in_array($tablePrefix . $tableName, $tableList))
			{
				$columns = $db->getTableColumns('#__' . $tableName, false);

				foreach ($columns as $columnKey => $columnValue)
				{
					if ($columnValue->Field == $columnName)
					{
						$column->name = $columnValue->Field;
						$column->title = $columnValue->Field;
						$column->description = $columnValue->Comment;
						$this->getValueTypeByDbType($column, $columnValue->Type);

						if ($columnValue->Key == 'PRI')
						{
							$column->column_type = RTranslationTable::COLUMN_PRIMARY;
							$column->value_type = 'referenceid';
						}
					}
				}
			}
		}

		echo RLayoutHelper::render(
			'translation.table.column',
			array(
				'view' => $this,
				'options' => array(
					'column' => $column,
					'form'   => null,
				)
			)
		);

		$app->close();
	}

	/**
	 * Method to get new Edit form in HTML
	 *
	 * @return  void
	 */
	public function ajaxGetEditForm()
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$extensionName = $input->getString('extensionName', '');
		$editForm = array('option' => $extensionName);

		echo RLayoutHelper::render(
			'translation.table.editform',
			array(
				'view' => $this,
				'options' => array(
					'editForm' => $editForm,
					'form'   => null,
				)
			)
		);

		$app->close();
	}

	/**
	 * Returns same object with updated fields depending on the type
	 *
	 * @param   object  &$column  Column
	 * @param   string  $type     Database type
	 *
	 * @return  string
	 */
	private function getValueTypeByDbType(&$column, $type)
	{
		$type = explode('(', $type);
		$type = strtoupper(trim($type[0]));

		// We do not test for Varchar because fallback Transform Element String
		switch ($type)
		{
			case 'TINYINT':
			case 'SMALLINT':
			case 'MEDIUMINT':
			case 'INT':
			case 'BIGINT':
				$column->filter = 'UINT';
				break;
			case 'FLOAT':
			case 'DOUBLE':
			case 'DECIMAL':
				$column->filter = 'FLOAT';
				break;
			default:
				$column->filter = 'STRING';
				$column->value_type = 'text';
		}
	}
}
