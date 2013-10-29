<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$list = $displayData['list'];
$options = new JRegistry($displayData['options']);

$showLimitBox   = $options->get('showLimitBox', true);
$showPagesLinks = $options->get('showPagesLinks', true);
$showLimitStart = $options->get('showLimitStart', true);
?>

<div class="pagination pagination-toolbar clearfix" style="text-align: center;">
	<?php if ($showLimitBox) : ?>
		<div class="limit pull-right">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM') . $list['limitfield']; ?>
		</div>
	<?php endif; ?>
	<?php if ($showPagesLinks) : ?>
		<?php echo $list['pageslinks']; ?>
	<?php endif; ?>
	<?php if ($showLimitStart) : ?>
		<input type="hidden" name="<?php echo $list['prefix']; ?>limitstart" value="<?php echo $list['limitstart']; ?>" />
	<?php endif; ?>
</div>
