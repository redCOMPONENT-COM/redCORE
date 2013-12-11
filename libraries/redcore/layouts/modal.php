<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$modal = $displayData;

$doc = JFactory::getDocument();

$cssId = $modal->getAttribute('id');

if ($link = $modal->params->get('link', null))
{
	// @ToDo Remove re adding css style if more modal buttons are used
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

	$jsEvents       = $modal->params->get('events', array());
	$jsEventsString = '';

	foreach ($jsEvents as $event => $function)
	{
		$jsEventsString .= $event . '="' . $function . '(this)" ';
	}

	$script   = array();

	$script[] = '	(function($) {';
	$script[] = '		$(document).ready(function() {';
	$script[] = '		$(\'#' . $cssId . '\').on(\'show\', function () {';
	$script[] = '			$(\'#' . $cssId . ' .modal-body\').html(\'<iframe class="iframe" src="' . $link . '" width="' .
		$modal->params->get('width', '100%') . '" scrolling="no" ' . $jsEventsString . '"></iframe>\');';
	$script[] = '			});';
	$script[] = '		});';
	$script[] = '	})( jQuery );';

	$doc->addScriptDeclaration(implode("\n", $script));
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