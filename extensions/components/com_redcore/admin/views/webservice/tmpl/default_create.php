<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2012 - 2018 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

echo RLayoutHelper::render(
	'webservice.operation',
	array(
		'view' => $this,
		'options' => array(
			'operation' => 'create',
			'form'      => $this->form,
			'tabActive' => ' active in ',
			'fieldList' => array('defaultValue', 'isRequiredField', 'isPrimaryField'),
		)
	)
);
