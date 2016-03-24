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

$chartOptions = $data['options']['chartOptions'];
$chartType = $data['options']['chartType'];
$chartData = $data['options']['chartData'];
$chartId = $data['options']['chartId'];

RHtmlRchart::addChart($chartType, '#' . $chartId, $chartData, $chartOptions);
?>
<div class="container-fluid chart-container">
	<div class="col-md-12 col-sm-12 chart-box">
		<canvas id="<?php echo $chartId ?>"></canvas>
	</div>
	<div id="<?php echo $chartId ?>Legend" class="chart-legend-container col-md-12 col-sm-12"></div>
</div>
