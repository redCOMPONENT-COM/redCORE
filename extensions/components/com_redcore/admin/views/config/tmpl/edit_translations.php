<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2021 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

echo RLayoutHelper::render(
	'translation.tables',
	array(
		'contentElements' => $this->contentElements,
		'componentName' => $this->componentName,
	)
);
