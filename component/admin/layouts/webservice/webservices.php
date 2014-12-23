<?php
/**
 * @package     Redcore.Webservice
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

$webservices = !empty($displayData['webservices']) ? $displayData['webservices'] : array();
$missingWebservices = !empty($displayData['missingWebservices']) ? $displayData['missingWebservices'] : array();
$column = 0;
?>


