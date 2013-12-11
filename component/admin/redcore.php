<?php
/**
 * @package    Redcore.Admin
 *
 * @copyright  Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

if (!class_exists('RLoader'))
{
	throw new RuntimeException('Please enable redCORE System plugin!');
}

RLoader::registerPrefix('Redcore', dirname(__FILE__));

$app = JFactory::getApplication();

// Check access.
if (!JFactory::getUser()->authorise('core.manage', 'com_redcore'))
{
	$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');

	return false;
}

// Instanciate and execute the front controller.
$controller = JControllerLegacy::getInstance('Redcore');
$controller->execute($app->input->get('task'));
$controller->redirect();
