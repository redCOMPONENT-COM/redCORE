<?php
/**
 * @package     Redcore
 * @subpackage  Upgrade
 *
 * @copyright   Copyright (C) 2012 - 2016 redCOMPONENT.com. All rights reserved.
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
class Com_RedcoreUpdateScript_1_8_6
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
		$query = $db->getQuery(true)
			->select($db->qn('params'))
			->from('#__extensions')
			->where($db->qn('type') . ' = ' . $db->q('plugin'))
			->where($db->qn('element') . ' = ' . $db->q('redcore'))
			->where($db->qn('folder') . ' = ' . $db->q('system'));

		$params = $db->setQuery($query)->loadResult();

		if ($params)
		{
			// We will update com_redcore component parameters with the plugin parameters
			try
			{
				// We have changed default behavior of stateful webservices so we will change it together with the update
				if (is_string($params))
				{
					$params = json_decode($params, true);
				}

				// We set all old installations to default ON condition if that parameter was not set
				$params['webservice_stateful'] = isset($params['webservice_stateful']) ? $params['webservice_stateful'] : 1;
				$params = json_encode($params);

				$query = $db->getQuery(true)
					->update('#__extensions')
					->set($db->qn('params') . ' = ' . $db->q($params))
					->where($db->qn('type') . ' = ' . $db->q('component'))
					->where($db->qn('element') . ' = ' . $db->q('com_redcore'));

				$db->setQuery($query);
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				JLog::add($e->getMessage(), JLog::ERROR, 'jerror');
			}
		}

		return true;
	}
}
