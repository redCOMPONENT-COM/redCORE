<?php
/**
 * @package     Redcore
 * @subpackage  Html
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Chart HTML class.
 *
 * @package     Redcore
 * @subpackage  Html
 * @since       1.0
 */
abstract class RHtmlRchart
{
	/**
	 * Extension name to use in the asset calls
	 * Basically the media/com_xxxxx folder to use
	 */
	const EXTENSION = 'redcore';

	/**
	 * Array containing information for loaded files
	 *
	 * @var  array
	 */
	protected static $loaded = array();

	/**
	 * Array containing information for loaded files
	 *
	 * @var  array
	 */
	protected static $charts = array(
		'line'      => 'Line',
		'bar'       => 'Bar',
		'radar'     => 'Radar',
		'polararea' => 'PolarArea',
		'pie'       => 'Pie',
		'doughnut'  => 'Doughnut',
		);

	/**
	 * Load the Chart framework
	 *
	 * @return  void
	 */
	public static function framework()
	{
		// Only load once
		if (!empty(static::$loaded[__METHOD__]))
		{
			return;
		}

		JHtmlRjquery::framework();

		RHelperAsset::load('lib/Chart-js/Chart.min.js', self::EXTENSION);

		static::$loaded[__METHOD__] = true;

		return;
	}

	/**
	 * Function to receive & pre-process javascript options
	 *
	 * @param   mixed  $options  Associative array/JRegistry object with options
	 *
	 * @return  JRegistry        Options converted to JRegistry object
	 */
	private static function options2Jregistry($options)
	{
		// Support options array
		if (is_array($options))
		{
			$options = new JRegistry($options);
		}

		if (!($options instanceof Jregistry))
		{
			$options = new JRegistry;
		}

		// Fix the width to resolve by default
		if ($options->get('responsive', null) === null)
		{
			$options->set('responsive', 'true');
		}

		return $options;
	}

	/**
	 * Returns proper name of the chart type
	 *
	 * @param   string  $chartType  Chart Type
	 *
	 * @return  string
	 */
	public static function getChartType($chartType)
	{
		return isset(self::$charts[strtolower($chartType)]) ? self::$charts[strtolower($chartType)] : 'Line';
	}

	/**
	 * Add Line Chart
	 * https://github.com/nnnick/Chart.js
	 *
	 * @param   string  $chartType   Chart types: Line, Bar, Radar, PolarArea, Pie, Doughnut
	 * @param   string  $selector    CSS Selector to initialise selects to the canvas element
	 * @param   mixed   $data        Data for the chart
	 * @param   array   $options     Optional array with options
	 * @param   string  $jsVariable  Id of the javascript chart variable
	 *
	 * @return  void
	 */
	public static function addChart($chartType = 'Line', $selector = '.myChart', $data = '{}', $options = null, $jsVariable = 'ctx')
	{
		self::framework();

		// Generate options with default values
		$options = static::options2Jregistry($options);

		$chartType = self::getChartType($chartType);

		// Fix legend error in chart.js
		self::legendTemplate($chartType, $options);

		if (is_object($data))
		{
			$data = ArrayHelper::fromObject($data);
		}

		if (is_array($data) || empty($data))
		{
			$data = json_encode($data);
		}

		$script = "
			(function($){
				$(document).ready(function () {
					var " . $jsVariable . "Data = " . $data . ";
					var " . $jsVariable . " = $('" . $selector . "').get(0).getContext('2d');
					var " . $jsVariable . "ChartObject = new Chart(" . $jsVariable . ");
					var " . $jsVariable . "Chart = " . $jsVariable . "ChartObject." . $chartType . "(" . $jsVariable . "Data, " . $options->toString() . ");
					var $" . $jsVariable . "Legend = $('" . $selector . "Legend');
					if ($" . $jsVariable . "Legend.length > 0)
					{
						$" . $jsVariable . "Legend.html(" . $jsVariable . "Chart.generateLegend());
					}
				});
			})(jQuery);
		";

		JFactory::getDocument()->addScriptDeclaration($script);
	}

	/**
	 * Gets different color for given string
	 *
	 * @param   string  $suffix      Add different id to get different color
	 * @param   string  $hashString  Change to get different palette
	 *
	 * @return  array
	 */
	public static function getColorFromHash($suffix = '1', $hashString = 'color')
	{
			$hash = md5($hashString . $suffix);

			return array(
				// R
				hexdec(substr($hash, 0, 2)),
				// G
				hexdec(substr($hash, 2, 2)),
				// B
				hexdec(substr($hash, 4, 2))
			);
	}

	/**
	 * Process legend Template for Chart
	 *
	 * @param   string     $chartType  Chart types: Line, Bar, Radar, PolarArea, Pie, Doughnut
	 * @param   JRegistry  &$options   Options fot the chart
	 *
	 * @return  array
	 */
	public static function legendTemplate($chartType, &$options)
	{
		if (in_array($chartType, array('PolarArea', 'Pie', 'Doughnut')))
		{
			// Fix legend error in chart.js
			if ($options->get('legendTemplate', null) !== null)
			{
				$template = $options->get('legendTemplate', null);
				$template = str_replace(array('datasets', 'strokeColor'), array('segments', 'fillColor'), $template);
				$options->set('legendTemplate', $template);
			}
		}
	}

	/**
	 * get currencies as options, with code as value
	 *
	 * @return array
	 */
	public static function getChartOptions()
	{
		$options = array();

		foreach (self::$charts as $code => $chart)
		{
			$options[] = JHTML::_('select.option', $code, $chart);
		}

		return $options;
	}

	/**
	 * get default Chart Legend Html template
	 *
	 * @return array
	 */
	public static function getDefaultLegendHtml()
	{
		return "<ul class=\"chart-legend <%=name.toLowerCase()%>-legend\">"
			. "<% for (var i=0; i<datasets.length; i++){%><li>"
			. "<div style=\"background-color:<%=datasets[i].strokeColor%>\">&nbsp;</div>"
			. "<%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>";
	}
}
