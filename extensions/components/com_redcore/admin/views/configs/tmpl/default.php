<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

echo RLayoutHelper::render(
	'dashboard.extensions',
	array(
		'view' => $this,
		'return' => base64_encode('index.php?option=com_redcore&view=configs'),
		'components' => $this->components,
		'configurationLink' => true,
		'translationLink' => false,
	)
);
