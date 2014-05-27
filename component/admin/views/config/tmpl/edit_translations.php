<?php
/**
 * @package    Redcore.Backend
 * @subpackage Templates
 *
 * @copyright  Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

echo RLayoutHelper::render(
	'translation.tables',
	array(
		'contentElements' => $this->contentElements,
		'missingContentElements' => $this->missingContentElements,
	)
);
