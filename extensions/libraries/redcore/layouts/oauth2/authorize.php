<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 *
 */

defined('JPATH_REDCORE') or die;
$formName = !empty($displayData['options']['formName']) ? $displayData['options']['formName'] : 'adminForm';
$formId = !empty($displayData['options']['formId']) ? $displayData['options']['formId'] : 'adminForm';
$formAlign = !empty($displayData['options']['formAlign']) ? $displayData['options']['formAlign'] : 'form-horizontal';
$formAction = !empty($displayData['options']['formAction']) ? $displayData['options']['formAction'] : '';
$clientId = !empty($displayData['options']['clientId']) ? $displayData['options']['clientId'] : JFactory::getConfig()->get('sitename', 'API');
$scopes = !empty($displayData['options']['scopes']) ? $displayData['options']['scopes'] : array();
$column = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1\" />
	<link type="text/css" href="<?php echo JUri::root(true) . '/media/redcore/css/component.bs3.min.css' ?>" rel="stylesheet" />
	<link type="text/javascript" href="<?php echo JUri::root(true) . '/media/redcore/js/lib/bootstrap3/js/bootstrap.min.js' ?>" />
</head>
<body class="redcore">
<form action="<?php echo $formAction; ?>" method="post" name="<?php echo $formName; ?>" id="<?php echo $formId; ?>" class="<?php echo $formAlign; ?>">
	<h1><?php echo JText::sprintf('LIB_REDCORE_API_OAUTH2_SERVER_AUTHORIZE_CLIENT_HEADER', JFactory::getConfig()->get('sitename', 'API')); ?></h1>

	<p><?php echo JText::sprintf('LIB_REDCORE_API_OAUTH2_SERVER_AUTHORIZE_CLIENT_BODY', $clientId, $clientId); ?></p>

	<?php if (!empty($scopes)): ?>
		<div class="container-fluid">
		<?php foreach ($scopes as $webServiceName => $scopeList) :?>
			<div class="col-xs-6 col-md-4">
				<div class="well">
					<h4>
						<?php echo $webServiceName; ?>
					</h4>
					<ul>
						<?php foreach ($scopeList as $scope) :?>
							<li><?php echo $scope['scopeDisplayName']; ?></li>
						<?php endforeach;?>
					</ul>
				</div>
			</div>
			<?php if ((++$column) % 3 == 0 ) : ?>
				</div>
				<div class="container-fluid">
			<?php endif; ?>
		<?php endforeach;?>
		</div>
	<?php endif; ?>

	<p><?php echo JText::sprintf('LIB_REDCORE_API_OAUTH2_SERVER_AUTHORIZE_CLIENT_FOOTER', $clientId); ?></p>
<div style="text-align: center">
	<input
		type="submit"
		name="authorized"
		class="btn btn-lg btn-success"
		value="<?php echo JText::_('LIB_REDCORE_API_OAUTH2_SERVER_AUTHORIZE_CLIENT_YES'); ?>"
		/>
	<input type="submit" name="authorized" class="btn btn-lg btn-danger" value="<?php echo JText::_('JCANCEL'); ?>" />
</div>

</form>
</body>
</html>
