<?php
/**
 * @package     Redcore
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Site view for redCORE based components
 *
 * @package     Redcore
 * @subpackage  View
 * @since       1.0
 */
abstract class RViewSite extends RViewBase
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
}
