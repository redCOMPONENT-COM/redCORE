<?php
/**
 * @package     redCORE
 * @subpackage  Page
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @licence     GNU General Public License version 2 or later; see LICENSE.TXT
 */
namespace Page;

/**
 * Class TemplatePage
 * @package Page
 */
class TemplatePage extends AbstractPage
{
	/**
	 * @var string
	 */
	public static $cPanel = 'Control Panel';

	/**
	 * @var array
	 */
	public static $templatesElement = ['link' => 'Templates'];

	/**
	 * @var string
	 */
	public static $templatesStyles = 'Templates: Styles';

	/**
	 * @var string
	 */
	public static $templatesStylesAdmin = 'Templates: Styles (Administrator)';

	/**
	 * @var string
	 */
	public static $templatesEditStyle = 'Templates: Edit Style';

	/**
	 * @var string
	 */
	public static $templatesStyleSaved = 'Style saved.';

	/**
	 * @var array
	 */
	public static $isisDefault = ['link' => 'isis - Default'];

	/**
	 * @var string
	 */
	public static $pinnedToolbar = 'Pinned Toolbar';
}