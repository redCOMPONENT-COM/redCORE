<?php
/**
 * @package     Redcore
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$height = $displayData['height'];
$width = $displayData['width'];
$itemId = (int) $displayData['itemId'];
$typeId = (int) $displayData['typeId'];
$typeAlias = $displayData['typeAlias'];
$title = $displayData['title'];

$link = 'index.php?option=com_contenthistory&amp;view=history&amp;layout=modal&amp;tmpl=component&amp;item_id=' . $itemId .
	'&amp;type_id=' . $typeId . '&amp;type_alias=' . $typeAlias . '&amp;' . JSession::getFormToken() . '=1';
?>

<a rel="{handler: 'iframe', size: {x: <?php echo $height ?>, y: <?php echo $width ?>}}"
	href="<?php echo $link ?>"
	title="<?php echo $title ?>"
	class="btn btn-default modal_jform_contenthistory">
	<i class="icon-archive"></i> <?php echo $displayData['title']; ?>
</a>
