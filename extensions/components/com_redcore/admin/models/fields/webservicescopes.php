<?php
/**
 * @package     Redcore
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Field to load a list of available webservice scopes
 *
 * @package     Redcore
 * @subpackage  Field
 * @since       1.0
 */
class JFormFieldWebservicescopes extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $type = 'Webservicescopes';

	/**
	 * Cached array of the items.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected static $cache = array();

	/**
	 * Method to get the options to populate to populate list
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.0
	 */
	protected function getOptions()
	{
		// Accepted modifiers
		$hash = md5($this->element);

		if (!isset(static::$cache[$hash]))
		{
			static::$cache[$hash] = parent::getOptions();

			$options = RApiHalHelper::getWebserviceScopes();

			static::$cache[$hash] = array_merge(static::$cache[$hash], $options);
		}

		return static::$cache[$hash];
	}

	/**
	 * Method to get the field input markup for OAuth2 Scope Lists.
	 *
	 * @return  string  The field input markup.
	 */
	protected function getInput()
	{
		// ShowCheckAll attribute process
		$showCheckAll = false;

		if ($this->getAttribute('showCheckAll', false) == true)
		{
			$showCheckAll = true;
		}

		return RLayoutHelper::render(
			'webservice.scopes',
			array(
				'view' => $this,
				'options' => array (
					'scopes' => $this->getOptions(),
					'id' => $this->id,
					'name' => $this->name,
					'label' => $this->label,
					'value' => $this->value,
					'showCheckAll' => $showCheckAll
				)
			)
		);
	}
}
