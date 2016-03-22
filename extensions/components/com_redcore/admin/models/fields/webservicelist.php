<?php
/**
 * @package     Redcore
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Field to load a list of database tables
 *
 * @package     Redcore
 * @subpackage  Field
 * @since       1.4
 */
class JFormFieldWebservicelist extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.4
	 */
	public $type = 'Webservicelist';

	/**
	 * A static cache.
	 *
	 * @var  array
	 */
	protected $cache = array();

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		$options = array();

		// Get the paths.
		$items = $this->getWebservices();

		// Build the field options.
		if (!empty($items))
		{
			foreach ($items as $item)
			{
				$options[] = JHtml::_('select.option', $item->identifier, $item->displayName);
			}
		}

		return array_merge(parent::getOptions(), $options);
	}

	/**
	 * Method to get the list of paths.
	 *
	 * @return  array  An array of path names.
	 */
	protected function getWebservices()
	{
		if (empty($this->cache))
		{
			$db = JFactory::getDbo();

			$query = $db->getQuery(true)
				->select('CONCAT_WS(" ", ' . $db->qn('client') . ', ' . $db->qn('name') . ', ' . $db->qn('version') . ') as displayName')
				->select('id as identifier')
				->from('#__redcore_webservices')
				->order('client')
				->order('name')
				->order('version');

			$db->setQuery($query);

			$result = $db->loadObjectList();

			if (is_array($result))
			{
				$this->cache = $result;
			}
		}

		return $this->cache;
	}
}
