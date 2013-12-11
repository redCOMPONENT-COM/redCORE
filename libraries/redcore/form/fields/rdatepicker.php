<?php
/**
 * @package     Redcore
 * @subpackage  Fields
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

JFormHelper::loadFieldClass('rtext');

/**
 * jQuery UI datepicker field for redbooking.
 *
 * @package     Redcore
 * @subpackage  Fields
 * @since       1.0
 */
class JFormFieldRdatepicker extends JFormFieldRtext
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Rdatepicker';

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
	public $datepickerOptions = '';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$id = isset($this->element['id']) ? $this->element['id'] : null;
		$this->cssId = '#' . $this->getId($id, $this->element['name']);

		// We will add a rdatepicker class to solve common styling issues
		$this->element['class'] = $this->element['class'] ? 'rdatepicker ' . $this->element['class'] : 'rdatepicker';

		if (isset($this->element['inline']) && $this->element['inline'] == 'true')
		{
			$class = ' class="' . (string) $this->element['class'] . '"';
			$this->fieldHtml = '<div id="' . $this->getId($id, $this->element['name']) . '" ' . $class . '></div>';
		}
		else
		{
			$this->fieldHtml = parent::getInput();
		}

		$this->datepickerOptions = $this->getDatepickerOptions();

		return RLayoutHelper::render('fields.rdatepicker', $this);
	}

	/**
	 * Build the datepicker JS options
	 *
	 * @return  string  Options in json format
	 */
	protected function getDatepickerOptions()
	{
		$options = new JRegistry;

		$optionsToCheck = array(
			'altField'               => array('type' => 'string'),
			'altFormat'              => array('type' => 'string'),
			'appendText'             => array('type' => 'string'),
			'autoSize'               => array('type' => 'boolean'),
			'buttonImage'            => array('type' => 'string', 'default' => 'rdatepicker-calendar.gif'),
			'buttonImageOnly'        => array('type' => 'boolean'),
			'buttonText'             => array('type' => 'string'),
			'calculateWeek'          => array('type' => 'string'),
			'changeMonth'            => array('type' => 'boolean'),
			'changeYear'             => array('type' => 'boolean'),
			// 'closeText'           => array('type' => 'string'),
			'constrainInput'         => array('type' => 'boolean'),
			// 'currentText'         => array('type' => 'string'),
			'dateFormat'             => array('type' => 'string', 'default' => 'dd-mm-yy'),
			/**
			 * 'dayNames'            => array('type' => 'string'),
			 * 'dayNamesMin'         => array('type' => 'string'),
			 * 'dayNamesShort'       => array('type' => 'string'),
			 */
			'defaultDate'            => array('type' => 'string'),
			'duration'               => array('type' => 'string'),
			// 'firstDay'            => array('type' => 'string'),
			'gotoCurrent'            => array('type' => 'boolean'),
			'hideIfNoPrevNext'       => array('type' => 'boolean'),
			// 'isRTL'               => array('type' => 'boolean'),
			'maxDate'                => array('type' => 'string'),
			'minDate'                => array('type' => 'string'),
			// 'monthNames'          => array('type' => 'string'),
			// 'monthNamesShort'     => array('type' => 'string'),
			'navigationAsDateFormat' => array('type' => 'boolean'),
			// 'nextText'            => array('type' => 'string'),
			'numberOfMonths'         => array('type' => 'array'),
			// 'prevText'            => array('type' => 'string'),
			'selectOtherMonths'      => array('type' => 'boolean'),
			'shortYearCutoff'        => array('type' => 'integer'),
			'showAnim'               => array('type' => 'string'),
			'showButtonPanel'        => array('type' => 'boolean'),
			'showCurrentAtPos'       => array('type' => 'integer'),
			// 'showMonthAfterYear'  => array('type' => 'boolean'),
			'showOn'                 => array('type' => 'string', 'default' => 'both'),
			'showOptions'            => array('type' => 'string'),
			'showOtherMonths'        => array('type' => 'boolean'),
			'showWeek'               => array('type' => 'boolean'),
			'stepMonths'             => array('type' => 'integer'),
			// 'weekHeader'          => array('type' => 'string'),
			'yearRange'              => array('type' => 'string'),
			// 'yearSuffix'          => array('type' => 'string'),
		);

		if ($optionsToCheck)
		{
			foreach ($optionsToCheck as $attribute => $option)
			{
				if (!empty($this->element[$attribute]) || isset($option['default']))
				{
					$value = isset($this->element[$attribute]) ? $this->element[$attribute] : $option['default'];

					switch ((string) $option['type'])
					{
						case 'bool':
						case 'boolean':
							$value = (boolean) $value;
							break;
						case 'int':
						case 'integer':
							$value = (integer) $value;
							break;
						case 'double':
						case 'float':
						case 'real':
							$value = (double) $value;
							break;
						case 'array':
							$value = str_replace(array('[', ']'), '', $value);
							$value = explode(',', $value);
							break;
						default:
							$value = (string) $value;
							break;
					}

					$options->set($attribute, $value);
				}
			}
		}

		return $options->toString();
	}
}
