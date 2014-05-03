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

		$Itemid = $app->input->getInt('Itemid', 0);
		$option = $app->input->getString('option', '');
		$view = $app->input->getString('view', '');
		$layout = $app->input->getString('layout', '');
		$id = $app->input->getInt('id', '');

		// Guessing some usual variables to try and get a better match
		$location = ($option != '' ? '&option=' . $option : '')
			. ($view != '' ? '&view=' . $view : '')
			. ($layout != '' ? '&layout=' . $layout : '')
			. ($id != '' ? '&id=' . $id : '');

		if (!$Itemid)
		{
			$menu = $app->getMenu();
			$active = $menu->getActive();

			if ($active)
			{
				$Itemid = $active->id;
			}
		}

		foreach ($languages as $i => $language)
		{
			$languages[$i]->active = ($language->lang_code == $currentLang);
			$languages[$i]->link = RRoute::_('index.php?lang=' . $language->sef . ($Itemid > 0 ? '&Itemid=' . $Itemid : '') . $location);
		}

		return $languages;
	}
}
