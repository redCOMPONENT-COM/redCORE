<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2020 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

echo RLayoutHelper::render(
	'dashboard.extensions',
	array(
		'view' => $this,
		'return' => '',
		'components' => $this->components,
		'configurationLink' => true,
		'translationLink' => true,
	)
);
