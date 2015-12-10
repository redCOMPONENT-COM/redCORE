<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Templates
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

?>
<div class="row fields-add-new-complex-type-row">
	<div class="col-lg-4">
		<div class="input-group">
			<input type="text" class="form-control" name="newType" onblur="this.value = this.value.replace(/[^\w]/g,'')"
			       placeholder="<?php echo JText::_('COM_REDCORE_WEBSERVICE_COMPLEX_TYPES_ADD_NEW_COMPLEX_TYPE_PLACEHOLDER'); ?>" />
			<span class="input-group-btn">
				<button type="button" class="btn btn-default btn-success fields-add-new-task">
					<i class="icon-plus"></i>
					<?php echo JText::_('COM_REDCORE_WEBSERVICE_COMPLEX_TYPES_ADD_NEW_COMPLEX_TYPE'); ?>
				</button>
			</span>
		</div>
	</div>
</div>
