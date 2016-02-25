<?php
/**
 * @package     Redcore
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
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
class JFormFieldPaymentplugins extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $type = 'Paymentplugins';

	/**
	 * Cached array of the plugin items.
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
				->where($db->qn('type') . ' = "plugin"')
				->where($db->qn('folder') . ' = "redpayment"');

			// Setup the query
			$db->setQuery($query);

			// Return the result
			$plugins = $db->loadObjectList();

			if (!empty($plugins))
			{
				foreach ($plugins as $value)
				{
					$extension = 'plg_redpayment_' . $value->element;
					$source = JPATH_PLUGINS . '/redpayment/' . $value->element;
					$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, true)
					||	$lang->load($extension . '.sys', $source, null, false, true);

					if ($this->getAttribute('showTitle', 'true') == 'true')
					{
						$params = new JRegistry($value->params);
						$title = $params->get('payment_title', $extension);
					}
					elseif ($this->getAttribute('showFullName', 'false') == 'true')
					{
						$title = JText::_($value->name);
					}
					else
					{
						$title = $extension;
					}

					$options[] = JHtml::_('select.option', $value->element, $title);
				}

				static::$cache[$hash] = array_merge(static::$cache[$hash], $options);
			}
		}

		return static::$cache[$hash];
	}
}
