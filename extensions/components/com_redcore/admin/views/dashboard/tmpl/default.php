<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
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
