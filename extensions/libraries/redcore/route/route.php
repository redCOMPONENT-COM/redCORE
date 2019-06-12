<?php
/**
 * @package     Redcore.Library
 * @subpackage  Base
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * RedCore Route
 *
 * @package     RedCore
 * @subpackage  Base
 * @since       1.0
 */
class RRoute extends JRoute
{
	/**
	 * Optional custom route (from a redCORE compatible app)
	 *
	 * @var  object
	 */
	protected static $customRouteClass = null;

	/**
	 * Sets a custom route class
	 *
	 * @param   object  $setCustomRouteClass  The class corresponding to a redCORE based app
	 *
	 * @return  void
	 */
	public static function setCustomRoute($setCustomRouteClass)
	{
		self::$customRouteClass = $setCustomRouteClass;
	}

	/**
	 * Translates an internal Joomla URL to a humanly readible URL.
	 *
	 * @param   string   $url    Absolute or Relative URI to Joomla resource.
	 * @param   boolean  $xhtml  Replace & by &amp; for XML compilance.
	 * @param   integer  $ssl    Secure state for the resolved URI.
	 *                             1: Make URI secure using global secure site URI.
	 *                             2: Make URI unsecure using the global unsecure site URI.
	 * @param   boolean  $absolute  Return an absolute URL
	 *
	 * @return  The translated humanly readible URL.
	 */
	public static function _($url, $xhtml = true, $ssl = null, $absolute = false)
	{
		if (self::$customRouteClass)
		{
			$getCustomRouteClass = self::$customRouteClass;

			return $getCustomRouteClass::_($url, $xhtml, $ssl);
		}

		return parent::_($url, $xhtml, $ssl, $absolute);
	}
}
