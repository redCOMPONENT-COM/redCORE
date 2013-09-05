<?php
/**
 * @package     RedRad
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDRAD') or die;

$data = $displayData;

if (!isset($data['view']))
{
	throw new InvalidArgumentException('No View passed to the "search" layout.');
}

/** @var RView $view */
$view = $data['view'];

// If a state is given use it
if (isset($data['state']))
{
	$state = $data['state'];
}

// Use the view state
else
{
	$state = $view->getModel()->getState();
}
?>

<div id="filter-bar" class="btn-toolbar">
	<div class="filter-search btn-group pull-left">
		<input type="text" name="filter_search" id="filter_search"
		       placeholder="<?php echo JText::_('JSEARCH'); ?>"
		       value="<?php echo $state->get('filter.search'); ?>"
		       title="<?php echo JText::_('JSEARCH'); ?>"/>
	</div>
	<div class="btn-group hidden-phone">
		<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
			<i class="icon-search"></i>
		</button>
		<button class="btn hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();"
		        title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>">
			<i class="icon-remove"></i>
		</button>
	</div>
</div>
