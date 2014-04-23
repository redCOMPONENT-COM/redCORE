<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Base View
 *
 * @package     Redcore.Backend
 * @subpackage  Helper
 * @since       1.0
 */
abstract class RedcoreHelpersTranslation extends JObject
{
	/**
	 * Gets translation table object
	 *
	 * @return  object  Translation Table object
	 */
	public static function getTranslationTable()
	{
		$contentElement = JFactory::getApplication()->input->getString('contentelement', '');
		$translationTables = RTranslationHelper::getInstalledTranslationTables();

		return !empty($translationTables['#__' . $contentElement]) ? $translationTables['#__' . $contentElement] : null;
	}

	/**
	 * Gets translation item status
	 *
	 * @param   object  $item     Translate item object
	 * @param   array   $columns  List of columns used in translation
	 *
	 * @return  string  Translation Item status
	 */
	public static function getTranslationItemStatus($item, $columns)
	{
		if (empty($item->t_rctranslations_language))
		{
			return array('badge' => 'badge-important', 'status' => 'JNONE');
		}
		elseif ($item->t_rctranslations_state != 1)
		{
			return array('badge' => 'badge-important', 'status' => 'JUNPUBLISHED');
		}
		else
		{
			$originalValues = new JRegistry;
			$originalValues->loadString($item->t_rctranslations_originals);
			$translationStatus = array('badge' => 'badge-success', 'status' => 'COM_REDCORE_TRANSLATIONS_STATUS_TRANSLATED');

			foreach ($columns as $column)
			{
				if (md5($item->$column) != $originalValues->get($column))
				{
					$translationStatus = array('badge' => 'badge-warning', 'status' => 'COM_REDCORE_TRANSLATIONS_STATUS_CHANGED');
					break;
				}
			}

			return $translationStatus;
		}
	}
}
