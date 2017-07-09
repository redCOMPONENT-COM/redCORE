<?php
/**
 * @package     Redcore
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('rlist');

/**
 * Field a list dependent
 *
 * @since  1.7
 */
class RFormFieldChildlist extends JFormFieldRlist
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	public $type = 'Childlist';

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
	protected $layout = 'redcore.field.childlist';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// Receive ajax URL
		$ajaxUrl = isset($this->element['url']) ? (string) $this->element['url'] : null;

		if (!is_null($ajaxUrl))
		{
			$siteUrl = JUri::root();
			$adminUrl = $siteUrl . 'administrator';

			$this->ajaxchildOptions['ajaxUrl'] = str_replace(
				array('{admin}', '{backend}', '{site}', '{frontend}'),
				array($adminUrl, $adminUrl, $siteUrl, $siteUrl),
				$ajaxUrl
			);

			// Automatically attach a token
			$this->ajaxchildOptions['ajaxUrl'] .= '&' . JSession::getFormToken() . '=1';
		}

		// Receive child field selector
		$childSelector = isset($this->element['child_selector']) ? (string) $this->element['child_selector'] : null;

		if (!is_null($childSelector))
		{
			$this->ajaxchildOptions['childSelector'] = $childSelector;
		}

		// Receive parent field selector
		$parentSelector = isset($this->element['parent_selector']) ? (string) $this->element['parent_selector'] : null;

		if (!is_null($parentSelector))
		{
			$this->ajaxchildOptions['parentSelector'] = $parentSelector;
		}

		// Receive parent request var
		$parentVarName = isset($this->element['parent_varname']) ? (string) $this->element['parent_varname'] : null;

		if (!is_null($parentVarName))
		{
			$this->ajaxchildOptions['parentVarName'] = $parentVarName;
		}

		return parent::getInput();
	}
}
