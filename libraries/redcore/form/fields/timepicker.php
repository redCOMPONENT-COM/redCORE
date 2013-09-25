<?php
/**
 * @package     Redcore
 * @subpackage  Fields
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Bootstrap timepicker field.
 *
 * @package     Redcore
 * @subpackage  Fields
 * @since       1.0
 */
class JFormFieldTimePicker extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'TimePicker';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$class = $this->element['class'] ? $this->element['class'] : '';

		return RLayoutHelper::render('fields.timepicker',
			array(
				'field'    => $this,
				'class'    => $class,
				'id'       => $this->id,
				'required' => $this->required,
				'name'     => $this->name,
				'value'    => $this->value
			)
		);
	}

	/**
	 * Get the field options as a js string.
	 *
	 * @return  string  The options.
	 */
	public function getOptions()
	{
		// Prepare the params.
		$template = $this->element['template'] ? $this->element['template'] : 'dropdown';
		$minuteStep = (isset($this->element['minute_step']) && !empty($this->element['minute_step'])) ?
			(int) $this->element['minute_step'] : 15;
		$secondStep = (isset($this->element['second_step']) && !empty($this->element['second_step'])) ?
			(int) $this->element['second_step'] : 15;
		$showSeconds = RHelperString::toBool($this->element['seconds']) ? 'true' : 'false';
		$showMeridian = RHelperString::toBool($this->element['meridian']) ? 'true' : 'false';
		$showInputs = RHelperString::toBool($this->element['inputs']) ? 'true' : 'false';
		$disableFocus = RHelperString::toBool($this->element['disable_focus']) ? 'true' : 'false';
		$modalBackdrop = RHelperString::toBool($this->element['backdrop']) ? 'true' : 'false';

		// If we don't have a value.
		if (empty($this->value))
		{
			$defaultTime = $this->element['default'] ? $this->element['default'] : 'current';
		}

		else
		{
			$defaultTime = 'value';
		}

		$options = new JRegistry;

		$options->loadArray(
			array(
				'template' => $template,
				'minuteStep' => $minuteStep,
				'showSeconds' => $showSeconds,
				'secondStep' => $secondStep,
				'defaultTime' => $defaultTime,
				'showMeridian' => $showMeridian,
				'showInputs' => $showInputs,
				'disableFocus' => $disableFocus,
				'modalBackdrop' => $modalBackdrop
			)
		);

		return $options->toString();
	}
}
