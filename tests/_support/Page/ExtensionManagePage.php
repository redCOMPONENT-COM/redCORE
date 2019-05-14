<?php
/**
 * @package     redCORE
 * @subpackage  Page
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Page;

/**
 * Class ExtensionManagePage
 * @package Page
 */
class ExtensionManagePage extends AbstractPage
{
	/**
	 * @var string
	 */
	public static $URL = '/administrator/index.php?option=com_installer&view=manage';

	/**
	 * @var string
	 */
	public static $textType = 'Component';

	/**
	 * @var string
	 */
	public static $textSearch = 'redCORE';

	/**
	 * @var string
	 */
	public static $noItems = '.alert-no-items';

	/**
	 * @var string
	 */
	public static $selectType = '- Select Type -';

	/**
	 * @var string
	 */
	public static $textSuccess = 'Uninstalling the component was successful';

	/**
	 * @var string
	 */
	public static $textNoExtensions = 'There are no extensions installed matching your query.';
}
