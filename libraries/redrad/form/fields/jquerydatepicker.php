<?php
/**
 * @package     RedRad
 * @subpackage  Fields
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDRAD') or die;

JFormHelper::loadFieldClass('text');

/**
 * jQuery UI datepicker field for redbooking.
 *
 * @package     RedRad
 * @subpackage  Fields
 * @since       1.0
 */
class JFormFieldJQueryDatePicker extends JFormFieldText
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'JQueryDatePicker';

	/**
	 * CSS id selector
	 *
	 * @var  string
	 */
	public $cssId = null;

	/**
	 * Field input
	 *
	 * @var  string
	 */
	public $fieldHtml = null;

	/**
	 * The datepicker options.
	 *
	 * @var  string
	 */
	protected $datepickerOptions = '';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$id = isset($this->element['id']) ? $this->element['id'] : null;
		$this->cssId = '#' . $this->getId($id, $this->element['name']);

		if (isset($this->element['inline']) && $this->element['inline'] == 'true')
		{
			$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
			$this->fieldHtml = '<div id="' . $this->getId($id, $this->element['name']) . '" ' . $class . '></div>';
		}
		else
		{
			$this->fieldHtml = parent::getInput();
		}

		$this->datepickerOptions = $this->getDatepickerOptions();

		return RLayoutHelper::render('fields.jquerydatepicker', $this);
	}

	/**
	 * Build the datepicker JS options
	 *
	 * @return  string  Options in json format
	 */
	protected function getDatepickerOptions()
	{
		$options = new JRegistry;

		$elementsToCheck = array(
			'altField' => 'string',
			'altFormat' => 'string',
			'appendText' => 'string',
			'autoSize' => 'boolean',
			'buttonImage' => 'string',
			'buttonImageOnly' => 'boolean',
			'buttonText' => 'string',
			'calculateWeek' => 'string',
			'changeMonth' => 'boolean',
			'changeYear' => 'boolean',
			// 'closeText' => 'string',
			'constrainInput' => 'boolean',
			// 'currentText' => 'string',
			// 'dateFormat' => 'string',
			// 'dayNames' => 'string',
			// 'dayNamesMin' => 'string',
			// 'dayNamesShort' => 'string',
			'defaultDate' => 'string',
			'duration' => 'string',
			// 'firstDay' => 'string',
			'gotoCurrent' => 'boolean',
			'hideIfNoPrevNext' => 'boolean',
			// 'isRTL' => 'boolean',
			'maxDate' => 'string',
			'minDate' => 'string',
			// 'monthNames' => 'string',
			// 'monthNamesShort' => 'string',
			'navigationAsDateFormat' => 'boolean',
			// 'nextText' => 'string',
			'numberOfMonths' => 'array',
			// 'prevText' => 'string',
			'selectOtherMonths' => 'boolean',
			'shortYearCutoff' => 'integer',
			'showAnim' => 'string',
			'showButtonPanel' => 'boolean',
			'showCurrentAtPos' => 'integer',
			// 'showMonthAfterYear' => 'boolean',
			'showOn' => 'string',
			'showOptions' => 'string',
			'showOtherMonths' => 'boolean',
			'showWeek' => 'boolean',
			'stepMonths' => 'integer',
			// 'weekHeader' => 'string',
			'yearRange' => 'string',
			// 'yearSuffix' => 'string',

		);

		if ($elementsToCheck)
		{
			foreach ($elementsToCheck as $attribute => $type)
			{
				if (!empty($this->element[$attribute]))
				{
					switch ((string) $type)
					{
						case 'bool':
						case 'boolean':
							$value = (boolean) $this->element[$attribute];
							break;
						case 'int':
						case 'integer':
							$value = (integer) $this->element[$attribute];
							break;
						case 'double':
						case 'float':
						case 'real':
							$value = (double) $this->element[$attribute];
							break;
						case 'array':
							$value = str_replace(array('[', ']'), '', $this->element[$attribute]);
							$value = explode(',', $value);
							break;
						default:
							$value = (string) $this->element[$attribute];
							break;
					}

					$options->set($attribute, $value);
				}
			}
		}

		return $options->toString();
	}
}
