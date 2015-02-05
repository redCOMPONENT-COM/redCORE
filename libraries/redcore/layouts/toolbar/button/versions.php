<?php
/**
 * @package     Redcore
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

JHtml::_('behavior.modal', 'a.modal_jform_contenthistory');

$height    = $displayData['height'];
$width     = $displayData['width'];
$itemId    = (int) $displayData['itemId'];
$typeId    = (int) $displayData['typeId'];
$typeAlias = $displayData['typeAlias'];
$title     = $displayData['title'];
$class     = $displayData['class'];
$iconClass = $displayData['iconClass'];

$link = 'index.php?option=com_contenthistory&amp;view=history&amp;layout=modal&amp;tmpl=component&amp;item_id=' . $itemId .
	'&amp;type_id=' . $typeId . '&amp;type_alias=' . $typeAlias . '&amp;' . JSession::getFormToken() . '=1';

$btnClass = 'btn btn-default modal_jform_contenthistory ' . $class;
?>

<a rel="{handler: 'iframe', size: {x: <?php echo $height ?>, y: <?php echo $width ?>}}"
	href="<?php echo $link ?>"
	title="<?php echo $title ?>"
	class="<?php echo $btnClass ?>">
	<i class="<?php echo $iconClass ?>"></i> <?php echo $title; ?>
</a>
