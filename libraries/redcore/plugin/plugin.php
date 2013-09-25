<?php
/**
 * @package     Redcore
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * redCORE base plugin
 *
 * @package     Redcore
 * @subpackage  Plugin
 * @since       1.0
 */
class RPlugin extends JPlugin
{
	/**
	 * The extension/plugin id in the #__extensions table
	 *
	 * @var  integer
	 */
	protected $extensionId = null;

	/**
	 * Plugin parameters
	 *
	 * @var  JRegistry
	 */
	public $params = null;

	/**
	 * Constructor
	 *
	 * @param   string  &$subject  Subject
	 * @param   array   $config    Configuration
	 *
	 * @throws  UnexpectedValueException
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		// Load plugin language
		$this->loadLanguage();

		// Ensure that we have plugin type & name
		if (empty($this->_type) || empty($this->_name))
		{
			throw new UnexpectedValueException(sprintf('Missing data to initialize %s plugin | id: %s', $this->_type, $this->_name));
		}

		// Set the extension id required by all the events
		$this->extensionId = $this->getExtensionId();
	}

	/**
	 * Get current plugin id in the #__extensions table
	 *
	 * @return  integer  The plugin / extension id
	 */
	protected function getExtensionId()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('extension_id'))
			->from($db->qn('#__extensions'))
			->where($db->qn('folder') . ' = ' . $db->q($this->_type))
			->where($db->qn('element') . ' = ' . $db->q($this->_name));

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Function to get the path to a layout checking overrides.
	 * It's exactly as it's used in the Joomla! Platform 12.2 to easily replace it when available
	 *
	 * @param   string  $layout  The layout name
	 *
	 * @return  string  Path where we have to use to call the layout
	 */
	protected function getLayoutPath($layout = 'default')
	{
		$type = $this->_type;
		$name = $this->_name;

		$template = JFactory::getApplication()->getTemplate();
		$defaultLayout = $layout;

		if (strpos($layout, ':') !== false)
		{
			// Get the template and file name from the string
			$temp          = explode(':', $layout);
			$template      = ($temp[0] == '_') ? $template : $temp[0];
			$layout        = $temp[1];
			$defaultLayout = ($temp[1]) ? $temp[1] : 'default';
		}

		// Build the template and base path for the layout
		$tPath = JPATH_THEMES . '/' . $template . '/html/plg_' . $type . '_' . $name . '/' . $layout . '.php';
		$bPath = JPATH_SITE . '/plugins/' . $type . '/' . $name . '/tmpl/' . $defaultLayout . '.php';
		$dPath = JPATH_SITE . '/plugins/' . $type . '/' . $name . '/tmpl/' . 'default.php';

		// If the template has a layout override use it
		if (file_exists($tPath))
		{
			return $tPath;
		}
		elseif (file_exists($bPath))
		{
			return $bPath;
		}
		else
		{
			return $dPath;
		}
	}

	/**
	 * function to get the plugin parameters
	 *
	 * @return  JRegistry  The plugin parameters object
	 */
	protected function getParams()
	{
		if (is_null($this->params))
		{
			$plugin = JPluginHelper::getPlugin($this->_type, $this->_name);
			$this->params = new JRegistry($plugin->params);
		}

		return $this->params;
	}
}
