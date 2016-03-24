<?php
/**
 * @package     Redcore.Translation
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('rdropdown.init');
JHtml::_('rbootstrap.tooltip');
JHtml::_('rjquery.chosen', 'select');

$original = isset($displayData['original']) ? $displayData['original'] : null;
$translation = isset($displayData['translation']) ? $displayData['translation'] : null;
$name = !empty($displayData['name']) ? $displayData['name'] : '';
$column = !empty($displayData['column']) ? $displayData['column'] : null;
$predefinedOptions = !empty($displayData['predefinedOptions']) ? $displayData['predefinedOptions'] : array();
$translationForm = !empty($displayData['translationForm']) ? $displayData['translationForm'] : null;

if (empty($column['stateoptions']))
{
	$optionValues = '0,1';
}
else
{
	$optionValues = $column['stateoptions'];
}

$optionValues = explode(',', $optionValues);
$options = array();
$options[] = JHtml::_('select.option', '', JText::_('JLIB_HTML_SELECT_STATE'));

foreach ($optionValues as $optionValue)
{
	if (!empty($predefinedOptions[$optionValue]))
	{
		$options[] = JHtml::_('select.option', $optionValue, JText::_($predefinedOptions[$optionValue]));
	}
}

echo JHtml::_(
	'select.genericlist',
	$options,
	'translation[' . $name . ']',
	array(),
	'value',
	'text',
	isset($translation) ? $translation : ''
);
