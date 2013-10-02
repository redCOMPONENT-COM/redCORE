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

$filters = $data->filterForm->getGroup('filter');

$searchField  = $data->options->get('searchField', 'filter_search');
?>
<?php if ($filters) : ?>
	<div class="filter-select hidden-phone">
		<?php foreach ($filters as $fieldName => $field) : ?>
			<?php if ($fieldName != $searchField) : ?>
				<?php echo $field->input; ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
