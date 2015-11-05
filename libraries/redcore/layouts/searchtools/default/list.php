<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$data = $displayData;

// Load the form list fields
$list = $data['view']->filterForm->getGroup('list');
?>
<?php if ($list) : ?>
	<div class="ordering-select hidden-phone">
		<?php foreach ($list as $fieldName => $field) : ?>
			<?php if ($field->hidden) : ?>
				<?php echo $field->input; ?>
			<?php else : ?>
				<div class="js-stools-field-list">
					<?php echo $field->input; ?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif;
