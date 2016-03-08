<?php
/**
 * @package     Redcore.Cli
 * @subpackage  Joomla Required
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

const _JEXEC = 1;

// Load system defines
if (file_exists(dirname(__FILE__) . '/../../defines.php'))
{
	require_once dirname(__FILE__) . '/../../defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', dirname(__FILE__) . '/../..');
	require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

// Import the configuration.
require_once JPATH_CONFIGURATION . '/configuration.php';

$redcoreLoader = JPATH_LIBRARIES . '/redcore/bootstrap.php';

if (file_exists($redcoreLoader))
{
	require_once $redcoreLoader;

	RBootstrap::bootstrap(false);
}
