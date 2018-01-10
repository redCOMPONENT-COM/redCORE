<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2012 - 2018 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

echo RLayoutHelper::render(
	'config.form',
	array(
		'form' => $this->form
	)
);
