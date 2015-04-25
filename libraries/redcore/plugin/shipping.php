<?php
/**
 * @package     Redcore
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * redCORE Shipping plugin
 *
 * @package     Redcore
 * @subpackage  Plugin.Shipping
 * @since       1.0
 */
class RPluginShipping extends RPlugin
{
	/**
	 * Shipping helper class with filled extension data and loaded configuration
	 *
	 * @var  Object
	 */
	public $shippingHelper = null;

	/**
	 * Constructor
	 *
	 * @param   string  &$subject  Subject
	 * @param   array   $config    Configuration
	 *
	 * @throws  UnexpectedValueException
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		include_once JPATH_ADMINISTRATOR . '/components/com_redshop/helpers/shipping.php';

		$this->shippingHelper = new RHelperPlugin($this->extensionId, $this->_type, $this->_name);

	}

	/**
	 * This function will Extend params object with Configuration data for the specific extension based on Application name
	 *
	 * @return  JRegistry  The plugin parameters object
	 */
	protected function getParams()
	{
		$this->params = parent::getParams();

		$itemConfiguration = json_decode($this->shippingHelper->item->custom_data, true);
		$this->params->loadArray($itemConfiguration[$this->shippingHelper->applicationName]);

		return $this->params;
	}
}
