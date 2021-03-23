<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2021 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;
?>
<div class="tab-pane" id="mainComponentInfo">
	<?php echo RComponentHelper::displayComponentInfo($this->component->option); ?>
</div>
