<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$modal = $displayData;

?>
<div class="modal-header">
	<?php if ($modal->params->get('showHeaderClose', true)) : ?>
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	<?php endif; ?>
	<?php if ($modal->params->get('title', null)) : ?>
		<h4><?php echo $modal->params->get('title', null); ?></h4>
	<?php endif; ?>
</div>
