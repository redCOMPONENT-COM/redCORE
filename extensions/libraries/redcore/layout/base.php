<?php
/**
 * @package     Redcore.Libraries
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

use Joomla\Registry\Registry;

/**
 * Base class for rendering a display layout.
 * Based on JLayoutBase introduced in Joomla! 3.2.0.
 *
 * @see    https://docs.joomla.org/Sharing_layouts_across_views_or_extensions_with_JLayout
 * @since  1.8.5
 */
abstract class RLayoutBase implements RLayout
{
	/**
	 * Options object
	 *
	 * @var    Registry
	 * @since  1.8.5
	 */
	protected $options = null;

	/**
	 * Data for the layout
	 *
	 * @var    array
	 * @since  1.8.5
	 */
	protected $data = array();

	/**
	 * Debug information messages
	 *
	 * @var    array
	 * @since  1.8.5
	 */
	protected $debugMessages = array();

	/**
	 * Set the options
	 *
	 * @param   array|Registry  $options  Array / Registry object with the options to load
	 *
	 * @return  RLayoutBase  Instance of $this to allow chaining.
	 *
	 * @since   1.8.5
	 */
	public function setOptions($options = null)
	{
		// Check if we have Registry defined or we should use JRegistry instead
		if (class_exists('Registry'))
		{
			// Received Registry
			if ($options instanceof Registry)
			{
				$this->options = $options;
			}
			// Received array
			elseif (is_array($options))
			{
				$this->options = new Registry($options);
			}
			else
			{
				$this->options = new Registry;
			}
		}
		elseif (class_exists('JRegistry'))
		{
			// Received Registry
			if ($options instanceof JRegistry)
			{
				$this->options = $options;
			}
			// Received array
			elseif (is_array($options))
			{
				$this->options = new JRegistry($options);
			}
			else
			{
				$this->options = new JRegistry;
			}
		}

		return $this;
	}

	/**
	 * Get the options
	 *
	 * @return  Registry  Object with the options
	 *
	 * @since   1.8.5
	 */
	public function getOptions()
	{
		// Check if we have Registry defined or we should use JRegistry instead
		if (class_exists('Registry'))
		{
			// Always return a Registry instance
			if (!($this->options instanceof Registry))
			{
				$this->resetOptions();
			}
		}
		elseif (class_exists('JRegistry'))
		{
			// Always return a Registry instance
			if (!($this->options instanceof JRegistry))
			{
				$this->resetOptions();
			}
		}

		return $this->options;
	}

	/**
	 * Function to empty all the options
	 *
	 * @return  RLayoutBase  Instance of $this to allow chaining.
	 *
	 * @since   1.8.5
	 */
	public function resetOptions()
	{
		return $this->setOptions(null);
	}

	/**
	 * Method to escape output.
	 *
	 * @param   string  $output  The output to escape.
	 *
	 * @return  string  The escaped output.
	 *
	 * @since   1.8.5
	 */
	public function escape($output)
	{
		return htmlspecialchars($output, ENT_COMPAT, 'UTF-8');
	}

	/**
	 * Get the debug messages array
	 *
	 * @return  array
	 *
	 * @since   1.8.5
	 */
	public function getDebugMessages()
	{
		return $this->debugMessages;
	}

	/**
	 * Method to render the layout.
	 *
	 * @param   array  $displayData  Array of properties available for use inside the layout file to build the displayed output
	 *
	 * @return  string  The necessary HTML to display the layout
	 *
	 * @since   1.8.5
	 */
	public abstract function render($displayData);

	/**
	 * Render the list of debug messages
	 *
	 * @return  string  Output text/HTML code
	 *
	 * @since   1.8.5
	 */
	public function renderDebugMessages()
	{
		return implode($this->debugMessages, "\n");
	}

	/**
	 * Add a debug message to the debug messages array
	 *
	 * @param   string  $message  Message to save
	 *
	 * @return  self
	 *
	 * @since   1.8.5
	 */
	public function addDebugMessage($message)
	{
		$this->debugMessages[] = $message;

		return $this;
	}

	/**
	 * Clear the debug messages array
	 *
	 * @return  self
	 *
	 * @since   1.8.5
	 */
	public function clearDebugMessages()
	{
		$this->debugMessages = array();

		return $this;
	}

	/**
	 * Render a layout with debug info
	 *
	 * @param   mixed  $data  Data passed to the layout
	 *
	 * @return  string
	 *
	 * @since   1.8.5
	 */
	public function debug($data = array())
	{
		$this->setDebug(true);

		$output = $this->render($data);

		$this->setDebug(false);

		return $output;
	}

	/**
	 * Method to get the value from the data array
	 *
	 * @param   string  $key           Key to search for in the data array
	 * @param   mixed   $defaultValue  Default value to return if the key is not set
	 *
	 * @return  mixed   Value from the data array | defaultValue if doesn't exist
	 *
	 * @since   1.8.5
	 */
	public function get($key, $defaultValue = null)
	{
		return isset($this->data[$key]) ? $this->data[$key] : $defaultValue;
	}

	/**
	 * Get the data being rendered
	 *
	 * @return  array
	 *
	 * @since   1.8.5
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Check if debug mode is enabled
	 *
	 * @return  boolean
	 *
	 * @since   1.8.5
	 */
	public function isDebugEnabled()
	{
		return $this->getOptions()->get('debug', false) === true;
	}

	/**
	 * Method to set a value in the data array. Example: $layout->set('items', $items);
	 *
	 * @param   string  $key    Key for the data array
	 * @param   mixed   $value  Value to assign to the key
	 *
	 * @return  self
	 *
	 * @since   1.8.5
	 */
	public function set($key, $value)
	{
		$this->data[(string) $key] = $value;

		return $this;
	}

	/**
	 * Set the the data passed the layout
	 *
	 * @param   array  $data  Array with the data for the layout
	 *
	 * @return  self
	 *
	 * @since   1.8.5
	 */
	public function setData(array $data)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * Change the debug mode
	 *
	 * @param   boolean  $debug  Enable / Disable debug
	 *
	 * @return  void
	 *
	 * @since   1.8.5
	 */
	public function setDebug($debug)
	{
		$this->options->set('debug', (boolean) $debug);
	}
}
