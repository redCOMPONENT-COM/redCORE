<?php
/**
 * @package     Redcore
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Field to load a list of installed components
 *
 * @package     Redcore
 * @subpackage  Field
 * @since       1.0
 */
class JFormFieldComponentlist extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $type = 'Componentlist';

	/**
	 * Cached array of the component items.
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
			$lang = JFactory::getLanguage();

			$options = array();
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select('*')
				->from('#__extensions')
				->where('type=' . $db->quote('component'));

			// Setup the query
			$db->setQuery($query);

			// Return the result
			$components = $db->loadObjectList();

			if (!empty($components))
			{
				foreach ($components as $value)
				{
					$extension = $value->element;
					$source = JPATH_ADMINISTRATOR . '/components/' . $extension;
					$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, true)
					||	$lang->load($extension . '.sys', $source, null, false, true);
					$contentElements = RTranslationHelper::getContentElements($value->name);

					if (!empty($contentElements))
					{
						$contentElements = ' (' . count($contentElements) . ')';
					}
					else
					{
						$contentElements = '';
					}

					$options[] = JHtml::_('select.option', $value->name, JText::_($value->name) . $contentElements);
				}

				static::$cache[$hash] = array_merge(static::$cache[$hash], $options);
			}
		}

		$component = JFactory::getApplication()->input->get->getString('component', '');

		if (!empty($component))
		{
			$this->value = $component;
		}

		return static::$cache[$hash];
	}
}
