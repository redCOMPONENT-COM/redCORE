<?php
/**
 * @package     Redcore.Webservice
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

$options = !empty($displayData['options']['scopes']) ? $displayData['options']['scopes'] : array();
$value = !empty($displayData['options']['value']) ? $displayData['options']['value'] : array();
$name = !empty($displayData['options']['name']) ? $displayData['options']['name'] : '';
$id = !empty($displayData['options']['id']) ? $displayData['options']['id'] : '';
$class = !empty($displayData['options']['class']) ? $displayData['options']['class'] : '';
$hiddenLabel = isset($displayData['options']['hiddenLabel']) ? (bool) $displayData['options']['hiddenLabel'] : false;
$showCheckAll = isset($displayData['options']['showCheckAll']) ? (bool) $displayData['options']['showCheckAll'] : false;
$column = 0;

?>

<?php if ($showCheckAll): ?>
<script type="text/javascript">
	(function($) {
		$(document).ready(function() {
			// Check all scopes checkbox
			$('.scopes-check-all').click(function(event){
				var checked = this.checked;

				$("#" + $(this).attr("data-id") + " input[type='checkbox'][name='<?php echo $name ?>']").each(function(index){
					$(this).prop('checked', checked);
				});
			});

			// Change property checked of "Check All" checkbox if all values has been checked.
			$('.group-scopes').each(function(index){
				if ($(this).find("input[type='checkbox'][name='<?php echo $name ?>']:not(:checked)").length == 0) {
					$(this).find("input[type='checkbox'].scopes-check-all").prop('checked', true);
				}
			});
		});
	})(jQuery);
</script>
<?php endif; ?>

<div class="form-group <?php echo $class; ?>">
	<div class="row">
		<?php foreach ($options as $webServiceName => $scopes) :?>
			<?php $webServiceId = JFilterOutput::stringURLSafe($webServiceName); ?>
			<div class="col-md-4 well group-scopes" id="<?php echo $webServiceId ?>">
				<h4>
					<?php echo $webServiceName; ?>
				</h4>
				<?php if ($showCheckAll): ?>
					<div class="checkbox">
						<label>
							<input type="checkbox" data-id="<?php echo $webServiceId ?>" class="scopes-check-all" />
							<?php echo JText::_('JALL') ?>
						</label>
					</div>
				<?php endif; ?>
				<?php foreach ($scopes as $scope) :?>
					<?php $isChecked = in_array($scope['scope'], $value) ? ' checked="checked" ' : ''; ?>
					<div class="checkbox">
						<label>
							<input type="checkbox" name="<?php echo $name; ?>" <?php echo $isChecked; ?> value="<?php echo $scope['scope']; ?>" />
							<?php echo $scope['scopeDisplayName']; ?>
						</label>
					</div>
				<?php endforeach;?>
			</div>
			<?php if ((++$column) % 3 == 0 ) : ?>
				</div>
				<div class="row">
			<?php endif; ?>
		<?php endforeach;?>
	</div>
</div>
