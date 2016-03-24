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
 * Payment Dashboard Model
 *
 * @package     Redcore.Backend
 * @subpackage  Models
 * @since       1.5
 */
class RedcoreModelPayment_Dashboard extends RModelList
{
	/**
	 * Name of the filter form to load
	 *
	 * @var  string
	 */
	protected $filterFormName = 'filter_payment_dashboard';

	/**
	 * Constructor
	 *
	 * @param   array  $config  Configuration array
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'payment_name', 'p.payment_name',
				'extension_name', 'p.extension_name',
				'owner_name', 'p.owner_name',
			);
		}

		parent::__construct($config);
	}
}
