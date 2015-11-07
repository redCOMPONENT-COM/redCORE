<?php
/**
 * @package     Redcore.Module.LanguageSwitcher
 * @subpackage  mod_redcore_language_switcher
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
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
		$db = JFactory::getDbo();

		$Itemid = $app->input->getInt('Itemid', 0);
		$uri = new JURI(Juri::current());
		$uri->delVar('lang');
		$uri->delVar('Itemid');
		$location = htmlspecialchars($uri->getQuery());

		if (!empty($location))
		{
			$location .= '&';
		}

		if (!$Itemid)
		{
			$active = $app->getMenu()->getActive();

			if ($active)
			{
				$Itemid = $active->id;
			}
		}

		// For every language we load menu items language specific alias and params
		foreach ($languages as $i => $language)
		{
			$db->forceLanguageTranslation = $language->lang_code;
			RMenu::resetJoomlaMenuItems();
			$db->forceLanguageTranslation = false;
			$languages[$i]->active = $language->lang_code == JFactory::getLanguage()->getTag();
			$languages[$i]->link = RRoute::_('index.php?' . $location . 'lang=' . $language->sef . ($Itemid > 0 ? '&Itemid=' . $Itemid : ''));
		}

		// After we are done we reset it the way it was
		RMenu::resetJoomlaMenuItems();

		return $languages;
	}
}
