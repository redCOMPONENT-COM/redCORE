<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$data = $displayData;

// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();

if (is_array($data['options']))
{
	$data['options'] = new JRegistry($data['options']);
}

// Options
$searchField  = 'filter_' . $data['options']->get('searchField', 'search');
$filterButton = $data['options']->get('filterButton', true);
$searchButton = $data['options']->get('searchButton', true);

$filters = $data['view']->filterForm->getGroup('filter');
?>

<script type="text/javascript">
jQuery(document).ready(function($){
	jQuery("input[type='text']").addClass('form-control');
});
</script>
<?php if (!empty($filters[$searchField])) : ?>
	<?php if ($searchButton) : ?>
		<section class="form-inline">
	        <div class="form-group">
	            <label for="filter_search" class="element-invisible">
			        <?php echo JText::_('LIB_REDCORE_FILTER_SEARCH_DESC'); ?>
	            </label>
		        <?php echo $filters[$searchField]->input; ?>
	            <button type="submit" class="btn btn-default hasTooltip" title="<?php echo RHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
	                <i class="icon-search"></i>
	            </button>
	        </div>
			<div class="form-group">
				<?php if ($filterButton) : ?>
					<button type="button" class="btn btn-default hasTooltip js-stools-btn-filter hidden-xs" title="<?php echo RHtml::tooltipText('JSEARCH_TOOLS_DESC'); ?>">
						<?php echo JText::_('JSEARCH_TOOLS');?> <i class="caret"></i>
					</button>
				<?php endif; ?>
					<button type="button" class="btn btn-default hasTooltip js-stools-btn-clear" title="<?php echo RHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>">
						<?php echo JText::_('JSEARCH_FILTER_CLEAR');?>
					</button>
				<?php endif; ?>
			</div>
		</section>
<?php endif;
