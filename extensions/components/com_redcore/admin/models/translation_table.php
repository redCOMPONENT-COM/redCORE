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
 * Translation Table Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.8
 */
class RedcoreModelTranslation_Table extends RModelAdmin
{
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @throws  RuntimeException
	 *
	 * @since   1.2
	 */
	public function save($data)
	{
		$primaryKeys = array();
		$fallbackColumns = array();
		$translateColumns = array();

		// Set up columns to their respective placeholder
		if (!empty($data['columns']))
		{
			foreach ($data['columns'] as $columnKey => $column)
			{
				if ($column['column_type'] == RTranslationTable::COLUMN_PRIMARY)
				{
					$primaryKeys[] = $column['name'];
				}
				elseif ($column['column_type'] == RTranslationTable::COLUMN_READONLY)
				{
				}
				elseif($column['fallback'] == 1)
				{
					$fallbackColumns[] = $column['name'];
					$translateColumns[] = $column['name'];
				}
				else
				{
					$translateColumns[] = $column['name'];
				}

				// Key/Value sets that do not belong in the table but are treated as extra parameters from editor or plugins
				if (!empty($column['extra_field_key']))
				{
					$params = array();

					foreach ($column['extra_field_key'] as $index => $extraKey)
					{
						if (!empty($extraKey))
						{
							$params[$extraKey] = isset($column['extra_field_value'][$index]) ? $column['extra_field_value'][$index] : '';
						}
					}

					$data['columns'][$columnKey]['params'] = $params;
				}

				$data['columns'][$columnKey]['params'] = !empty($data['columns'][$columnKey]['params']) ?
					json_encode($data['columns'][$columnKey]['params']) : json_encode(array());
			}
		}

		// Set up params
		$params = !empty($data['params']) ? $data['params'] : array();

		if ($data['description'])
		{
			$params['description'] = $data['description'];
		}

		if ($data['author'])
		{
			$params['author'] = $data['author'];
		}

		if ($data['copyright'])
		{
			$params['copyright'] = $data['copyright'];
		}

		$data['params'] = $params;

		$data['primary_columns'] = implode(',', $primaryKeys);
		$data['fallback_columns'] = implode(',', $fallbackColumns);
		$data['translate_columns'] = implode(',', $translateColumns);

		if (!empty($data['formLinks']))
		{
			$data['form_links'] = json_encode($data['formLinks']);
		}
		elseif (!empty($data['editForms']))
		{
			$data['form_links'] = json_encode($data['editForms']);
		}

		$data['fromEditForm'] = JFactory::getApplication()->input->get('fromEditForm') == '1';

		return parent::save($data);
	}

	/**
	 * Load item object
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   1.2
	 */
	public function getItem($pk = null)
	{
		$item = parent::getItem($pk);

		if ($item)
		{
			// Get Access token and Authorization codes
			$db	= $this->getDbo();

			// There can be multiple access tokens that are not expired yet so we only load last one
			$query = $db->getQuery(true)
				->select('*')
				->from($db->qn('#__redcore_translation_columns', 'tc'))
				->where('tc.translation_table_id = ' . $db->q($item->id))
				->order('tc.id');
			$db->setQuery($query);

			$item->columns = $db->loadObjectList();

			foreach ($item->columns as $key => $column)
			{
				$item->columns[$key]->params = json_decode($column->params, true);
			}

			$item->editForms = json_decode($item->form_links, true);
			$item->params = is_string($item->params) ? json_decode($item->params, true) : $item->params;

			foreach ($item->params as $key => $param)
			{
				if (!isset($item->{$key}))
				{
					$item->{$key} = $param;
				}
			}
		}
		else
		{
			$item->columns = array();
			$item->editForms = array();
		}

		return $item;
	}
}
