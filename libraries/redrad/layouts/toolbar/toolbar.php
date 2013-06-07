<?php
/**
 * @package     RedRad
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

$data = $displayData;

if (!isset($data['toolbar']))
{
	throw new InvalidArgumentException('The toolbar is not passed to the layout "toolbar".');
}

/** @var RToolbar $toolbar */
$toolbar = $data['toolbar'];

$groups = $toolbar->getGroups();
?>
<div class="btn-toolbar">
	<?php foreach ($groups as $group) : ?>
		<div class="btn-group">
			<?php
			foreach ($group->getButtons() as $button)
			{
				echo $button->render();
			}
			?>
		</div>
	<?php endforeach; ?>
</div>
