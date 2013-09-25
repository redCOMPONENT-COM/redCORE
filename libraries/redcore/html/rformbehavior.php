<?php
/**
 * @package     Redcore
 * @subpackage  Html
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

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
	 * Array containing information for loaded files
	 *
	 * @var  array
	 */
	protected static $loaded = array();

	/**
	 * Method to load the AJAX Chosen library
	 *
	 * If debugging mode is on an uncompressed version of AJAX Chosen is included for easier debugging.
	 *
	 * @param   JRegistry  $options  Options in a JRegistry object
	 * @param   mixed      $debug    Is debugging mode on? [optional]
	 *
	 * @return  void
	 */
	public static function ajaxchosen(JRegistry $options, $debug = null)
	{
		// Retrieve options/defaults
		$selector       = $options->get('selector', '.tagfield');
		$type           = $options->get('type', 'GET');
		$url            = $options->get('url', null);
		$dataType       = $options->get('dataType', 'json');
		$jsonTermKey    = $options->get('jsonTermKey', 'term');
		$afterTypeDelay = $options->get('afterTypeDelay', '500');
		$minTermLength  = $options->get('minTermLength', '3');

		JText::script('JGLOBAL_KEEP_TYPING');
		JText::script('JGLOBAL_LOOKING_FOR');

		// Ajax URL is mandatory
		if (!empty($url))
		{
			if (isset(self::$loaded[__METHOD__][$selector]))
			{
				return;
			}

			// Include jQuery
			JHtml::_('rjquery.framework');

			// Requires chosen to work
			JHtml::_('rjquery.chosen', $selector, $debug);

			JHtml::_('script', 'jui/ajax-chosen.min.js', false, true, false, false, $debug);
			JFactory::getDocument()->addScriptDeclaration("
				(function($){
					$(document).ready(function () {
						$('" . $selector . "').ajaxChosen({
							type: '" . $type . "',
							url: '" . $url . "',
							dataType: '" . $dataType . "',
							jsonTermKey: '" . $jsonTermKey . "',
							afterTypeDelay: '" . $afterTypeDelay . "',
							minTermLength: '" . $minTermLength . "'
						}, function (data) {
							var results = [];

							$.each(data, function (i, val) {
								results.push({ value: val.value, text: val.text });
							});

							return results;
						});
					});
				})(jQuery);
				"
			);

			self::$loaded[__METHOD__][$selector] = true;
		}

		return;
	}
}
