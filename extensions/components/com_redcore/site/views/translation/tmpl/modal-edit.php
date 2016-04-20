<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;
jimport('joomla.html.editor');

RHelperAsset::load('component.bs3.min.css', 'redcore');
?>

	<div class="row">
		<h1><?php echo $this->getTitle(); ?></h1>
	</div>

<?php

echo RLayoutHelper::render(
	'translation.modal',
	array(
		'modal' => true,
		'view' => $this,
	),
	JPATH_ROOT . '/administrator/components/com_redcore/layouts'
);
