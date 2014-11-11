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
	 * @return	array  Language list
	 */
	public static function getList()
	{
		$app = JFactory::getApplication();
		$languages = JLanguageHelper::getLanguages();
		$currentLang = JLanguageHelper::detectLanguage();
		$db = JFactory::getDbo();

		$Itemid = $app->input->getInt('Itemid', 0);
		$uri = new JURI(Juri::current());
		$uri->delVar('lang');
		$uri->delVar('Itemid');
		$location = htmlspecialchars($uri->getQuery());

		if (!$Itemid)
		{
			$active = $app->getMenu()->getActive();

			if ($active)
			{
				$Itemid = $active->id;
			}
		}

		foreach ($languages as $i => $language)
		{
			$db->forceLanguageTranslation = $language->lang_code;
			self::resetMenuItems();
			$db->forceLanguageTranslation = false;
			$languages[$i]->active = ($language->lang_code == $currentLang);
			$languages[$i]->link = RRoute::_('index.php?' . $location . '&lang=' . $language->sef . ($Itemid > 0 ? '&Itemid=' . $Itemid : ''));
		}

		self::resetMenuItems();

		return $languages;
	}

	/**
	 * Function for resetting menu items so they can be loaded with separate language aliases
	 *
	 * @return	array  Language list
	 */
	public static function resetMenuItems()
	{
		$menu = JFactory::getApplication()->getMenu();
		$menu->load();
		$menuItems = $menu->getMenu();

		foreach ($menuItems as $item)
		{
			if ($item->home)
			{
				$menu->setDefault($item->id, trim($item->language));
			}

			$item = $menu->getItem($item->id);

			// Decode the item params
			$result = new JRegistry;
			$result->loadString($item->params);
			$item->params = $result;
		}
	}
}
