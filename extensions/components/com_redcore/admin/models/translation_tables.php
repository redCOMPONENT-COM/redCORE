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
 * Translation Tables Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.8
 */
class RedcoreModelTranslation_Tables extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_translation_tables';

	/**
	 * Limitstart field used by the pagination
	 *
	 * @var  string
	 */
	protected $limitField = 'translation_tables_limit';

	/**
	 * xml Files from webservice folder
	 *
	 * @var  array
	 */
	public $xmlFiles = array();

	/**
	 * Number of available xml files for install
	 *
	 * @var  integer
	 */
	public $xmlFilesAvailable = 0;

	/**
	 * Number of available xml files for install
	 *
	 * @var  integer
	 */
	public $languages = array();

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
				'extension_name',
			);
		}

		$this->languages = version_compare(JVERSION, '3.7', '<') ? JFactory::getLanguage()->getKnownLanguages() : JLanguageHelper::getKnownLanguages();

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$db	= $this->getDbo();

		$query = $db->getQuery(true)
			->select('tt.*')
			->from($db->qn('#__redcore_translation_tables', 'tt'));

		// Filter search
		$search = $this->getState('filter.search_translation_tables');

		if (!empty($search))
		{
			$search = $db->quote('%' . $db->escape($search, true) . '%');
			$query->where('(tt.name LIKE ' . $search . ' OR tt.title LIKE ' . $search . ')');
		}

		if ($extensionName = $this->getState('filter.extension_name'))
		{
			$query->where('tt.extension_name = ' . $db->q($extensionName));
		}

		// Ordering
		$orderList     = $this->getState('list.ordering');
		$directionList = $this->getState('list.direction');

		$order     = !empty($orderList) ? $orderList : 'tt.name';
		$direction = !empty($directionList) ? $directionList : 'ASC';
		$query->order($db->escape($order) . ' ' . $db->escape($direction));

		return $query;
	}

	/**
	 * Method to get an array of data items. override to add content items
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.2
	 */
	public function getItems()
	{
		$items = parent::getItems();

		foreach ($items as $key => $item)
		{
			$rowCount                      = RedcoreHelpersTranslation::getTableRowCount($item);
			$items[$key]->original_rows    = isset($rowCount['original_rows']) ? $rowCount['original_rows'] : 0;
			$items[$key]->translation_rows = isset($rowCount['translation_rows']) ? $rowCount['translation_rows'] : 0;
		}

		return $items;
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 */
	protected function setXmlFiles()
	{
		$this->xmlFiles          = RTranslationContentElement::getContentElements(true);
		$this->xmlFilesAvailable = count($this->xmlFiles);
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.2
	 */
	public function getXmlFiles()
	{
		$this->setXmlFiles();

		return $this->xmlFiles;
	}
}
