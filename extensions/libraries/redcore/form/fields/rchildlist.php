<?php
/**
 * @package     Redcore
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

/**
 * Field a list dependent
 *
 * @package     Redcore
 * @subpackage  Field
 * @since       1.0
 *
 * @deprecated  1.7  Use RFormFieldChildlist
 */
class JFormFieldRchildlist extends RFormFieldChildlist
{
	/**
	 * Layout to render
	 *
	 * @var  string
	 */
	protected $layout = 'fields.rchildlist';
}
