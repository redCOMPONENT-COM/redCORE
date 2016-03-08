<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$selector = empty($displayData['selector']) ? '' : $displayData['selector'];

?>

<ul class="nav nav-tabs" id="<?php echo $selector; ?>Tabs"></ul>
<div class="tab-content" id="<?php echo $selector; ?>Content">
