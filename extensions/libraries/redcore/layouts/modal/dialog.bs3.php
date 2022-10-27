<?php
defined('_JEXEC') or die;

// Make layout variables available
extract($displayData);
?>

<div class="modal fade" id="<?php echo $selector; ?>" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close novalidate" data-dismiss="modal">×</button>
				<h3 class="modal-title"><?php echo $params['title']; ?></h3>
			</div>
			<div class="modal-body">
				<?php echo $body; ?>
			</div>
			<div class="modal-footer">
				<?php echo key_exists('footer', $params) ? $params['footer'] : ''; ?>
			</div>
		</div>
	</div>
</div>
