<?php
/**
 * @package     Redcore
 * @subpackage  Fields
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;
JFormHelper::loadFieldClass('color');

/**
 * Color picker field.
 *
 * @package     Redcore
 * @subpackage  Fields
 * @since       1.0
 */
class JFormFieldRcolor extends JFormFieldColor
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.3
	 */
	protected $type = 'Rcolor';

	/**
	 * Layout to render
	 *
	 * @var  string
	 */
	protected $layout = 'fields.rcolor';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.3
	 */
	protected function getInput()
	{
		$layout = !empty($this->element['layout']) ? $this->element['layout'] : $this->layout;

		return RLayoutHelper::render(
			$layout,
			array(
				'id'       => $this->id,
				'element'  => $this->element,
				'field'    => $this,
				'name'     => $this->name,
				'required' => $this->required,
				'value'    => $this->value
			)
		);
	}
}