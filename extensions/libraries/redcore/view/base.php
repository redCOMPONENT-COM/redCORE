<?php
/**
 * @package     Redcore
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * A view that can be rendered in full screen.
 *
 * @package     Redcore
 * @subpackage  View
 * @since       1.0
 */
abstract class RViewBase extends JViewLegacy
{
	/**
	 * The component title to display in the topbar layout (if using it).
	 * It can be html.
	 *
	 * @var string
	 */
	protected $componentTitle = '';

	/**
	 * Layout used to render the component
	 *
	 * @var  string
	 */
	protected $componentLayout = 'component.site';

	/**
	 * The topbar layout name to display.
	 *
	 * @var  boolean
	 */
	protected $topBarLayout = 'topbar.site';

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 */
	public function display($tpl = null)
	{
		$render = RLayoutHelper::render(
			$this->componentLayout,
			array(
				'view' => $this,
				'tpl' => $tpl,
				'component_title' => $this->componentTitle,
				'topbar_layout' => $this->topBarLayout,
			)
		);

		if ($render instanceof Exception)
		{
			return $render;
		}

		echo $render;

		return true;
	}

	/**
	 * Get the view title.
	 *
	 * @return  string  The view title.
	 */
	public function getTitle()
	{
		return '';
	}

	/**
	 * Load a template file -- first look in the templates folder for an override
	 *
	 * @param   string  $tpl  The name of the template source file; automatically searches the template paths and compiles as needed.
	 *
	 * @return  string  The output of the the template script.
	 *
	 * @since   10.3
	 * @throws  Exception
	 */
	public function loadTemplate($tpl = null)
	{
		// Uses the parent function if no framework suffix is present
		if (RHtmlMedia::$frameworkSuffix == '')
		{
			return parent::loadTemplate($tpl);
		}

		// Clear prior output
		$this->_output = null;

		$template = JFactory::getApplication()->getTemplate();
		$layout = $this->getLayout();
		$layoutTemplate = $this->getLayoutTemplate();

		// Create the template file name based on the layout
		$file = isset($tpl) ? $layout . '_' . $tpl : $layout;

		// Clean the file name
		$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
		$tpl = isset($tpl) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl) : $tpl;

		// Load the language file for the template
		$lang = JFactory::getLanguage();
		$lang->load('tpl_' . $template, JPATH_BASE, null, false, true)
		|| $lang->load('tpl_' . $template, JPATH_THEMES . "/$template", null, false, true);

		// Change the template folder if alternative layout is in different template
		if (isset($layoutTemplate) && $layoutTemplate != '_' && $layoutTemplate != $template)
		{
			$this->_path['template'] = str_replace($template, $layoutTemplate, $this->_path['template']);
		}

		// Load the template script
		jimport('joomla.filesystem.path');

		// Tries first with the framework suffix
		$filetofind = $this->_createFileName('template', array('name' => $file . '.' . RHtmlMedia::$frameworkSuffix));
		$this->_template = JPath::find($this->_path['template'], $filetofind);

		// If no framework-speficic is found, tries with the normal alternate layout
		if ($this->_template == false)
		{
			$filetofind      = $this->_createFileName('template', array('name' => $file));
			$this->_template = JPath::find($this->_path['template'], $filetofind);
		}

		// If alternate layout can't be found, fall back to default layout, first with framework suffix
		if ($this->_template == false)
		{
			$filetofind = $this->_createFileName(
				'', array('name' => 'default' . (isset($tpl) ? '_' . $tpl : $tpl) . '.' . RHtmlMedia::$frameworkSuffix)
			);
			$this->_template = JPath::find($this->_path['template'], $filetofind);
		}

		// If no default with suffix is found, fall back to default layout without framework suffix
		if ($this->_template == false)
		{
			$filetofind = $this->_createFileName('', array('name' => 'default' . (isset($tpl) ? '_' . $tpl : $tpl)));
			$this->_template = JPath::find($this->_path['template'], $filetofind);
		}

		if ($this->_template != false)
		{
			// Unset so as not to introduce into template scope
			unset($tpl);
			unset($file);

			// Never allow a 'this' property
			if (isset($this->this))
			{
				unset($this->this);
			}

			// Start capturing output into a buffer
			ob_start();

			// Include the requested template filename in the local scope
			// (this will execute the view logic).
			include $this->_template;

			// Done with the requested template; get the buffer and
			// clear it.
			$this->_output = ob_get_contents();
			ob_end_clean();

			return $this->_output;
		}
		else
		{
			throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $file), 500);
		}
	}
}
