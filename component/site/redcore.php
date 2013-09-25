<?php
/**
 * @package     Redcore.Site
 * @subpackage  Entry
 *
 * @copyright   Copyright (C) 2013 Roberto Segura López. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

// Register component prefix
JLoader::registerPrefix('Redcore', __DIR__);

$app = JFactory::getApplication();

// Instanciate and execute the front controller.
$controller = JControllerLegacy::getInstance('Redcore');
$controller->execute($app->input->get('task'));
$controller->redirect();
