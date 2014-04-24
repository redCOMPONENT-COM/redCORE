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
 * Translations Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.0
 */
class RedcoreModelTranslations extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_translations';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'translations_limit';

	/**
	 * Constructor
	 *
	 * @param   array  $config  Configuration array
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'rctranslations_language', 't.rctranslations_language',
				'published', 't.published'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$table = RedcoreHelpersTranslation::getTranslationTable();
		$db	= $this->getDbo();
		$query = $db->getQuery(true);

		if (empty($table))
		{
			return $query;
		}

		$query->select('o.*')
			->from($db->qn($table->table, 'o'));

		$columns = (array) $table->columns;

		foreach ($columns as $column)
		{
			$query->select($db->qn('t.' . $column, 't_' . $column));
		}

		$query->select(
			array(
				$db->qn('t.rctranslations_id'),
				$db->qn('t.rctranslations_language'),
				$db->qn('t.rctranslations_originals'),
				$db->qn('t.rctranslations_modified'),
				$db->qn('t.rctranslations_state')
			)
		);

		$leftJoinOn = array();

		foreach ($table->primaryKeys as $primaryKey)
		{
			$leftJoinOn[] = 'o.' . $primaryKey . ' = t.' . $primaryKey;
		}

		if ($language = $this->getState('filter.language'))
		{
			$leftJoinOn[] = 't.rctranslations_language = ' . $db->q($language);
		}

		$leftJoinOn = implode(' AND ', $leftJoinOn);

		$query->leftJoin(
			$db->qn(RTranslationTable::getTranslationsTableName($table->table, ''), 't') . (!empty($leftJoinOn) ? ' ON ' . $leftJoinOn . '' : '')
		);

		// Filter search
		$search = $this->getState('filter.search_translations');

		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$searchColumns = array();

			foreach ($columns as $column)
			{
				$searchColumns[] = '(o.' . $column . ' LIKE ' . $search . ')';
				$searchColumns[] = '(t.' . $column . ' LIKE ' . $search . ')';
			}

			if (!empty($searchColumns))
			{
				$query->where('(' . implode(' OR ', $searchColumns) . ')');
			}
		}

		// Ordering
		$orderList = $this->getState('list.ordering');
		$directionList = $this->getState('list.direction');

		$order = !empty($orderList) ? $orderList : 't.rctranslations_language';
		$direction = !empty($directionList) ? $directionList : 'DESC';
		$query->order($db->escape($order) . ' ' . $db->escape($direction));

		return $query;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function getItems()
	{
		$items = parent::getItems();
		$table = RedcoreHelpersTranslation::getTranslationTable();
		$columns = (array) $table->columns;

		if (!empty($items))
		{
			foreach ($items as $itemKey => $item)
			{
				$items[$itemKey]->translationStatus = RedcoreHelpersTranslation::getTranslationItemStatus($item, $columns);
			}
		}

		return $items;
	}
}
