<?php
/**
 * @package     RedRad
 * @subpackage  Fields
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDRAD') or die;

JFormHelper::loadFieldClass('list');

/**
 * jQuery UI datepicker field for redbooking.
 *
 * @package     RedRad
 * @subpackage  Fields
 * @since       1.0
 */
class JFormFieldRpublished extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Rpublished';

	/**
	 * The array of values
	 *
	 * @var  string
	 */
	protected $availableStatuses = array(
		array('value' => 1, 'text' => 'JPUBLISHED'),
		array('value' => 0, 'text' => 'JUNPUBLISHED'),
		array('value' => 2, 'text' => 'JARCHIVED'),
		array('value' => -2, 'text' => 'JTRASHED'),
		array('value' => '*', 'text' => 'JALL')
	);

	/**
	 * Get options for the select
	 *
	 * @return  array  Array of objects
	 */
	public function getOptions()
	{
		$options = array();

		if ($this->availableStatuses)
		{
			$activeStatuses = array_keys($this->availableStatuses);

			if ($this->element['statuses'])
			{
				$activeStatuses = explode(',', $this->element['statuses']);
			}

			foreach ($this->availableStatuses as $status)
			{
				if (in_array($status['value'], $activeStatuses))
				{
					// Translate the statuses
					$status['text'] = JText::_($status['text']);
					$options[] = (object) $status;
				}
			}
		}

		if (!$options)
		{
			return parent::getOptions();
		}

		return array_merge(parent::getOptions(), $options);
	}
}
