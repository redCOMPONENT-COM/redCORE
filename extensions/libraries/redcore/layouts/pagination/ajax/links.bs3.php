<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$numberOfPages = (int) $displayData['numberOfPages'];
$currentPage   = (int) $displayData['currentPage'];
$ajaxJS        = $displayData['ajaxJS'];

// Calculate to display range of pages
/**
 * @var   int  $range  Number of pages to show around current page, from start and till end page
 */
$range = (isset($displayData['range']) && ((int) $displayData['range']) > 0) ? (int) $displayData['range'] : 3;

/**
 * @var   int  $paginationButtons  Number of buttons to display inside pagination list (including "..." fields)
 */
$paginationButtons = (isset($displayData['paginationButtons']) && ((int) $displayData['paginationButtons']) > 0) ? (int) $displayData['paginationButtons'] : 17;

if ($numberOfPages > $paginationButtons)
{
	$start  = 1;
	$end    = $numberOfPages;
	$middle = ceil($numberOfPages / 2);

	$startZone    = range($start, $start + $range, 1);
	$middleZone   = range($middle - $range, $middle + $range, 1);
	$endZone      = range($end - $range, $end, 1);
	$pageZone     = range($currentPage - $range, $currentPage + $range, 1);
	$displayZones = array_merge($startZone, $middleZone, $endZone, $pageZone);
	$showPoints   = true;
}
?>

<div class="pagination pagination-toolbar clearfix" style="text-align: center;">
	<ul class="pagination-list">

		<?php
		echo RLayoutHelper::render(
			'pagination.ajax.link',
			array(
				'text'          => JText::_('JLIB_HTML_START'),
				'active'        => ($currentPage > 1) ? true : false,
				'ajaxJS'        => $ajaxJS,
				'currentPage'   => $currentPage,
				'numberOfPages' => $numberOfPages
			)
		);
		echo RLayoutHelper::render(
			'pagination.ajax.link',
			array(
				'text'          => JText::_('JPREVIOUS'),
				'active'        => ($currentPage > 1) ? true : false,
				'ajaxJS'        => $ajaxJS,
				'currentPage'   => $currentPage,
				'numberOfPages' => $numberOfPages
			)
		);?>

		<?php if ($numberOfPages > $paginationButtons - 4): ?>
			<?php for ($i = 1; $i <= $numberOfPages; $i++) : ?>

				<?php if (in_array($i, $displayZones)): ?>
					<?php $output = RLayoutHelper::render(
						'pagination.ajax.link',
						array(
							'text'          => $i,
							'active'        => ($currentPage == $i) ? false : true,
							'ajaxJS'        => $ajaxJS,
							'currentPage'   => $currentPage,
							'numberOfPages' => $numberOfPages
						)
					); ?>

					<?php
					$showPoints = true;
					echo $output;
					?>
				<?php elseif($showPoints) : ?>
					<li><span>...</span></li>
					<?php $showPoints = false; ?>
				<?php endif;?>

			<?php endfor; ?>
		<?php else :?>
			<?php for ($i = 1; $i <= $numberOfPages; $i++) : ?>

				<?php $output = RLayoutHelper::render(
					'pagination.ajax.link',
					array(
						'text'          => $i,
						'active'        => ($currentPage == $i) ? false : true,
						'ajaxJS'        => $ajaxJS,
						'currentPage'   => $currentPage,
						'numberOfPages' => $numberOfPages
					)
				); ?>

				<?php echo $output; ?>
			<?php endfor; ?>
		<?php endif;?>

		<?php
		echo RLayoutHelper::render(
			'pagination.ajax.link',
			array(
				'text'          => JText::_('JNEXT'),
				'active'        => ($currentPage < $numberOfPages) ? true : false,
				'ajaxJS'        => $ajaxJS,
				'currentPage'   => $currentPage,
				'numberOfPages' => $numberOfPages
			)
		);
		echo RLayoutHelper::render(
			'pagination.ajax.link',
			array(
				'text'          => JText::_('JLIB_HTML_END'),
				'active'        => ($currentPage < $numberOfPages) ? true : false,
				'ajaxJS'        => $ajaxJS,
				'currentPage'   => $currentPage,
				'numberOfPages' => $numberOfPages
			)
		);?>

	</ul>
</div>
