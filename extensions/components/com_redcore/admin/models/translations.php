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
		$table = RTranslationTable::setTranslationTableWithColumn($this->getState('filter.translationTableName', ''));
		$db	= $this->getDbo();
		$query = $db->getQuery(true);

		if (empty($table))
		{
			$query->select('*')
				->from('#__extensions')
				->where('1=2');

			return $query;
		}

		$query->select('o.*')
			->from($db->qn($table->table, 'o'));

		foreach ($table->allColumns as $column)
		{
			if ($column['column_type'] != RTranslationTable::COLUMN_READONLY)
			{
				$query->select($db->qn('t.' . $column['name'], 't_' . $column['name']));
			}
		}

		$query->select(
			array(
				$db->qn('t.rctranslations_id'),
				$db->qn('t.rctranslations_language'),
				$db->qn('t.rctranslations_originals'),
				$db->qn('t.rctranslations_modified'),
				$db->qn('t.rctranslations_modified_by'),
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

		$query->select($db->qn('u.name', 'rctranslations_modified_user'))
			->leftJoin(
				$db->qn('#__users', 'u')
				. ' ON u.id = t.rctranslations_modified_by');

		// Filter search
		$search = $this->getState('filter.search_translations');

		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$searchColumns = array();

			foreach ($table->columns as $column)
			{
				$searchColumns[] = '(o.' . $column . ' LIKE ' . $search . ')';
				$searchColumns[] = '(t.' . $column . ' LIKE ' . $search . ')';
			}

			if (!empty($searchColumns))
			{
				$query->where('(' . implode(' OR ', $searchColumns) . ')');
			}
		}

		if ($authorId = $this->getState('filter.author_id'))
		{
			$query->where('t.rctranslations_modified_by = ' . $db->q($authorId));
		}

		$state = $this->getState('filter.state', '');

		if ($state != '')
		{
			$query->where('t.rctranslations_state = ' . $db->q((int) $state));
		}

		if (!empty($table->filter_query))
		{
			$query->where((string) $table->filter_query);
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
		$table = RTranslationTable::getTranslationTableByName($this->getState('filter.translationTableName', ''));

		if (!empty($items))
		{
			foreach ($items as $itemKey => $item)
			{
				$items[$itemKey]->translationStatus = RTranslationHelper::getTranslationItemStatus($item, $table->columns);
			}
		}

		return $items;
	}
}
