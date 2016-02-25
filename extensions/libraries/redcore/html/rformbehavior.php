<?php
/**
 * @package     Redcore
 * @subpackage  Html
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

use Joomla\Registry\Registry;

/**
 * Utility class for form related behaviors
 *
 * @package     Redcore
 * @subpackage  Html
 * @since       1.0
 */
abstract class JHtmlRformbehavior extends JHtmlFormbehavior
{
	/**
	 * Method to load the AJAX Chosen library
	 *
	 * If debugging mode is on an uncompressed version of AJAX Chosen is included for easier debugging.
	 *
	 * @param   Registry  $options  Options in a Registry object
	 * @param   mixed     $debug    Is debugging mode on? [optional]
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public static function ajaxchosen(Registry $options, $debug = null)
	{
		// Retrieve options/defaults
		$selector       = $options->get('selector', '.tagfield');
		$type           = $options->get('type', 'GET');
		$url            = $options->get('url', null);
		$dataType       = $options->get('dataType', 'json');
		$jsonTermKey    = $options->get('jsonTermKey', 'term');
		$afterTypeDelay = $options->get('afterTypeDelay', '500');
		$minTermLength  = $options->get('minTermLength', '3');

		// Ajax URL is mandatory
		if (!empty($url))
		{
			if (isset(static::$loaded[__METHOD__][$selector]))
			{
				return;
			}

			// Requires chosen to work
			static::chosen($selector, $debug);

			$displayData = array(
				'url'            => $url,
				'debug'          => $debug,
				'options'        => $options,
				'selector'       => $selector,
				'type'           => $type,
				'dataType'       => $dataType,
				'jsonTermKey'    => $jsonTermKey,
				'afterTypeDelay' => $afterTypeDelay,
				'minTermLength'  => $minTermLength
			);

			JLayoutHelper::render('joomla.html.formbehavior.ajaxchosen', $displayData);

			static::$loaded[__METHOD__][$selector] = true;
		}

		return;
	}
}
