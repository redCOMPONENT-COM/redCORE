<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Translation Controller
 *
 * @package     Redcore.Backend
 * @subpackage  Controllers
 * @since       1.0
 */
class RedcoreControllerTranslation extends RControllerForm
{
	/**
	 * Constructor.
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$view = $this->getView('translation', 'html');
		$view->setLayout('modal-edit');
		$model = RModel::getAdminInstance('translation', array(), 'com_redcore');
		$view->setModel($model, true);
	}
}
