<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Models
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Payment Configuration Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.5
 */
class RedcoreModelPayment_Configuration extends RModelAdmin
{
	/**
	 * Name of the payment plugin
	 *
	 * @var  string
	 */
	public $paymentName = '';

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @throws  RuntimeException
	 *
	 * @since   1.2
	 */
	public function save($data)
	{
		$pluginParams = JFactory::getApplication()->input->get('plugin', array(), 'array');
		$data = array_merge($data, $pluginParams);

		if (parent::save($data))
		{
			return true;
		}

		return false;
	}

	/**
	 * Load item object
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   1.2
	 */
	public function getItem($pk = null)
	{
		$this->paymentName = $this->getState('payment_name', '');

		if ($this->paymentName != '' || $this->getState('process_params', '0') == '1')
		{
			$item = parent::getItem($pk);

			if ($this->paymentName && (empty($item->payment_name) || $item->payment_name != $this->paymentName))
			{
				$db	= $this->getDbo();
				$query = $db->getQuery(true)
					->select('p.params as plugin_params, p.name as plugin_name, p.element, p.enabled, p.extension_id')
					->select('CONCAT("plg_redpayment_", p.element) as plugin_path_name')
					->from($db->qn('#__extensions', 'p'))
					->where($db->qn('p.type') . '= ' . $db->q("plugin"))
					->where($db->qn('p.folder') . '= ' . $db->q("redpayment"))
					->where($db->qn('p.element') . '= ' . $db->q($this->paymentName));
				$db->setQuery($query);

				if ($defaultPlugin = $db->loadObject())
				{
					$item->params = $defaultPlugin->plugin_params;
					$item->payment_name = $this->paymentName;
				}
			}
			else
			{
				$this->paymentName = $item->payment_name;
				$item->params = json_encode($item->params);
			}

			$item->folder = 'redpayment';
			$item->element = $this->paymentName;

			return $item;
		}

		return parent::getItem($pk);
	}
}
