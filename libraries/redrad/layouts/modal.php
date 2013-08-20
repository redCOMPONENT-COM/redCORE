<?php
/**
 * @package     RedRad
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDRAD') or die;

$modal = $displayData;

$doc = JFactory::getDocument();

$cssId = $modal->getAttribute('id');

if ($link = $modal->params->get('link', null))
{
	$styleSheet = "
	iframe { border: 0 none; }
	.modal {
		position: absolute;
		left: 40%;
	}
	.modal-body {
		padding: 5px;
	}
	";

	$doc->addStyleDeclaration($styleSheet);

	$script = "
		(function($) {
			$(document).ready(function() {
				$('#" . $cssId . "').on('show', function () {
					console.log($('#" . $cssId . " .modal-body'));
					$('#" . $cssId . " .modal-body').html('<iframe class=\"iframe\" src=\""
					. $link . "\" height=\"" . $modal->params->get('height', '500px') . "\" width=\"" . $modal->params->get('width', '100%') . "\" scrolling=\"no\"></iframe>');
				});
			});
		})( jQuery );
	";

	$doc->addScriptDeclaration($script);
}

?>
<!-- Modal -->
<div <?php echo $modal->renderAttributes(); ?>>
	<?php if ($modal->params->get('showHeader', true)) : ?>
		<?php echo $this->sublayout('header', $modal); ?>
	<?php endif; ?>
	<?php echo $this->sublayout('body', $modal); ?>
	<?php if ($modal->params->get('showFooter', true)) : ?>
		<?php echo $this->sublayout('footer', $modal); ?>
	<?php endif; ?>
</div>