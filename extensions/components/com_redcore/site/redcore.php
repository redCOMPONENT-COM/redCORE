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
RHtmlMedia::$frameworkOptions['disableMootools'] = false;

// Check access.
if (!JFactory::getUser()->authorise('core.manage', 'com_redcore'))
{
	$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');

	return false;
}

// Load administrator language files
$language = JFactory::getLanguage();
$language->load('joomla', JPATH_ADMINISTRATOR, 'en-GB', true);
$language->load('joomla', JPATH_ADMINISTRATOR, null, true);
$language->load('com_redcore', JPATH_ADMINISTRATOR . '/components/com_redcore', 'en-GB', true);
$language->load('com_redcore', JPATH_ADMINISTRATOR . '/components/com_redcore', null, true);

// Instantiate and execute the front controller.
$controller = JControllerLegacy::getInstance('Redcore');
$controller->execute($app->input->get('task'));
$controller->redirect();
