<?php
/**
 * @package     Redcore
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

$data = $displayData;

// Is multilanguage enabled?
$langs = isset(JFactory::getApplication()->languages_enabled);

$pagination = $data->get('pagination');

$list = $data->filterForm->getGroup('list');
?>
<?php if ($list) : ?>
	<div class="ordering-select hidden-phone">
		<?php foreach ($list as $fieldName => $field) : ?>
				<?php echo $field->input; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
