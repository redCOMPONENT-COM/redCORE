<?php
/**
 * Command line script for executing PHPCS during a Travis build.
 *
 * @copyright  Copyright (C) 2008 - 2015 redCOMPONENT.com, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Only run on the CLI SAPI
(php_sapi_name() == 'cli' ?: die('CLI only'));

// Script defines
define('REPO_BASE', dirname(dirname(__DIR__)));

// Require Composer autoloader
if (!file_exists(REPO_BASE . '/tests/vendor/autoload.php'))
{
	fwrite(STDOUT, "\033[37;41mThis script requires Composer to be set up, please run 'composer install' first.\033[0m\n");
}

require REPO_BASE . '/tests/vendor/autoload.php';

// Welcome message
fwrite(STDOUT, "\033[32;1mInitializing PHP_CodeSniffer checks.\033[0m\n");

// Ignored files
$ignored = array(
	REPO_BASE . '/extensions/components/com_redcore/admin/views/*/tmpl/*',
	REPO_BASE . '/extensions/components/com_redcore/admin/layouts/*',
	REPO_BASE . '/extensions/components/com_redcore/site/views/*/tmpl/*',
	REPO_BASE . '/extensions/components/com_redcore/site/layouts/*',
	REPO_BASE . '/extensions/libraries/redcore/api/hal/document/resource.php',
	REPO_BASE . '/extensions/libraries/redcore/api/hal/document/link.php',
	REPO_BASE . '/extensions/libraries/redcore/api/oauth2/*',
	REPO_BASE . '/extensions/libraries/redcore/layouts/*',
	REPO_BASE . '/extensions/libraries/redcore/model/admin.php',
	REPO_BASE . '/extensions/libraries/redcore/oauth/*',
	REPO_BASE . '/extensions/libraries/redcore/joomla/*',
	REPO_BASE . '/extensions/libraries/redcore/controller/admin.php',
	REPO_BASE . '/extensions/libraries/redcore/form/form.php',
	REPO_BASE . '/extensions/libraries/redcore/database/sqlparser/lexersplitter.php',
	REPO_BASE . '/extensions/libraries/redcore/database/sqlparser/positioncalculator.php',
	REPO_BASE . '/extensions/libraries/redcore/database/sqlparser/sqlcreator.php',
	REPO_BASE . '/extensions/libraries/redcore/database/sqlparser/sqllexer.php',
	REPO_BASE . '/extensions/libraries/redcore/database/sqlparser/sqlparser.php',
	REPO_BASE . '/extensions/libraries/redcore/database/sqlparser/sqlparserutils.php',
	REPO_BASE . '/extensions/libraries/redcore/database/driver.php',
	REPO_BASE . '/extensions/libraries/redcore/table/*',
	REPO_BASE . '/extensions/libraries/redcore/media/redcore/lib/*',
	REPO_BASE . '/extensions/modules/site/mod_redcore_language_switcher/tmpl/*',
	REPO_BASE . '/extensions/plugins/redpayment/paypal/form/*',
);

// Build the options for the sniffer
$options = array(
	'files'        => array(
		REPO_BASE . '/extensions/plugins',
		REPO_BASE . '/extensions/components',
		REPO_BASE . '/extensions/modules',
		REPO_BASE . '/extensions/libraries',
	),
	'standard'     => array( REPO_BASE . '/tests/checkers/phpcs/Joomla'),
	'ignored'      => $ignored,
	'showProgress' => true,
	'verbosity' => false,
	'extensions' => array('php')
);

// Instantiate the sniffer
$phpcs = new PHP_CodeSniffer_CLI;

// Ensure PHPCS can run, will exit if requirements aren't met
$phpcs->checkRequirements();

// Run the sniffs
$numErrors = $phpcs->process($options);

// If there were errors, output the number and exit the app with a fail code
if ($numErrors)
{
	fwrite(STDOUT, sprintf("\033[37;41mThere were %d issues detected.\033[0m\n", $numErrors));
	exit(1);
}
else
{
	fwrite(STDOUT, "\033[32;1mThere were no issues detected.\033[0m\n");
	exit(0);
}
