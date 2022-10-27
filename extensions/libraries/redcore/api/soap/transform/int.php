<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2021 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * Interface to transform api output for SOAP
 *
 * @package     Redcore
 * @subpackage  Api
 * @since       1.4
 */
final class RApiSoapTransformInt extends RApiSoapTransformBase
{
	/**
	 * string $type
	 *
	 * @var    string  Base SOAP type
	 * @since  1.4
	 */
	public $type = 's:int';

	/**
	 * string $defaultValue
	 *
	 * @var    string  Default value when not null
	 * @since  1.4
	 */
	public $defaultValue = '0';
}
