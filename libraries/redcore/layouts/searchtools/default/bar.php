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

$options = property_exists('stoolsOptions', $data) ? $data->stoolsOptions : array();

// Receive
$options = new JRegistry($options);
$options->set('filtersApplied', !empty($data->activeFilters));
$options->set('defaultLimit', JFactory::getApplication()->getCfg('list_limit', 20));

$formSelector = $options->get('formSelector', '#adminForm');
$searchString = $options->get('searchString', null);

// Load the jQuery plugin && CSS
JHtml::_('rsearchtools.main');

$doc = JFactory::getDocument();
$script = "
	(function($){
		$(document).ready(function() {
			$('" . $formSelector . "').searchtools(
				" . $options->toString() . "
			);
		});
	})(jQuery);
";
$doc->addScriptDeclaration($script);

// Options
$searchButton     = $options->get('searchButton', true);
$showSearchTitle  = $options->get('showSearchTitle', false);
$showFilterButton = $options->get('filterButton', true);
$showOrderButton  = $options->get('orderButton', true);

$filters = $data->filterForm->getGroup('filter');
?>

<?php if (isset($filters['filter_search'])) : ?>
	<div class="stools-buttons">
		<?php if ($searchButton) : ?>
			<?php if ($showSearchTitle) : ?>
				<label for="filter_search" class="element-invisible">
					<?php echo JText::_('LIB_REDCORE_SEARCHTOOLS_FIELD_SEARCH'); ?>
				</label>
			<?php endif; ?>
			<div class="btn-wrapper input-append">
				<?php echo $filters['filter_search']->input; ?>
				<button type="submit" class="btn hasTooltip" title="<?php echo RHtml::tooltipText('LIB_REDCORE_SEARCHTOOLS_SUBMIT'); ?>">
					<i class="icon-search"></i>
				</button>
			</div>
			<?php if ($showFilterButton) : ?>
				<div class="btn-wrapper">
					<button type="button" class="btn hasTooltip js-stools-btn-filter" title="<?php echo RHtml::tooltipText('LIB_REDCORE_SEARCHTOOLS_DESC'); ?>">
						<?php echo JText::_('LIB_REDCORE_SEARCHTOOLS');?> <i class="caret"></i>
					</button>
				</div>
			<?php endif; ?>
			<div class="btn-wrapper">
				<button type="button" class="btn hasTooltip js-stools-btn-clear" title="<?php echo RHtml::tooltipText('LIB_REDCORE_SEARCHTOOLS_CLEAR_DESC'); ?>">
					<?php echo JText::_('LIB_REDCORE_SEARCHTOOLS_CLEAR');?>
				</button>
			</div>
		<?php endif; ?>
	</div>
<?php endif;