<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$data = $displayData;
?>
<a href="#"
   onclick="Joomla.tableOrdering('<?php echo $data->order; ?>','<?php echo $data->direction; ?>',
	   '<?php echo $data->task; ?>', document.getElementById('<?php echo $data->form; ?>'));return false;"
   class="hasTooltip"
   title="<?php echo $data->metatitle; ?>">
	<?php if (!empty($data->icon)) : ?>
		<i class="<?php echo $data->icon; ?>"></i>
	<?php endif; ?>
	<?php if (!empty($data->title)) : ?>
		<?php echo JText::_($data->title); ?>
	<?php endif; ?>

	<?php if ($data->order == $data->selected) : ?>
		<i class="<?php echo $data->orderIcon; ?>"></i>
	<?php endif; ?>
</a>
