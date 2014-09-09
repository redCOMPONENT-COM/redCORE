<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 *
 */

defined('JPATH_REDCORE') or die;
$formName = !empty($displayData['formName']) ? $displayData['formName'] : 'adminForm';
$formId = !empty($displayData['formId']) ? $displayData['formId'] : 'adminForm';
$formAlign = !empty($displayData['formAlign']) ? $displayData['formAlign'] : 'form-horizontal';
$apiName = !empty($displayData['apiName']) ? $displayData['apiName'] : JFactory::getConfig()->get('sitename', 'API');

?>
<form method="post" name="<?php echo $formName; ?>" id="<?php echo $formId; ?>" class="<?php echo $formAlign; ?>">
	<label class="label"><?php echo JText::sprintf('LIB_REDCORE_API_OAUTH2_SERVER_AUTHORIZE_CLIENT_HEADER', $apiName); ?></label><br />
	<input class="btn" type="submit" name="authorized" value="yes" />
	<input class="btn" type="submit" name="authorized" value="no" />
</form>
