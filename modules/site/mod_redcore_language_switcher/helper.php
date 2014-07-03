<?php
/**
 * @package     Redcore.Module.LanguageSwitcher
 * @subpackage  mod_redcore_language_switcher
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Redcore Language Switcher Module Helper
 *
 * @package     Redcore
 * @subpackage  Module
 * @since       1.0.0
 */
class ModRedCORELanguageSwitcherHelper
{
	/**
	 * Function for getting the list of languages
	 *
	 * @return	string  Language list
	 */
	public static function getList()
	{
		$app = JFactory::getApplication();
		$languages = JLanguageHelper::getLanguages();
		$currentLang = JLanguageHelper::detectLanguage();
		$db = JFactory::getDbo();

		$Itemid = $app->input->getInt('Itemid', 0);
		$uri = Juri::getInstance();
		$uri->delVar('lang');
		$uri->delVar('Itemid');
		$location = htmlspecialchars($uri->getQuery());
		$menu = $app->getMenu();

		if (!$Itemid)
		{
			$active = $menu->getActive();

			if ($active)
			{
				$Itemid = $active->id;
			}
		}

		foreach ($languages as $i => $language)
		{
			$db->forceLanguageTranslation = $language->lang_code;
			$menu->load();
			$languages[$i]->active = ($language->lang_code == $currentLang);
			$languages[$i]->link = RRoute::_('index.php?' . $location . '&lang=' . $language->sef . ($Itemid > 0 ? '&Itemid=' . $Itemid : ''));
			$db->forceLanguageTranslation = false;
		}

		$menu->load();

		return $languages;
	}
}
