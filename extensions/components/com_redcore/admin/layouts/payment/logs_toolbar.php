<?php
/**
 * @package     Redcore.Frontend
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$data = $displayData;

$formName = $data['formName'];
$return = isset($data['return']) ? $data['return'] : null;
?>
<h2>
	<?php echo JText::_('COM_REDCORE_PAYMENT_LOG'); ?>
</h2>
<div class="container-fluid">
	<div class="btn-toolbar toolbar">
		<div class="btn-group">
			<button class="btn"
			        onclick="if (document.<?php echo $formName; ?>.boxchecked.value==0){alert('<?php echo JText::_('COM_REDCORE_PLEASE_SELECT_ITEM', true); ?>');}
				        else{ Joomla.submitform('payment_log.edit', document.getElementById('<?php echo $formName; ?>'))}"
			        href="#">
				<i class="icon-edit"></i>
				<?php echo JText::_('JTOOLBAR_EDIT') ?>
			</button>
			<button class="btn btn-danger"
			        onclick="if (document.<?php echo $formName; ?>.boxchecked.value==0){alert('<?php echo JText::_('COM_REDCORE_PLEASE_SELECT_ITEM', true); ?>');}
				        else{ Joomla.submitform('payment_logs.delete', document.getElementById('<?php echo $formName; ?>'))}"
			        href="#">
				<i class="icon-trash"></i>
				<?php echo JText::_('JTOOLBAR_DELETE') ?>
			</button>
		</div>
	</div>
</div>
