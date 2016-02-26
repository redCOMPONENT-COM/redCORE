<?php
/**
 * @package     Redcore
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$data = $displayData;

if (!isset($data['toolbar']))
{
	throw new InvalidArgumentException('The toolbar is not passed to the layout "toolbar".');
}

/** @var RToolbar $toolbar */
$toolbar = $data['toolbar'];

// Get the toolbar class.
$toolBarClass = $toolbar->getClass();

if (empty($toolBarClass))
{
	$toolBarClass = 'btn-toolbar';
}

else
{
	$toolBarClass = 'btn-toolbar ' . $toolBarClass;
}

$groups = $toolbar->getGroups();
?>
<?php if (!$toolbar->isEmpty()) : ?>
	<div class="<?php echo $toolBarClass ?>">
		<?php
		foreach ($groups as $group) :

			$groupClass = $group->getClass();
			$iconClass  = $group->getIconClass();

			if (empty($groupClass))
			{
				$groupClass = 'btn-group';
			}

			else
			{
				$groupClass = 'btn-group ' . $groupClass;
			}
		?>
		<?php if ($group->isMenu()):?>
			<div class="<?php echo $groupClass;?>">
				<a class="btn dropdown-toggle btn-primary" data-toggle="dropdown" href="#">
					<?php if (!empty($iconClass)):?>
						<i class="<?php echo $iconClass;?>"></i>
					<?php endif;?>
					<?php echo $group->getTitle();?>
				</a>
				<ul class="dropdown-menu">
					<?php
					foreach ($group->getButtons() as $button)
					{
						echo $button->render(true);
					}
					?>
				</ul>
			</div>
		<?php else: ?>
			<div class="<?php echo $groupClass ?>">
				<?php
				foreach ($group->getButtons() as $button)
				{
					echo $button->render();
				}
				?>
			</div>
		<?php endif;?>
		<?php endforeach; ?>
	</div>
<?php endif;
