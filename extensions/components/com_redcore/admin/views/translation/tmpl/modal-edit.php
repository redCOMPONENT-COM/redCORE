<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2012 - 2018 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;
jimport('joomla.html.editor');

$contentLanguages = JLanguageHelper::getLanguages();

echo RLayoutHelper::render(
	'translation.modal',
	array(
		'modal' => true,
		'view' => $this,
	),
	JPATH_ROOT . '/administrator/components/com_redcore/layouts'
);
