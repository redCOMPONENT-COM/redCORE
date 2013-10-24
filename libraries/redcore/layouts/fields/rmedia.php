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
?>

<?php echo RLayoutHelper::render('modal', $data['modal']); ?>
<a
	class="btn modalAjax"
	data-toggle="modal"
	title="<?php echo JText::_('JLIB_FORM_BUTTON_SELECT'); ?>"
	href="#<?php echo $data['modal']->getAttribute('id'); ?>"
	>
	<?php echo JText::_('JLIB_FORM_BUTTON_SELECT'); ?>
</a>
<a
	class="btn hasTooltip"
	title="<?php echo JText::_('JLIB_FORM_BUTTON_CLEAR'); ?>"
	href="#"
	onclick="jQuery('#<?php echo $data['field']->id; ?>').val(''); return false;"
	>
	<i class="icon-remove"></i>
</a>