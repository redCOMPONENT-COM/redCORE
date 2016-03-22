<?php
/**
 * @package    Redcore.Admin
 *
 * @copyright  Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

if (!class_exists('RBootstrap'))
{
	throw new RuntimeException('Please enable redCORE System plugin!');
}

RBootstrap::bootstrap();

RLoader::registerPrefix('Redcore', dirname(__FILE__));

$app = JFactory::getApplication();

RHtmlMedia::setFramework('bootstrap3');

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
