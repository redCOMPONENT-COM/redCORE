<?php
/**
 * @package     Redcore
 * @subpackage  Fields
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
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
	 * @var string
	 */
	public $picker = 'datepicker';

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
	 * The filter.
	 *
	 * @var    integer
	 */
	protected $filter;

	/**
	 * Show time addon
	 *
	 * @var  boolean
	 */
	protected $showTime = false;

	/**
	 * @var array
	 */
	protected $symbolsMatching = array(
		// Day
		'dd' => 'd',
		'D' => 'D',
		'd' => 'j',
		'DD' => 'l',
		'o' => 'z',
		// Month
		'MM' => 'F',
		'mm' => 'm',
		'M' => 'M',
		'm' => 'n',
		// Year
		'yy' => 'Y',
		'y' => 'y',
		'oo' => 'z',
		'@' => 'U',
		'!' => 'u',
		'ATOM' => JDate::ATOM,
		'COOKIE' => JDate::COOKIE,
		'ISO_8601' => JDate::ISO8601,
		'RFC_822' => JDate::RFC822,
		'RFC_850' => JDate::RFC850,
		'RFC_1036' => JDate::RFC1036,
		'RFC_1123' => JDate::RFC1123,
		'RFC_2822' => JDate::RFC2822,
		'RSS' => JDate::RSS,
		'TICKS' => '\!',
		'TIMESTAMP' => 'U',
		'W3C' => JDate::W3C,
	);

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the `<field>` tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see     JFormField::setup()
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$return = parent::setup($element, $value, $group);

		if ($return)
		{
			$this->filter   = (string) $this->element['filter'] ? (string) $this->element['filter'] : '';
			$this->showTime = (string) $this->element['showTime'] && (string) $this->element['showTime'] == 'true' ? true : false;
		}

		return $return;
	}

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'filter':
			case 'showTime':
				return $this->$name;
		}

		return parent::__get($name);
	}

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'filter':
				$this->$name = (string) $value;
				break;
			case 'showTime':
				if (is_bool($value))
				{
					$this->$name = $value;
				}
				elseif ($value == 'true')
				{
					$this->$name = true;
				}
				else
				{
					$this->$name = false;
				}

				break;

			default:
				parent::__set($name, $value);
		}
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		$id          = isset($this->element['id']) ? $this->element['id'] : null;
		$this->cssId = '#' . $this->getId($id, $this->element['name']);

		// We will add a rdatepicker class to solve common styling issues
		$this->element['class'] = $this->element['class'] ? 'rdatepicker ' . $this->element['class'] : 'rdatepicker';

		// Get some system objects.
		$config    = JFactory::getConfig();
		$user      = JFactory::getUser();
		$phpFormat = $this->dateFormatJQueryUIToPHP($this->getAttribute('dateFormat'));

		if ($this->showTime == true)
		{
			$phpFormat   .= ' H:i:s';
			$this->picker = 'datetimepicker';
		}
		else
		{
			$this->picker = 'datepicker';
		}

		// If a known filter is given use it. Allowed data just like database format 'Y-m-d H:i:s'
		switch (strtoupper($this->filter))
		{
			case 'SERVER_UTC':
				// Convert a date to UTC based on the server timezone.
				if ($this->value && $this->value != JFactory::getDbo()->getNullDate())
				{
					// Get a date object based on the correct timezone.
					$date = JFactory::getDate($this->value, 'UTC');
					$date->setTimezone(new DateTimeZone($config->get('offset')));

					// Transform the date string.
					$this->value = $date->format($phpFormat, true, false);
				}

				break;

			case 'USER_UTC':
				// Convert a date to UTC based on the user timezone.
				if ($this->value && $this->value != JFactory::getDbo()->getNullDate())
				{
					// Get a date object based on the correct timezone.
					$date = JFactory::getDate($this->value, 'UTC');

					$date->setTimezone(new DateTimeZone($user->getParam('timezone', $config->get('offset'))));

					// Transform the date string.
					$this->value = $date->format($phpFormat, true, false);
				}

				break;
		}

		if (isset($this->element['inline']) && $this->element['inline'] == 'true')
		{
			$class           = ' class="' . (string) $this->element['class'] . '"';
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
	 * Matches each symbol of PHP date format standard
	 * with jQuery equivalent codeword
	 *
	 * @param   string  $jQueryFormat  $jQueryFormat date format
	 *
	 * @author Tristan Jahier
	 *
	 * @return  string
	 */
	protected function dateFormatJQueryUIToPHP($jQueryFormat)
	{
		$phpFormat = "";
		$escaping  = false;
		$length    = strlen($jQueryFormat);

		for ($i = 0; $i < $length; $i++)
		{
			$char = $jQueryFormat[$i];

			// JQuery date format escaping character
			if ($char === '\'')
			{
				if ($escaping)
				{
					$escaping = false;
				}
				else
				{
					$i++;
					$phpFormat .= '\\' . $jQueryFormat[$i];
					$escaping   = true;
				}
			}
			else
			{
				if ($escaping || !($findMatch = $this->findMatch(substr($jQueryFormat, $i))))
				{
					if ($char == ' ')
					{
						$phpFormat .= $char;
					}
					else
					{
						$phpFormat .= "\\" . $char;
					}
				}
				else
				{
					$phpFormat .= $this->symbolsMatching[$findMatch];
					$i         += strlen($findMatch) - 1;
				}
			}
		}

		return $phpFormat;
	}

	/**
	 * Find match date template
	 *
	 * @param   string  $string  Current string
	 *
	 * @return boolean|string
	 */
	protected function findMatch($string)
	{
		$length  = 0;
		$itemKey = false;

		foreach ($this->symbolsMatching as $key => $item)
		{
			if (strpos($string, $key) === 0 && $length < strlen($item))
			{
				$length  = strlen($item);
				$itemKey = $key;
			}
		}

		return $itemKey;
	}

	/**
	 * Build the datepicker JS options
	 *
	 * @return  string  Options in json format
	 */
	protected function getDatepickerOptions()
	{
		$return = '{';

		$optionsToCheck = array(
			'altField'               => array('type' => 'string'),
			'altFormat'              => array('type' => 'string'),
			'appendText'             => array('type' => 'string'),
			'autoSize'               => array('type' => 'boolean'),
			'buttonImage'            => array('type' => 'string'),
			'buttonImageOnly'        => array('type' => 'boolean'),
			'buttonText'             => array('type' => 'string', 'default' => '<i class="icon-calendar icon-2x"></i>'),
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
			'onSelect'               => array('type' => 'function'),
			'timeFormat'             => array('type' => 'string', 'default' => 'HH:mm:ss')
		);

		if ($this->disabled || $this->readonly)
		{
			// Do not show a button
			$optionsToCheck['showOn']['default'] = 'focus';
		}

		if ($optionsToCheck)
		{
			$options = array();

			foreach ($optionsToCheck as $attribute => $params)
			{
				if (!empty($this->element[$attribute]) || isset($params['default']))
				{
					$value  = isset($this->element[$attribute]) ? $this->element[$attribute] : $params['default'];
					$option = '"' . $attribute . '":';

					switch ((string) $params['type'])
					{
						case 'bool':
						case 'boolean':
							$option .= (boolean) $value;
							break;
						case 'int':
						case 'integer':
							$option .= (integer) $value;
							break;
						case 'double':
						case 'float':
						case 'real':
							$option .= (double) $value;
							break;
						case 'array':
							$value   = str_replace(array('[', ']'), '', $value);
							$option .= json_encode(explode(',', $value));
							break;

						// Do nothing with value
						case 'function':
							$option .= $value;
							break;
						default:
							$option .= json_encode((string) $value);
							break;
					}

					$options[] = $option;
				}
			}

			$return .= implode(',', $options);
		}

		$return .= '}';

		return $return;
	}
}
