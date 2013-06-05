<?php
/**
 * Bootstrap file.
 * Including this file into your application will make redRad available to use.
 *
 * @copyright  Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('JPATH_PLATFORM') or die;

define('JPATH_REDRAD', __DIR__ . '/redrad');

// Register the classes for autoload.
JLoader::registerPrefix('R', JPATH_REDRAD);
