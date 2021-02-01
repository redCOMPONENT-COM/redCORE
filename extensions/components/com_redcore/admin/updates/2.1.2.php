<?php
/**
 * @package     Redcore
 * @subpackage  Upgrade
 *
 * @copyright   Copyright (C) 2012 - 2020 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Table\Extension;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

/**
 * Upgrade script for redCORE.
 *
 * @package     Redcore
 * @subpackage  Upgrade
 * @since       2.1.2
 */
class Com_RedcoreUpdateScript_2_1_2
{
	/**
	 * Performs the upgrade after initial Joomla update for this version
	 *
	 * @param   JInstallerAdapter  $parent   Class calling this method
	 *
	 * @throws RuntimeException
	 * @return  boolean
	 */
	public function executeAfterUpdate($parent)
	{
		try
		{
			$table = new Extension(Factory::getDbo());

			if (!$table->load([
					'type' => 'component',
					'element' => 'com_redcore',
				]
			))
			{
				throw new RuntimeException('Component com_redcore not found');
			}

			$params = new Registry($table->get('params'));
			$params->set('webservice_put_sends_changes_only', 0);

			if (!$table->save([
					'params' => $params->toString()
				]
			))
			{
				throw new RuntimeException('Error when saving: ' . $table->getError());
			}
		}
		catch (Throwable $e)
		{
			Log::add($e->getMessage(), Log::ERROR, 'jerror');
		}

		return true;
	}
}
