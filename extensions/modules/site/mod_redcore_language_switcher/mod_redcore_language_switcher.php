<?php
/**
 * @package     Redcore.Module.LanguageSwitcher
 * @subpackage  mod_redcore_language_switcher
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die('Restricted access');

$redcoreLoader = JPATH_LIBRARIES . '/redcore/bootstrap.php';

if (!file_exists($redcoreLoader) || !JPluginHelper::isEnabled('system', 'redcore'))
{
	throw new Exception(JText::_('MOD_REDCORE_LANGUAGE_REDCORE_INIT_FAILED'), 404);
}

$app = JFactory::getApplication();

require_once dirname(__FILE__) . '/helper.php';
$helper = new ModRedCORELanguageSwitcherHelper;

RHelperAsset::load('mod_redcore_language_switcher.min.css', 'mod_redcore_language_switcher');

// Module specific variables
$headerText	= JString::trim($params->get('header_text'));
$footerText	= JString::trim($params->get('footer_text'));

$list = ModRedCORELanguageSwitcherHelper::getList($params);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$layout = $params->get('layout', 'default');
require JModuleHelper::getLayoutPath('mod_redcore_language_switcher', $layout);
