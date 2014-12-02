<?php
/**
 * @package     Redcore.Webservice
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
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
$column = 0;
?>

<div class="form-group <?php echo $class; ?>">
	<div class="row">
		<?php foreach ($options as $webServiceName => $scopes) :?>
			<div class="col-md-4 well">
				<h4>
					<?php echo $webServiceName; ?>
				</h4>
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
