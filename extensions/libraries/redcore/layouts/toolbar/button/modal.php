<?php
/**
 * @package     Redcore
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$data = $displayData;

if (!isset($data['button']))
{
	throw new InvalidArgumentException(JText::sprintf('LIB_REDCORE_LAYOUTS_TOOLBAR_BUTTON_ERROR_MISSING_BUTTON', 'button.modal'));
}

/** @var RToolbarButtonModal $button */
$button = $data['button'];
$isOption = $data['isOption'];

$class = $button->getClass();
$iconClass = $button->getIconClass();
$text = $button->getText();
$isList = $button->isList();
$params = $button->getParams();

$dataTarget = $button->getDataTarget();

// Fix old name targets with #
$dataTarget = str_replace('#', '', $dataTarget);

// Get the button class.
$btnClass = $isOption ? '' : 'btn btn-default';
$isFrameModal = false;
$attributes = '';
$cmd = '';

if (!empty($class))
{
	$btnClass .= ' ' . $class;
}

if (isset($params['url']) && $params['url'])
{
	$isFrameModal = true;
	$btnClass .= ' modal';
	$attributes = ' data-toggle="modal" data-target="#' . $dataTarget . '"';
}
else
{
	$cmd = "jQuery('#" . $dataTarget . "').modal('toggle');";
}

if ($isList)
{
	// Get the button command.
	JHtml::_('behavior.framework');
	$message = JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
	$message = addslashes($message);
	$cmd = "if (document.adminForm.boxchecked.value == 0) {alert('" . $message . "');jQuery('#" . $dataTarget . "').modal('hide');}
	else {jQuery('#" . $dataTarget . "').modal('toggle');}";
}
?>

<?php if ($isOption) :?>
	<li>
		<a href="#" class="<?php echo $btnClass ?>" <?php echo $attributes; ?> onclick="<?php echo $cmd ?>">
			<?php if (!empty($iconClass)) : ?>
				<i class="<?php echo $iconClass ?>"></i>
			<?php endif; ?>
			<?php echo $text ?>
		</a>
	</li>
<?php else:?>
	<button class="<?php echo $btnClass ?>" <?php echo $attributes; ?> onclick="<?php echo $cmd ?>">
		<?php if (!empty($iconClass)) : ?>
			<i class="<?php echo $iconClass ?>"></i>
		<?php endif; ?>
		<?php echo $text ?>
	</button>
<?php endif;

if ($isFrameModal)
{
	$params['title']  = $text;

	if (!isset($params['width']))
	{
		$params['width'] = 640;
	}

	if (!isset($params['height']))
	{
		$params['height'] = 480;
	}

	echo JHtml::_('rbootstrap.renderModal', $dataTarget, $params);

	$document = JFactory::getDocument();
	$document->addScriptDeclaration(
		'jQuery(document).ready(function(){jQuery("#' . $dataTarget . '").appendTo(jQuery(document.body));});'
	);
	$document->addStyleDeclaration('
		#' . $dataTarget . '{
			width: ' . ($params['width'] + 25) . 'px;
			height: ' . ($params['height'] + 100) . 'px;
			margin-left: -' . (($params['width'] + 20) / 2) . 'px;
		}
		#' . $dataTarget . ' .modal-body {
			max-height: none !important;
		}
		#' . $dataTarget . ' .modal-body iframe{
			width: 100%;
			max-height: none;
			border: 0 !important;
			max-width: 100%;
		}
  ');
}
