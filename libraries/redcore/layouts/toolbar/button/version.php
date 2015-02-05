<?php
/**
 * @package     Redcore
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$data = $displayData;

if (!isset($data['button']))
{
	throw new InvalidArgumentException('The button is not passed to the layout "button.version".');
}

/** @var RToolbarButtonStandard $button */
$button   = $data['button'];
$isOption = $data['isOption'];

$text      = $button->getText();
$iconClass = $button->getIconClass();
$class     = $button->getClass();
$height    = $button->getModalHeight();
$width     = $button->getModalWidth();
$itemId    = $button->getItemId();
$typeId    = $button->getTypeId();
$typeAlias = $button->getTypeAlias();

// Load modal framework.
JHtml::_('behavior.modal', 'a.modal_jform_contenthistory');

$link = 'index.php?option=com_contenthistory&amp;view=history&amp;layout=modal&amp;tmpl=component&amp;item_id=' . $itemId .
	'&amp;type_id=' . $typeId . '&amp;type_alias=' . $typeAlias . '&amp;' . JSession::getFormToken() . '=1';

$btnClass = 'btn btn-default modal_jform_contenthistory ' . $class;
?>

<?php if ($isOption): ?>
	<li>
		<a rel="{handler: 'iframe', size: {x: <?php echo $height ?>, y: <?php echo $width ?>}}"
			href="<?php echo $link ?>"
			title="<?php echo $text ?>"
			class="<?php echo $btnClass ?>">
			<?php if (!empty($iconClass)): ?>
				<i class="<?php echo $iconClass ?>"></i>
			<?php endif; ?>
			<?php echo $text; ?>
		</a>
	</li>
<?php else: ?>
	<a rel="{handler: 'iframe', size: {x: <?php echo $height ?>, y: <?php echo $width ?>}}"
		href="<?php echo $link ?>"
		title="<?php echo $text ?>"
		class="<?php echo $btnClass ?>">
		<?php if (!empty($iconClass)): ?>
			<i class="<?php echo $iconClass ?>"></i>
		<?php endif; ?>
		<?php echo $text; ?>
	</a>
<?php endif; ?>
