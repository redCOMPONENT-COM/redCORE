<?php
/**
 * @package     Redcore
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('rlist');

/**
 * Field a list dependent
 *
 * @package     Redcore
 * @subpackage  Field
 * @since       1.0
 */
class JFormFieldRchildlist extends JFormFieldRlist
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $type = 'Rchildlist';

	/**
	 * Options for the ajaxfield script
	 *
	 * @var  array
	 */
	public $ajaxchildOptions = array(
		'formSelector'   => '#adminForm',
		'parentSelector' => '.js-parent-field',
		'parentVarName'  => null,
		'parentOnChange' => true,
		'childSelector'  => null,
		'ajaxUrl'        => null
	);

	/**
	 * Layout to render
	 *
	 * @var  string
	 */
	protected $layout = 'fields.rchildlist';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.0
	 */
	protected function getInput()
	{
		// Receive ajax URL
		$ajaxUrl = isset($this->element['url']) ? (string) $this->element['url'] : null;

		if ($ajaxUrl)
		{
			$siteUrl = JUri::root();
			$adminUrl = $siteUrl . 'administrator';

			$this->ajaxchildOptions['ajaxUrl'] = str_replace(
				array('{admin}', '{backend}', '{site}', '{frontend}'),
				array($adminUrl, $adminUrl, $siteUrl, $siteUrl),
				$ajaxUrl
			);
		}

		// Receive child field selector
		$childSelector = isset($this->element['child_selector']) ? (string) $this->element['child_selector'] : null;

		if ($childSelector)
		{
			$this->ajaxchildOptions['childSelector'] = $childSelector;
		}

		// Receive parent field selector
		$parentSelector = isset($this->element['parent_selector']) ? (string) $this->element['parent_selector'] : null;

		if ($parentSelector)
		{
			$this->ajaxchildOptions['parentSelector'] = $parentSelector;
		}

		// Receive parent request var
		$parentVarName = isset($this->element['parent_varname']) ? (string) $this->element['parent_varname'] : null;

		if ($parentVarName)
		{
			$this->ajaxchildOptions['parentVarName'] = $parentVarName;
		}

		return parent::getInput();
	}
}
