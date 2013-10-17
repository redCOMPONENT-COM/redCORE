<?php
/**
 * Bootstrap file.
 * Including this file into your application will make redCORE available to use.
 *
 * @package    Redcore
 * @copyright  Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

define('JPATH_REDCORE', dirname(__FILE__));

require JPATH_REDCORE . '/functions.php';

// Use our own base field
if (!class_exists('JFormField', false))
{
	$baseField = JPATH_LIBRARIES . '/redcore/joomla/form/field.php';

	if (file_exists($baseField))
	{
		require_once $baseField;
	}
}

// Register the classes for autoload.
JLoader::registerPrefix('R', JPATH_REDCORE);

// Setup the RLoader.
RLoader::setup();

// Make available the redCORE fields
JFormHelper::addFieldPath(JPATH_REDCORE . '/form/fields');

// Make available the redCORE form rules
JFormHelper::addRulePath(JPATH_REDCORE . '/form/rules');

// HTML helpers
JHtml::addIncludePath(JPATH_REDCORE . '/html');
RHtml::addIncludePath(JPATH_REDCORE . '/html');

// Load library language
$lang = JFactory::getLanguage();
$lang->load('lib_redcore', JPATH_SITE);
