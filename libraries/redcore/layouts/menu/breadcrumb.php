<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

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

$links = array();

if ($activeNode)
{
	$links[] = $activeNode;

	while ($parent = $activeNode->getParent())
	{
		$activeNode = $parent;

		$links[] = $parent;
	}

	$links = array_reverse($links);
}

?>
<?php if ($links) : ?>
	<ul class="breadcrumb">
		<?php foreach ($links as $link) : ?>
			<?php if ($link->isActive()): ?>
				<li class="active">
					<?php echo $link->getContent(); ?>
				</li>
			<?php else : ?>
				<li>
					<a href="<?php echo $link->getTarget(); ?>">
						<?php echo $link->getContent(); ?>
					</a>
					<span class="divider">/</span>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
<?php endif;
