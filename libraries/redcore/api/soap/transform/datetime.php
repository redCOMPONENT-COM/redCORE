<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Interface to transform api output for SOAP
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.4
 */
final class RApiSoapTransformDatetime extends RApiSoapTransformBase
{
	/**
	 * string $type
	 *
	 * @var    string  Base SOAP type
	 * @since  1.4
	 */
	public $type = 's:dateTime';

	/**
	 * Constructor function
	 *
	 * @since  1.4
	 */
	public function __construct()
	{
		$this->defaultValue = date('Y-m-d h:i:s');
	}
}
