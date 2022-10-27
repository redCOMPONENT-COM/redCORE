<?php
defined('_JEXEC') or die;

// Make layout variables available
extract($displayData);
?>

<div id="<?php echo $selector; ?>" tabindex="-1" class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close novalidate" data-dismiss="modal">×</button>
		<h3><?php echo $params['title']; ?></h3>
	</div>
	<div class="modal-body">
		<?php echo $body; ?>
	</div>
	<div class="modal-footer">
		<?php echo $params['footer']; ?>
	</div>
</div>
