<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

RHelperAsset::load('full-page-modal.min.css', 'redcore');

$modal = $displayData;
$id = !empty($modal['options']['id']) ? $modal['options']['id'] : RFilesystemFile::getUniqueName();
$header = !empty($modal['options']['header']) ? $modal['options']['header'] : '';
$footer = !empty($modal['options']['footer']) ? $modal['options']['footer'] : '';
$link = !empty($modal['options']['link']) ? $modal['options']['link'] : '';
$linkName = !empty($modal['options']['linkName']) ? $modal['options']['linkName'] : '&nbsp;';
$linkClass = !empty($modal['options']['linkClass']) ? $modal['options']['linkClass'] : 'btn btn-default';
?>

<div class="modalContainer" style="display:none;">
	<?php if (!empty($modal['options']['id']))  : ?>
	<a class="<?php echo $linkClass; ?> modal-iframe-external" data-toggle="modal" href="#"
	   id="modalButton_<?php echo $id ?>"
	   data-target="#modalContainer_modalButton_<?php echo $id ?>"
	   title="<?php echo $header ?>" data-href="<?php echo $link ?>"
	   onclick="openFullPageModal(this);">
		<?php echo $linkName ?>
	</a>
	<?php else: ?>
		<button type="button" class="btn btn-primary disabled">
			<?php echo JText::_('LIB_REDCORE_TRANSLATION_SAVE_ITEM_TO_TRANSLATE'); ?>
		</button>
	<?php endif; ?>
	<div class="modal modal-iframe-external-container full-page hide" id="modalContainer_modalButton_<?php echo $id ?>"
	     tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-dialog-lg modal-lg">
			<div class="modal-content">
				<div class="modal-body"></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal" name="modal-close">
						<?php echo JText::_('LIB_REDCORE_TRANSLATION_NAME_CANCEL') ?>
					</button>
					<button type="button" class="btn btn-primary" name="modal-save" data-dismiss="modal" onclick="saveIframe();">
						<?php echo JText::_('LIB_REDCORE_TRANSLATION_NAME_SAVE') ?>
					</button>
					<?php echo $footer ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function openFullPageModal(el) 
	{
		var url = jQuery(el).attr('data-href');

		jQuery('.translation-message').remove();

		jQuery('#modalContainer_modalButton_<?php echo $id; ?> .modal-body')
		.html('<iframe id="modal_iframe_<?php echo $id; ?>" width="100%" height="100%" frameborder="0" scrolling="yes" allowtransparency="true" src="' 
		+ url 
		+ '"></iframe>');
	}

	function saveIframe() 
	{
		jQuery('#modal_iframe_<?php echo $id; ?>')[0].contentWindow.Joomla.submitbutton('translation.apply');
		var savedIframe = '<div class="alert alert-success translation-message"><?php echo JTEXT::_('LIB_REDCORE_TRANSLATION_SAVE_SUCESS'); ?></div>';
		jQuery('#system-message-container').append(savedIframe);
	}

	jQuery(document).ready(function() {
		jQuery('.btn-toolbar').first().append(jQuery('.modalContainer'));
		jQuery('.modalContainer').show();
	});
</script>
