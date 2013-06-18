<?php
/**
 * @package     RedRad
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDRAD') or die;

/**
 * Layout library
 *
 * @package     RedRad
 * @subpackage  Layout
 * @since       1.0
 */
class RLayout extends JLayoutFile
{
	/**
	 * Paths to search for layouts
	 *
	 * @var  array
	 */
	protected $availablePaths = array();

	/**
	 * Client 0 = site | 1 = admin
	 *
	 * @var  integer
	 */
	protected $client = 0;

	/**
	 * Method to instantiate the file-based layout.
	 *
	 * @param   string  $layoutId   Dot separated path to the layout file, relative to base path
	 * @param   string  $component  Active component. Use null to skip it
	 * @param   string  $client     site or 0 for frontend | anything else for backend
	 */
	public function __construct($layoutId, $component = 'auto', $client = 'auto')
	{
		// Autodetect component ?
		if ($component == 'auto')
		{
			$component = JFactory::getApplication()->input->get('option', null);
		}

		$this->initClient($client);

		$this->layoutId = $layoutId;

		// Layouts relative to current component
		if ($component)
		{
			// (1) Component template overrides path
			$basePaths[] = JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/layouts/' . $component;

			// (2) Component path
			if ($this->client == 0)
			{
				$basePaths[] = JPATH_SITE . '/components/' . $component . '/layouts';
			}
			else
			{
				$basePaths[] = JPATH_ADMINISTRATOR . '/components/' . $component . '/layouts';
			}

			// (3) Search a component library created like /libraries/redshop
			$compNameParts = explode('_', $component);

			if (count($compNameParts) == 2)
			{
				$basePaths[] = JPATH_LIBRARIES . '/' . $compNameParts[1] . '/layouts';
			}
		}

		// (4) Library path
		$this->addIncludePaths(JPATH_LIBRARIES . '/redrad/layouts');

		// (5) Standard Joomla! layouts overriden
		$basePaths[] = JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/layouts';

		// (6) Standard Joomla! layouts
		$basePaths[] = $this->addIncludePaths(JPATH_ROOT . '/layouts');

		$this->addIncludePaths($basePaths);
	}

	/**
	 * Add one or more paths to include in layout search
	 *
	 * @param   string  $paths  The path or array of paths to search for layouts
	 *
	 * @return  void
	 */
	public function addIncludePaths($paths)
	{
		if (is_array($paths) && !empty($paths))
		{
			$this->availablePaths = array_merge($paths, $this->availablePaths);
		}
		else
		{
			array_unshift($this->availablePaths, $paths);
		}
	}

	/**
	 * Method to finds the full real file path, checking possible overrides
	 *
	 * @return  string  The full path to the layout file
	 */
	protected function getPath()
	{
		if (is_null($this->fullPath) && !empty($this->layoutId))
		{
			$rawPath  = str_replace('.', '/', $this->layoutId) . '.php';

			$this->fullPath = JPath::find($this->availablePaths, $rawPath);
		}

		return $this->fullPath;
	}

	/**
	 * Function to initialise the application client
	 *
	 * @param   mixed  $client  Frontend: 'site' or 0 | Backend: 'admin' or 1
	 *
	 * @return  integer  The client.
	 */
	protected function initClient($client = 'auto')
	{
		// Force string conversion to avoid unexpected states
		switch ((string) $client)
		{
			case 'site':
			case '0':
				$this->client = 0;
				break;

			case 'admin':
			case '1':
				$this->client = 1;
				break;

			default:
				$this->client = (int) JFactory::getApplication()->isAdmin();
				break;
		}

		return $this->client;
	}

	/**
	 * Dirty function to debug layout path errors
	 *
	 * @return  void
	 */
	public function debugPath()
	{
		echo "<pre>";
		print_r($this->availablePaths);
		$rawPath  = str_replace('.', '/', $this->layoutId) . '.php';
		echo 'Layout: ' . $rawPath . '<br>';

		if ($this->availablePaths)
		{
			foreach ($this->availablePaths as $path)
			{
				$file = $path . '/' . $rawPath;

				if (!file_exists($file))
				{
					echo '!exists: ' . $file . '<br />';
				}
				else
				{
					echo 'exists: ' . $file . '<br />';
					break;
				}
			}
		}

		echo "</pre>";
	}
}
