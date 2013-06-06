<?php
/**
 * Bootstrap file.
 * Including this file into your application will make redRad available to use.
 *
 * @package    RedRad
 * @copyright  Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

define('JPATH_REDRAD', __DIR__);

require JPATH_REDRAD . '/inflector/inflector.php';

// Register the classes for autoload.
JLoader::registerPrefix('R', JPATH_REDRAD);

// Make available the redRAD fields
JFormHelper::addFieldPath(JPATH_REDRAD . '/form/fields');

// Make available the redRAD form rules
JFormHelper::addRulePath(JPATH_REDRAD . '/form/rules');

// HTML helpers
JHtml::addIncludePath(JPATH_REDRAD . '/html');
