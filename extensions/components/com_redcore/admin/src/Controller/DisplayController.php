<?php

namespace Redcore\Component\Redcore\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * @package     Joomla.Administrator
 * @subpackage  com_redcore
 *
 * @copyright   Copyright (C) 2021 redWEb. All rights reserved.
 * @license     GNU General Public License version 2; see LICENSE
 */

/**
 * Default Controller of Redcore component
 *
 * @package     Joomla.Administrator
 * @subpackage  com_redcore
 */
class DisplayController extends BaseController {
	/**
	 * The default view for the display method.
	 *
	 * @var string
	 */
	protected $default_view = 'hello';

	/**
	 * @param   false  $cachable
	 * @param   array  $urlparams
	 *
	 * @return mixed
	 */
	public function display($cachable = false, $urlparams = array()) {
		return parent::display($cachable, $urlparams);
	}

}