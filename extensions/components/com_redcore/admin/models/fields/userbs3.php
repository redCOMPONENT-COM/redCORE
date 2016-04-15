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
 * Field to select a user ID from a list.
 *
 * @package     Redcore
 * @subpackage  Field
 * @since       1.0
 */
class JFormFieldUserbs3 extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	public $type = 'Userbs3';

	/**
	 * Method to get the options to populate list
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.0
	 */
	protected function getOptions()
	{
		$options = array_merge(parent::getOptions(), $this->getUsers());

		return $options;
	}

	/**
	 * Method to get the users
	 *
	 * @return  mixed  Array of users
	 *
	 * @since   1.6
	 */
	protected function getUsers()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select(
				array(
					$db->qn('a.id', 'value'),
					$db->qn('a.name', 'text'),
				)
			)
			->from($db->qn('#__users', 'a'));

		$db->setQuery($query);
		$items = $db->loadObjectList();

		$options = array();

		if ($items)
		{
			foreach ($items as $item)
			{
				$options[] = JHtml::_('select.option', $item->value, $item->text);
			}
		}

		return $options;
	}
}
