<?php
/**
 * @package     RedRad
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_REDRAD') or die;

$data = $displayData;

if (!isset($displayData['menu']))
{
	return;
}

/** @var RMenu $menu */
$menu = $displayData['menu'];

// Get the active node in the trees.
$activeNode = null;

foreach ($menu->getTrees() as $tree)
{
	$node = $tree->getActiveNode();

	if ($node)
	{
		$activeNode = $node;
		break;
	}
}

$html = array();

if ($activeNode)
{
	$html[] = '<li class="active">' . $activeNode->getContent() . '</li>';

	// Traverse in reverse order until the root.
	while ($parent = $activeNode->getParent())
	{
		$activeNode = $parent;

		$html[] = '<li><a href="' . $parent->getTarget() . '">'
			. $parent->getContent() . '</a><span class="divider">/</span></li>';
	}
}

if (!empty($html))
{
	echo '<ul class="breadcrumb">';
	echo implode(array_reverse($html));
	echo '</ul>';
}
