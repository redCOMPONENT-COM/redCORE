<?php
/**
 * @package     Redcore
 * @subpackage  Upgrade
 *
 * @copyright   Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Upgrade script for redCORE.
 *
 * @package     Redcore
 * @subpackage  Upgrade
 * @since       1.5
 */
class Com_RedcoreUpdateScript_1_8_3
{
	/**
	 * Performs the upgrade after initial Joomla update for this version
	 *
	 * @param   JInstallerAdapter  $parent  Class calling this method
	 *
	 * @return  bool
	 */
	public function executeAfterUpdate($parent)
	{
		$db = JFactory::getDbo();
		$tables = $this->getInstalledTranslationTables();

		if (!empty($tables))
		{
			foreach ($tables as $table)
			{
				// Reinstall tables with the new installer
				RTranslationTable::installContentElement($table->option, $table->path, true);

				// We need to add new column to each existing translation table
				try
				{
					$newTable = RTranslationTable::getTranslationsTableName($table->table);
					$query = 'ALTER TABLE ' . $db->qn($newTable) . ' ADD COLUMN ' . $db->qn('rctranslations_modified_by') . ' INT(11) NULL DEFAULT NULL;';
					$db->setQuery($query);
					$db->execute();
				}
				catch (RuntimeException $e)
				{
					JLog::add(JText::sprintf('LIB_REDCORE_TRANSLATIONS_CONTENT_ELEMENT_ERROR', $e->getMessage()), JLog::ERROR, 'jerror');
				}
			}
		}

		return true;
	}

	/**
	 * Get list of all translation tables with columns
	 *
	 * @return  array  Array or table with columns columns
	 */
	public function getInstalledTranslationTables()
	{
		$db = JFactory::getDbo();
		$oldTranslate = isset($db->translate) ? $db->translate : false;

		// We do not want to translate this value
		$db->translate = false;

		$component = JComponentHelper::getComponent('com_redcore');

		// We put translation check back on
		$db->translate = $oldTranslate;

		return (array) $component->params->get('translations', array());
	}
}
