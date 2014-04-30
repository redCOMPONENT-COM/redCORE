<?php
/**
 * @package     Redcore.Module.LanguageSwitcher
 * @subpackage  mod_redcore_language_switcher
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die('Restricted access');

$redCORELoader = JPATH_LIBRARIES . '/redcore/bootstrap.php';

if (!file_exists($redCORELoader))
{
	throw new Exception(JText::_('COM_REDCORE_INIT_FAILED'), 404);
}

// Bootstraps redSHOPB2B application
require_once $redCORELoader;
RBootstrap::bootstrap();

$app = JFactory::getApplication();

require_once dirname(__FILE__) . '/helper.php';
$helper = new ModRedCORELanguageSwitcherHelper;

JHtml::stylesheet('mod_redcore_language_switcher/mod_redcore_language_switcher.css', false, true);

// Module specific variables
$headerText	= JString::trim($params->get('header_text'));
$footerText	= JString::trim($params->get('footer_text'));

$list = ModRedCORELanguageSwitcherHelper::getList($params);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_redcore_language_switcher');
