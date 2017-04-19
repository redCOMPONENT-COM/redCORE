<?php
/**
 * @package     Redcore
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

/**
 * Base class for rendering a display layout
 * loaded from from a layout file
 *
 * @see    https://docs.joomla.org/Sharing_layouts_across_views_or_extensions_with_JLayout
 * @since  1.8.8
 */
class RLayoutFile extends RLayoutBase
{
	/**
	 * Cached layout paths
	 *
	 * @var    array
	 * @since  1.8.8
	 */
	protected static $cache = array();

	/**
	 * Dot separated path to the layout file, relative to base path
	 *
	 * @var    string
	 * @since  1.8.8
	 */
	protected $layoutId = '';

	/**
	 * Base path to use when loading layout files
	 *
	 * @var    string
	 * @since  1.8.8
	 */
	protected $basePath = null;

	/**
	 * Full path to actual layout files, after possible template override check
	 *
	 * @var    string
	 * @since  1.8.8
	 */
	protected $fullPath = null;

	/**
	 * Paths to search for layouts
	 *
	 * @var    array
	 * @since  1.8.8
	 */
	protected $includePaths = array();

	/**
	 * Method to instantiate the file-based layout.
	 *
	 * @param   string  $layoutId  Dot separated path to the layout file, relative to base path
	 * @param   string  $basePath  Base path to use when loading layout files
	 * @param   mixed   $options   Optional custom options to load. Registry or array format [@since 3.2]
	 *
	 * @since   1.8.8
	 */
	public function __construct($layoutId, $basePath = null, $options = null)
	{
		// Initialise / Load options
		$this->setOptions($options);

		// Main properties
		$this->setLayout($layoutId);
		$this->basePath = $basePath;

		// Init Environment
		$this->setComponent($this->options->get('component', 'auto'));
		$this->setClient($this->options->get('client', 'auto'));

		// Add suffixes for .bs2 and .bs3
		if (!empty(RHtmlMedia::$frameworkSuffix))
		{
			$suffixes = $this->options->get('suffixes', array());

			foreach ($suffixes as &$suffix)
			{
				$suffix .= '.' . RHtmlMedia::$frameworkSuffix;
			}

			$suffixes[] = RHtmlMedia::$frameworkSuffix;

			$this->options->set('suffixes', $suffixes);
		}
	}

	/**
	 * Get the default array of include paths
	 *
	 * @return  array
	 *
	 * @since   1.8.8
	 */
	public function getDefaultIncludePaths()
	{
		// Reset includePaths
		$paths = array();

		// (1 - highest priority) Received a custom high priority path
		if (!is_null($this->basePath))
		{
			$paths[] = rtrim($this->basePath, DIRECTORY_SEPARATOR);
		}

		// Component layouts & overrides if exist
		$component = $this->options->get('component', null);

		if (!empty($component))
		{
			// (2) Component template overrides path
			$paths[] = JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/layouts/' . $component;

			// (3) Component path
			if ($this->options->get('client') == 0)
			{
				$paths[] = JPATH_SITE . '/components/' . $component . '/layouts';
			}
			else
			{
				$paths[] = JPATH_ADMINISTRATOR . '/components/' . $component . '/layouts';
			}
		}

		// (4) Standard Joomla! layouts overriden
		$paths[] = JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/layouts';

		// (5 - lower priority) Frontend base layouts
		$paths[] = JPATH_LIBRARIES . '/redcore/layouts';

		// (6 - lower priority) Frontend base layouts
		$paths[] = JPATH_ROOT . '/layouts';

		return $paths;
	}

	/**
	 * Refresh the list of include paths
	 *
	 * @return  void
	 *
	 * @since   1.8.8
	 */
	protected function refreshIncludePaths()
	{
		$this->clearIncludePaths();
		$this->includePaths = $this->getIncludePaths();
	}

	/**
	 * Method to render the layout.
	 *
	 * @param   array  $displayData  Array of properties available for use inside the layout file to build the displayed output
	 *
	 * @return  string  The necessary HTML to display the layout
	 *
	 * @since   1.8.8
	 */
	public function render($displayData = array())
	{
		$this->clearDebugMessages();

		// Inherit base output from parent class
		$layoutOutput = parent::render($displayData);

		// Automatically merge any previously data set if $displayData is an array
		if (is_array($displayData))
		{
			$displayData = array_merge($this->data, $displayData);
		}

		// Check possible overrides, and build the full path to layout file
		$path = $this->getPath();

		if ($this->isDebugEnabled())
		{
			echo "<pre>" . $this->renderDebugMessages() . "</pre>";
		}

		// Nothing to show
		if (empty($path))
		{
			return $layoutOutput;
		}

		ob_start();
		include $path;
		$layoutOutput .= ob_get_contents();
		ob_end_clean();

		return $layoutOutput;
	}

	/**
	 * Method to finds the full real file path, checking possible overrides
	 *
	 * @return  string  The full path to the layout file
	 *
	 * @since   1.8.8
	 */
	protected function getPath()
	{
		JLoader::import('joomla.filesystem.path');

		$layoutId     = $this->getLayoutId();
		$includePaths = $this->getIncludePaths();
		$suffixes     = $this->getSuffixes();

		$this->addDebugMessage('<strong>Layout:</strong> ' . $this->layoutId);

		if (!$layoutId)
		{
			$this->addDebugMessage('<strong>There is no active layout</strong>');

			return null;
		}

		if (!$includePaths)
		{
			$this->addDebugMessage('<strong>There are no folders to search for layouts:</strong> ' . $layoutId);

			return null;
		}

		$hash = md5(
			json_encode(
				array(
					'paths'    => $includePaths,
					'suffixes' => $suffixes
				)
			)
		);

		if (!empty(static::$cache[$layoutId][$hash]))
		{
			$this->addDebugMessage('<strong>Cached path:</strong> ' . static::$cache[$layoutId][$hash]);

			return static::$cache[$layoutId][$hash];
		}

		$this->addDebugMessage('<strong>Include Paths:</strong> ' . print_r($includePaths, true));

		// Search for suffixed versions. Example: tags.j31.php
		if ($suffixes)
		{
			$this->addDebugMessage('<strong>Suffixes:</strong> ' . print_r($suffixes, true));

			foreach ($suffixes as $suffix)
			{
				$rawPath  = str_replace('.', '/', $this->layoutId) . '.' . $suffix . '.php';
				$this->addDebugMessage('<strong>Searching layout for:</strong> ' . $rawPath);

				if ($foundLayout = JPath::find($this->includePaths, $rawPath))
				{
					$this->addDebugMessage('<strong>Found layout:</strong> ' . $this->fullPath);

					static::$cache[$layoutId][$hash] = $foundLayout;

					return static::$cache[$layoutId][$hash];
				}
			}
		}

		// Standard version
		$rawPath  = str_replace('.', '/', $this->layoutId) . '.php';
		$this->addDebugMessage('<strong>Searching layout for:</strong> ' . $rawPath);

		$foundLayout = JPath::find($this->includePaths, $rawPath);

		if (!$foundLayout)
		{
			$this->addDebugMessage('<strong>Unable to find layout: </strong> ' . $layoutId);

			return null;
		}

		$this->addDebugMessage('<strong>Found layout:</strong> ' . $foundLayout);

		static::$cache[$layoutId][$hash] = $foundLayout;

		return static::$cache[$layoutId][$hash];
	}

	/**
	 * Add one path to include in layout search. Proxy of addIncludePaths()
	 *
	 * @param   string  $path  The path to search for layouts
	 *
	 * @return  self
	 *
	 * @since   1.8.8
	 */
	public function addIncludePath($path)
	{
		$this->addIncludePaths($path);

		return $this;
	}

	/**
	 * Add one or more paths to include in layout search
	 *
	 * @param   string  $paths  The path or array of paths to search for layouts
	 *
	 * @return  self
	 *
	 * @since   1.8.8
	 */
	public function addIncludePaths($paths)
	{
		if (empty($paths))
		{
			return $this;
		}

		$includePaths = $this->getIncludePaths();

		if (is_array($paths))
		{
			$includePaths = array_unique(array_merge($paths, $includePaths));
		}
		else
		{
			array_unshift($includePaths, $paths);
		}

		$this->setIncludePaths($includePaths);

		return $this;
	}

	/**
	 * Clear the include paths
	 *
	 * @return  self
	 *
	 * @since   1.8.8
	 */
	public function clearIncludePaths()
	{
		$this->includePaths = array();

		return $this;
	}

	/**
	 * Get the active include paths
	 *
	 * @return  array
	 *
	 * @since   1.8.8
	 */
	public function getIncludePaths()
	{
		if (empty($this->includePaths))
		{
			$this->includePaths = $this->getDefaultIncludePaths();
		}

		return $this->includePaths;
	}

	/**
	 * Get the active layout id
	 *
	 * @return  string
	 *
	 * @since   1.8.8
	 */
	public function getLayoutId()
	{
		return $this->layoutId;
	}

	/**
	 * Get the active suffixes
	 *
	 * @return  array
	 *
	 * @since   1.8.8
	 */
	public function getSuffixes()
	{
		return $this->getOptions()->get('suffixes', array());
	}

	/**
	 * Load the automatically generated language suffixes.
	 * Example: array('es-ES', 'es', 'ltr')
	 *
	 * @return  self
	 *
	 * @since   1.8.8
	 */
	public function loadLanguageSuffixes()
	{
		$lang = JFactory::getLanguage();

		$langTag = $lang->getTag();
		$langParts = explode('-', $langTag);

		$suffixes = array($langTag, $langParts[0]);
		$suffixes[] = $lang->isRTL() ? 'rtl' : 'ltr';

		$this->setSuffixes($suffixes);

		return $this;
	}

	/**
	 * Load the automatically generated version suffixes.
	 * Example: array('j311', 'j31', 'j3')
	 *
	 * @return  self
	 *
	 * @since   1.8.8
	 */
	public function loadVersionSuffixes()
	{
		$cmsVersion = new JVersion;

		// Example j311
		$fullVersion = 'j' . str_replace('.', '', $cmsVersion->getShortVersion());

		// Create suffixes like array('j311', 'j31', 'j3')
		$suffixes = array(
			$fullVersion,
			substr($fullVersion, 0, 3),
			substr($fullVersion, 0, 2),
		);

		$this->setSuffixes(array_unique($suffixes));

		return $this;
	}

	/**
	 * Remove one path from the layout search
	 *
	 * @param   string  $path  The path to remove from the layout search
	 *
	 * @return  self
	 *
	 * @since   1.8.8
	 */
	public function removeIncludePath($path)
	{
		$this->removeIncludePaths($path);

		return $this;
	}

	/**
	 * Remove one or more paths to exclude in layout search
	 *
	 * @param   string  $paths  The path or array of paths to remove for the layout search
	 *
	 * @return  self
	 *
	 * @since   1.8.8
	 */
	public function removeIncludePaths($paths)
	{
		if (!empty($paths))
		{
			$paths = (array) $paths;

			$this->includePaths = array_diff($this->includePaths, $paths);
		}

		return $this;
	}

	/**
	 * Validate that the active component is valid
	 *
	 * @param   string  $option  URL Option of the component. Example: com_content
	 *
	 * @return  boolean
	 *
	 * @since   1.8.8
	 */
	protected function validComponent($option = null)
	{
		// By default we will validate the active component
		$component = ($option !== null) ? $option : $this->options->get('component', null);

		// Valid option format
		if (!empty($component) && substr_count($component, 'com_'))
		{
			// Latest check: component exists and is enabled
			return JComponentHelper::isEnabled($component);
		}

		return false;
	}

	/**
	 * Method to change the component where search for layouts
	 *
	 * @param   string  $option  URL Option of the component. Example: com_content
	 *
	 * @return  mixed  Component option string | null for none
	 *
	 * @since   1.8.8
	 */
	public function setComponent($option)
	{
		$component = null;

		switch ((string) $option)
		{
			case 'none':
				$component = null;
				break;

			case 'auto':
				$component = JApplicationHelper::getComponentName();
				break;

			default:
				$component = $option;
				break;
		}

		// Extra checks
		if (!$this->validComponent($component))
		{
			$component = null;
		}

		$this->options->set('component', $component);

		// Refresh include paths
		$this->refreshIncludePaths();
	}

	/**
	 * Function to initialise the application client
	 *
	 * @param   mixed  $client  Frontend: 'site' or 0 | Backend: 'admin' or 1
	 *
	 * @return  void
	 *
	 * @since   1.8.8
	 */
	public function setClient($client)
	{
		// Force string conversion to avoid unexpected states
		switch ((string) $client)
		{
			case 'site':
			case '0':
				$client = 0;
				break;

			case 'admin':
			case '1':
				$client = 1;
				break;

			default:
				$client = (int) JFactory::getApplication()->isAdmin();
				break;
		}

		$this->options->set('client', $client);

		// Refresh include paths
		$this->refreshIncludePaths();
	}

	/**
	 * Change the layout
	 *
	 * @param   string  $layoutId  Layout to render
	 *
	 * @return  self
	 *
	 * @since   1.8.8
	 *
	 * @deprecated  3.5  Use setLayoutId()
	 */
	public function setLayout($layoutId)
	{
		// Log usage of deprecated function
		JLog::add(__METHOD__ . '() is deprecated, use JLayoutFile::setLayoutId() instead.', JLog::WARNING, 'deprecated');

		return $this->setLayoutId($layoutId);
	}

	/**
	 * Set the active layout id
	 *
	 * @param   string  $layoutId  Layout identifier
	 *
	 * @return  self
	 *
	 * @since   1.8.8
	 */
	public function setLayoutId($layoutId)
	{
		$this->layoutId = $layoutId;
		$this->fullPath = null;

		return $this;
	}

	/**
	 * Set the include paths to search for layouts
	 *
	 * @param   array  $paths  Array with paths to search in
	 *
	 * @return  self
	 *
	 * @since   1.8.8
	 */
	public function setIncludePaths($paths)
	{
		$this->includePaths = (array) $paths;

		return $this;
	}

	/**
	 * Set suffixes to search layouts
	 *
	 * @param   mixed  $suffixes  String with a single suffix or 'auto' | 'none' or array of suffixes
	 *
	 * @return  self
	 *
	 * @since   1.8.8
	 */
	public function setSuffixes(array $suffixes)
	{
		$this->options->set('suffixes', $suffixes);

		return $this;
	}

	/**
	 * Render a layout with the same include paths & options
	 *
	 * @param   object  $layoutId     Object which properties are used inside the layout file to build displayed output
	 * @param   mixed   $displayData  Data to be rendered
	 *
	 * @return  string  The necessary HTML to display the layout
	 *
	 * @since   1.8.8
	 */
	public function sublayout($layoutId, $displayData)
	{
		// Sublayouts are searched in a subfolder with the name of the current layout
		if (!empty($this->layoutId))
		{
			$layoutId = $this->layoutId . '.' . $layoutId;
		}

		$sublayout = new static($layoutId, $this->basePath, $this->options);
		$sublayout->includePaths = $this->includePaths;

		return $sublayout->render($displayData);
	}
}
