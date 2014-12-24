<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Helper
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
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
	 * @param   string  $default  Default Content Element Name
	 *
	 * @return  object  Translation Table object
	 */
	public static function getTranslationTable($default = '')
	{
		$contentElement = self::getCurrentContentElement();

		$translationTables = RTranslationHelper::getInstalledTranslationTables();

		return !empty($translationTables['#__' . $contentElement]) ? $translationTables['#__' . $contentElement] : null;
	}

	/**
	 * Gets content element name from request
	 *
	 * @param   string  $default  Default Content Element Name
	 *
	 * @return  string  Content element name
	 */
	public static function getCurrentContentElement($default = '')
	{
		$app = JFactory::getApplication();

		$filter = $app->getUserStateFromRequest('com_redcore.translations.translations.filter', 'filter', array(), 'array');

		$contentElement = $app->input->get->get('contentelement', null);

		if ($contentElement === null && !empty($filter['contentelement']))
		{
			$contentElement = $filter['contentelement'];
		}

		if (JFactory::getApplication()->input->get('view') == 'translation' || $contentElement === null)
		{
			$contentElement = $app->input->getString('contentelement', $default);
		}

		return $contentElement;
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
		if (empty($item->rctranslations_language))
		{
			return array('badge' => 'label label-danger', 'status' => 'JNONE');
		}
		elseif ($item->rctranslations_state != 1)
		{
			return array('badge' => 'label label-danger', 'status' => 'JUNPUBLISHED');
		}
		else
		{
			$originalValues = new JRegistry;

			if (is_array($item->rctranslations_originals))
			{
				$originalValues->loadArray($item->rctranslations_originals);
			}
			else
			{
				$originalValues->loadString((string) $item->rctranslations_originals);
			}

			$translationStatus = array('badge' => 'label label-success', 'status' => 'COM_REDCORE_TRANSLATIONS_STATUS_TRANSLATED');

			foreach ($columns as $column)
			{
				if (md5($item->$column) != $originalValues->get($column))
				{
					$translationStatus = array('badge' => 'label label-warning', 'status' => 'COM_REDCORE_TRANSLATIONS_STATUS_CHANGED');
					break;
				}
			}

			return $translationStatus;
		}
	}
}
