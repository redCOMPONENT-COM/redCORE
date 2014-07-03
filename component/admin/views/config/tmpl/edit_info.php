<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;
?>
<div class="tab-pane" id="mainComponentInfo">
	<?php echo RComponentHelper::displayComponentInfo($this->component->option); ?>
</div>
