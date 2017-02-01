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

$id = !empty($displayData['id']) ? $displayData['id'] : 0;
$header = !empty($displayData['header']) ? $displayData['header'] : '';
$footer = !empty($displayData['footer']) ? $displayData['footer'] : '';
$link = !empty($displayData['link']) ? $displayData['link'] : '';
$linkName = !empty($displayData['linkName']) ? $displayData['linkName'] : '&nbsp;';
$linkClass = !empty($displayData['linkClass']) ? $displayData['linkClass'] : 'btn btn-default';
$htmlposition = !empty($displayData['htmlposition']) ? $displayData['htmlposition'] : '.btn-toolbar:first';
$contentElement = !empty($displayData['contentElement']) ? $displayData['contentElement'] : RFilesystemFile::getUniqueName();
?>
<?php if (!empty($displayData['id'])) : ?>
	<button type="button"
	    style="display:none;"
		class="btn modalContainer_modalButton_<?php echo $contentElement; ?>"
		data-toggle="modal"
		data-target="#modalContainer_modalButton_<?php echo $contentElement; ?>"
		title="<?php echo $header ?>"
		onclick="openFullPageModal_<?php echo $contentElement; ?>(this);">
			<i class="icon-globe"></i>
			<?php echo $linkName ?>
	</button>
	<div id="modalContainer<?php echo $contentElement; ?>" class="modalContainer" style="display:none;">
		<div class="modal full-page" style="display:none" id="modalContainer_modalButton_<?php echo $contentElement; ?>" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body"></div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal" name="modal-close">
							<?php echo JText::_('LIB_REDCORE_TRANSLATION_NAME_CANCEL') ?>
						</button>
						<button type="button" class="btn btn-primary" name="modal-save" data-dismiss="modal" onclick="saveIframe_<?php echo $contentElement; ?>();">
							<?php echo JText::_('LIB_REDCORE_TRANSLATION_NAME_SAVE') ?>
						</button>
						<?php echo $footer ?>
					</div>
				</div>
			</div>
		</div>
	</div>
		<script type="text/javascript">
			function openFullPageModal_<?php echo $contentElement; ?>(el)
			{
				var url = '<?php echo $link; ?>';

				jQuery('.translation-message').remove();
				jQuery('#modalContainer<?php echo $contentElement; ?> .modal-body')
					.html('<iframe id="modal_iframe_<?php echo $contentElement; ?>" width="100%" height="100%" frameborder="0" scrolling="yes" allowtransparency="true" src="'
						+ url
						+ '"></iframe>');
				jQuery('#modalContainer<?php echo $contentElement; ?>').show();
			}

			function saveIframe_<?php echo $contentElement; ?>()
			{
				jQuery('#modal_iframe_<?php echo $contentElement; ?>')[0].contentWindow.Joomla.submitbutton('translation.apply');
				var savedIframe = '<div class="alert alert-success translation-message"><?php echo JTEXT::_('LIB_REDCORE_TRANSLATION_SAVE_SUCESS'); ?></div>';
				jQuery('#system-message-container').append(savedIframe);
			}

			jQuery(document).ready(function() {
				if (!jQuery('<?php echo $htmlposition; ?> .modalContainer_modalButton_<?php echo $contentElement; ?>').length)
				{
					jQuery('<?php echo $htmlposition; ?>').append(jQuery('.modalContainer_modalButton_<?php echo $contentElement; ?>'));
				}
				jQuery('.modalContainer_modalButton_<?php echo $contentElement; ?>:first').show();
			});
		</script>

<?php else: ?>
	<button type="button" class="btn btn-primary disabled">
		<?php echo JText::_('LIB_REDCORE_TRANSLATION_SAVE_ITEM_TO_TRANSLATE'); ?>
	</button>
<?php endif;
