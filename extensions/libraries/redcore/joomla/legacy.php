<?php
/**
 * @package     Redcore
 * @subpackage  Legacy
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class have some functions needed for other Joomla Classes so we dont end up copying every Joomla class there is
 *
 * @since  11.1
 */
class RJoomlaLegacy
{
	/**
	 * Parse the show on conditions
	 *
	 * @param   string  $showOn       Show on conditions.
	 * @param   string  $formControl  Form name.
	 * @param   string  $group        The dot-separated form group path.
	 *
	 * @return  array   Array with show on conditions.
	 *
	 * @since   3.7.0
	 */
	public static function parseShowOnConditions($showOn, $formControl = null, $group = null)
	{
		// Process the showon data.
		if (!$showOn)
		{
			return array();
		}

		$formPath = $formControl ?: '';

		if ($group)
		{
			$formPath .= $formPath ? '[' . $group . ']' : $group;
		}

		$showOnData  = array();
		$showOnParts = preg_split('#(\[AND\]|\[OR\])#', $showOn, -1, PREG_SPLIT_DELIM_CAPTURE);
		$op          = '';

		foreach ($showOnParts as $showOnPart)
		{
			if (($showOnPart === '[AND]') || $showOnPart === '[OR]')
			{
				$op = trim($showOnPart, '[]');
				continue;
			}

			$compareEqual     = strpos($showOnPart, '!:') === false;
			$showOnPartBlocks = explode(($compareEqual ? ':' : '!:'), $showOnPart, 2);

			$showOnData[] = array(
				'field'  => $formPath ? $formPath . '[' . $showOnPartBlocks[0] . ']' : $showOnPartBlocks[0],
				'values' => explode(',', $showOnPartBlocks[1]),
				'sign'   => $compareEqual === true ? '=' : '!=',
				'op'     => $op,
			);

			if ($op !== '')
			{
				$op = '';
			}
		}

		return $showOnData;
	}
}
