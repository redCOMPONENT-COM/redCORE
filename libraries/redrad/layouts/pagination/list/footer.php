<?php
/**
 * @package     RedRad
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDRAD') or die;

$data = $displayData;
?>

<div class="pagination pagination-toolbar clearfix" style="text-align: center;">
	<div class="limit pull-right">
		<?php echo JText::_('JGLOBAL_DISPLAY_NUM') . $data['limitfield']; ?>
	</div>
	<?php echo $data['pageslinks']; ?>
	<input type="hidden" name="<?php echo $data['prefix']; ?>limitstart" value="<?php echo $data['limitstart']; ?>" />
</div>
