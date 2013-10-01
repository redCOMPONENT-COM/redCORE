<?php
/**
 * @package     Redcore
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

JHtml::_('rbootstrap.tooltip');

$data = $displayData;

$options = isset($data->stoolsOptions) ? $data->stoolsOptions : array();

// Generate options object + common required settings
$data->options = new JRegistry($options);
$data->options->set('searchField', 'filter_' . $data->options->get('searchField', 'search'));
$data->options->set('filtersApplied', !empty($data->activeFilters));
$data->options->set('defaultLimit', JFactory::getApplication()->getCfg('list_limit', 20));

?>
<div class="stools js-stools clearfix">
	<div id="filter-bar" class="hidden-phone row-fluid clearfix">
		<div class="span6">
			<?php echo $this->sublayout('bar', $data); ?>
		</div>
		<div class="span6 hidden-phone stools-list js-stools-container-order">
			<?php echo $this->sublayout('list', $data); ?>
		</div>
	</div>
	<!-- Filters div -->
	<div class="js-stools-container">
		<div class="js-stools-container-filter stools-filters hidden-phone">
			<?php  echo $this->sublayout('filters', $data); ?>
		</div>
	</div>
</div>