<?php
/**
 * @package     Redcore
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2008 - 2021 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Base field for overridable list of options
 *
 * @package     Redcore
 * @subpackage  Field
 * @since       1.0
 */
class JFormFieldRlist extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $type = 'Rlist';

	/**
	 * Options to pass to the layout
	 *
	 * @var  array
	 */
	public $selectOptions = array();

	/**
	 * Layout to render
	 *
	 * @var  string
	 */
	protected $layout = 'fields.rlist';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.0
	 */
	protected function getInput()
	{
		$layout = !empty($this->element['layout']) ? $this->element['layout'] : $this->layout;

		return RLayoutHelper::render(
			trim($layout),
			array(
				'id'       => $this->id,
				'element'  => $this->element,
				'field'    => $this,
				'multiple' => $this->multiple,
				'name'     => $this->name,
				'options'  => (array) $this->getOptions(),
				'required' => $this->required,
				'value'    => $this->value
			)
		);
	}
}
