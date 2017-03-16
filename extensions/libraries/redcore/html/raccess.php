<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  HTML
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_REDCORE') or die;
JLoader::import('cms.html.access');

/**
 * Extended Utility class for all HTML drawing classes.
 *
 * @since  1.8.9
 */
abstract class JHtmlRAccess extends JHtmlAccess
{
	/**
	 * Returns a UL list of user groups with buttons (Add/Remove).
	 * This is a workaround for selecting usergroups. It is used in cases where
	 * we have large number of usergroups which results in problems with
	 * sending too much inputs (per each usergroup). This fix sends only
	 * selected usergroups instead. Beside using this function, it is required to
	 * override admin tpl file under
	 * "administrator/templates/TEMPLATE/html/com_users/user/edit_groups.php"
	 * and change `JHtml::_('access.usergroups', 'jform[groups]', $this->groups, true);`
	 * to `JHtml::_('raccess.usergroups', 'jform[groups]', $this->groups, true);`
	 *
	 * @param   string   $name             The name of the checkbox controls array
	 * @param   array    $selected         An array of the checked boxes
	 * @param   boolean  $checkSuperAdmin  If false only super admins can add to super admin groups
	 *
	 * @return  string
	 *
	 * @since   1.8.9
	 */
	public static function usergroups($name, $selected, $checkSuperAdmin = false)
	{
		static $count;

		$count++;
		$isSuperAdmin = JFactory::getUser()->authorise('core.admin');
		$groups       = array_values(JHelperUsergroups::getInstance()->getAll());
		$html         = array();
		$script       = array();
		$doc          = JFactory::getDocument();

		$script[] = 'function JAddUsergroup(id, value){';
		$script[] = '  html = "<input type=\'hidden\' name=\'' . $name . '[]\' value=\'" + value + "\' id=\'" + id + "\' />";';
		$script[] = '  html += "<button type=\'button\' class=\'btn btn-small btn-danger\' onclick=\'JRemoveUsergroup(\"" + id + "\", \"" + value + "\")\'>'
			. JText::_('JREMOVE') . '</button>";';
		$script[] = '  jQuery("#" + id + "-hidden").html(html);';
		$script[] = '}';
		$script[] = 'function JRemoveUsergroup(id, value){';
		$script[] = '  html = "<button type=\'button\' class=\'btn btn-small btn-success\' onclick=\'JAddUsergroup(\"" + id + "\", \"" + value + "\")\'>'
			. JText::_('JADD') . '</button>";';
		$script[] = '  jQuery("#" + id + "-hidden").html(html);';
		$script[] = '}';

		$doc->addScriptDeclaration(implode("\n", $script));

		for ($i = 0, $n = count($groups); $i < $n; $i++)
		{
			$item = &$groups[$i];

			// If checkSuperAdmin is true, only add item if the user is superadmin or the group is not super admin
			if ((!$checkSuperAdmin) || $isSuperAdmin || (!JAccess::checkGroup($item->id, 'core.admin')))
			{
				// Setup  the variable attributes.
				$eid = $count . 'group_' . $item->id;

				// Build the HTML for the item.
				$html[] = '<div class="control-group"><div class="controls"><span id="' . $eid . '-hidden">';

				if (!empty($selected) && in_array($item->id, $selected))
				{
					$html[] = '<input type="hidden" name="' . $name . '[]" value="' . $item->id . '" id="' . $eid . '" />';
					$html[] = '<button type="button" class="btn btn-small btn-danger" onclick="JRemoveUsergroup(\''
						. $eid . '\', \'' . $item->id . '\')">' . JText::_('JREMOVE') . '</button>';
				}
				else
				{
					$html[] = '<button type="button" class="btn btn-small btn-success" onclick="JAddUsergroup(\''
						. $eid . '\', \'' . $item->id . '\')">' . JText::_('JADD') . '</button>';
				}

				$html[] = '</span>';
				$html[] = JLayoutHelper::render('joomla.html.treeprefix', array('level' => $item->level + 1)) . $item->title;
				$html[] = '</div></div>';
			}
		}

		return implode("\n", $html);
	}
}
