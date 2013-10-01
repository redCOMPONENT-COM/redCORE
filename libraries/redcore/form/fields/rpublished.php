<?php
/**
 * @package     Redcore
 * @subpackage  Fields
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

JFormHelper::loadFieldClass('rpredefinedlist');

/**
 * jQuery UI datepicker field for redbooking.
 *
 * @package     Redcore
 * @subpackage  Fields
 * @since       1.0
 */
class JFormFieldRpublished extends JFormFieldRpredefinedList
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
	protected $predefinedOptions = array(
		1   => 'JPUBLISHED',
		0   => 'JUNPUBLISHED',
		2   => 'JARCHIVED',
		-2  => 'JTRASHED',
		'*' => 'JALL'
	);
}
