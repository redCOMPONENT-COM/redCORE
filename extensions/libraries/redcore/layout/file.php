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
 * @since  3.0
 */
class RLayoutFile extends JLayoutFile
{
	/**
	 * Method to instantiate the file-based layout.
	 *
	 * @param   string  $layoutId  Dot separated path to the layout file, relative to base path
	 * @param   string  $basePath  Base path to use when loading layout files
	 * @param   mixed   $options   Optional custom options to load. Registry or array format [@since 3.2]
	 *
	 * @since   3.0
	 */
	public function __construct($layoutId, $basePath = null, $options = null)
	{
		parent::__construct($layoutId, $basePath, $options);

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
	 * @since   3.5
	 */
	public function getDefaultIncludePaths()
	{
		$paths = parent::getDefaultIncludePaths();

		// Comes after (1 - lower priority) Frontend base layouts
		array_splice($paths, count($paths) - 1, 0, JPATH_LIBRARIES . '/redcore/layouts');

		return $paths;
	}

	/**
	 * Refresh the list of include paths
	 *
	 * @return  void
	 *
	 * @since   3.2
	 */
	protected function refreshIncludePaths()
	{
		parent::clearIncludePaths();

		// If method getDefaultIncludePaths does not exists we are in old Layout system
		if (version_compare(JVERSION, '3.0', '>') && version_compare(JVERSION, '3.5', '<'))
		{
			$redCORELayoutPath = JPATH_LIBRARIES . '/redcore/layouts';

			// If we already added the path, then do not add it again
			if ($this->includePaths[count($this->includePaths) - 1] != $redCORELayoutPath)
			{
				// Comes after (1 - lower priority) Frontend base layouts
				array_splice($this->includePaths, count($this->includePaths) - 1, 0, $redCORELayoutPath);
			}
		}
	}
}
