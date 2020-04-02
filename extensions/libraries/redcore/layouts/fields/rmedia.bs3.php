<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

use Joomla\CMS\Factory;

defined('JPATH_REDCORE') or die;

$data = $displayData;
$doc = JFactory::getDocument();
$script   = array();

$script[] = '	(function($) {';
$script[] = '		$(document).ready(function() {';
$script[] = '			jModalClose = function() {';
$script[] = '				$(\'.modal.in\').modal(\'hide\');';
$script[] = '			}';
$script[] = '		});';
$script[] = '	})( jQuery );';
$doc->addScriptDeclaration(implode("\n", $script));

$modal = RLayoutHelper::render('modal', $data['modal']);
$app   = Factory::getApplication();

// Move modal at the end of the document
$app->registerEvent('onAfterRender', function () use ($modal, $app) {
	$app->setBody($app->getBody() . $modal);
});
?>
<a
	class="btn btn-default modalAjax"
	data-toggle="modal"
	title="<?php echo JText::_('JLIB_FORM_BUTTON_SELECT'); ?>"
	href="#<?php echo $data['modal']->getAttribute('id'); ?>"
	>
	<?php echo JText::_('JLIB_FORM_BUTTON_SELECT'); ?>
</a>
<a
	class="btn btn-default hasTooltip"
	title="<?php echo JText::_('JLIB_FORM_BUTTON_CLEAR'); ?>"
	href="#"
	onclick="jQuery('#<?php echo $data['field']->id; ?>').val(''); return false;"
	>
	<i class="icon-remove"></i>
</a>
