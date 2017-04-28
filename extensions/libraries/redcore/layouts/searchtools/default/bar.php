<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

use Joomla\Registry\Registry;

$data = $displayData;

// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();

if (is_array($data['options']))
{
	$data['options'] = new Registry($data['options']);
}

// Options
$searchField  = 'filter_' . $data['options']->get('searchField', 'search');
$filterButton = $data['options']->get('filterButton', true);
$searchButton = $data['options']->get('searchButton', true);

$filters = $data['view']->filterForm->getGroup('filter');
?>
<?php if (!empty($filters[$searchField])) : ?>
	<?php if ($searchButton) : ?>
		<label for="filter_search" class="element-invisible">
			<?php if (isset($filters[$searchField]->label)) : ?>
				<?php echo JText::_($filters[$searchField]->label); ?>
			<?php else : ?>
				<?php echo JText::_('LIB_REDCORE_FILTER_SEARCH_DESC'); ?>
			<?php endif; ?>
		</label>
		<div class="btn-wrapper input-append">
			<?php echo $filters[$searchField]->input; ?>
			<?php if ($filters[$searchField]->description) : ?>
				<?php JHtmlBootstrap::tooltip('#' . $searchField, array('title' => JText::_($filters[$searchField]->description))); ?>
			<?php endif; ?>
			<button type="submit" class="btn hasTooltip" title="<?php echo RHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
				<i class="icon-search"></i>
			</button>
		</div>
		<?php if ($filterButton) : ?>
			<div class="btn-wrapper hidden-phone">
				<button type="button" class="btn hasTooltip js-stools-btn-filter" title="<?php echo RHtml::tooltipText('JSEARCH_TOOLS_DESC'); ?>">
					<?php echo JText::_('JSEARCH_TOOLS');?> <i class="caret"></i>
				</button>
			</div>
		<?php endif; ?>
		<div class="btn-wrapper">
			<button type="button" class="btn hasTooltip js-stools-btn-clear" title="<?php echo RHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR');?>
			</button>
		</div>
	<?php endif; ?>
<?php endif;
