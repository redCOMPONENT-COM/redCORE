<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$modal = $displayData;
$id = !empty($modal['options']['id']) ? $modal['options']['id'] : RFilesystemFile::getUniqueName();
$header = !empty($modal['options']['header']) ? $modal['options']['header'] : '';
$footer = !empty($modal['options']['footer']) ? $modal['options']['footer'] : '';
$link = !empty($modal['options']['link']) ? $modal['options']['link'] : '';
$linkName = !empty($modal['options']['linkName']) ? $modal['options']['linkName'] : '&nbsp;';
$linkClass = !empty($modal['options']['linkClass']) ? $modal['options']['linkClass'] : 'btn btn-default';
?>
<a class="<?php echo $linkClass; ?> modal-iframe-external" data-toggle="modal" href="#"
   id="modalButton_<?php echo $id ?>"
   data-target="#modalContainer_modalButton_<?php echo $id ?>"
   title="<?php echo $header ?>" data-href="<?php echo $link ?>">
	<?php echo $linkName ?>
</a>
<div class="modal modal-iframe-external-container" id="modalContainer_modalButton_<?php echo $id ?>"
     tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-dialog-lg modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="<?php echo JText::_('JTOOLBAR_CLOSE') ?>">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title"><?php echo $header ?></h4>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal" name="modal-close"><?php echo JText::_('JTOOLBAR_CLOSE') ?></button>
				<button type="button" class="btn btn-primary" name="modal-save" data-dismiss="modal"><?php echo JText::_('JTOOLBAR_APPLY') ?></button>
				<?php echo $footer ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	jQuery('#modalButton_<?php echo $id ?>').on('click', function (e) {
		e.preventDefault();
		var url = jQuery(this).attr('data-href');

		jQuery('#modalContainer_modalButton_<?php echo $id ?> .modal-body')
			.html('<iframe width="100%" height="100%" frameborder="0" scrolling="yes" allowtransparency="true" src="' + url + '"></iframe>');
	});
</script>
