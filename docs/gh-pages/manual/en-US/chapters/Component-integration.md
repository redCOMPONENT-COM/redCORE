To integrate redCORE in your component:

You can start using redCORE directly in the installer script (most of the times `install.php`):

```
<?php
/**
 * @package     Redshopb
 * @subpackage  Install
 *
 * @copyright   Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

// Find redCORE installer to use it as base system
if (!class_exists('Pkg_RedcoreInstallerScript'))
{
	$searchPaths = array(
		// Install
		dirname(__FILE__) . '/redCORE',
		// Discover install
		JPATH_ADMINISTRATOR . '/manifests/packages/redcore',
		// Uninstall
		JPATH_LIBRARIES . '/redcore'
	);

	if ($redcoreInstaller = JPath::find($searchPaths, 'install.php'))
	{
		require_once $redcoreInstaller;
	}
}

/**
 * Custom installation of Redshop b2b.
 *
 * @package     Redshopb
 * @subpackage  Install
 * @since       1.0
 */
class Com_RedshopbInstallerScript extends Pkg_RedcoreInstallerScript
{
}
```

That will extend the redCORE installer (**Pkg_RedcoreInstallerScript**) so your installer is 100% free of redCORE stuff. When your component gets installed the parent class will automatically install redCORE.

To be detected as a **redCORE component** the manifest also requires that you add:

```
<redcore version="1.0" />
```
Manifest example:
```
    <name>COM_REDSOURCE</name>
    <creationDate>June 2013</creationDate>
    <author>redCOMPONENT</author>
    <authorEmail>email@redcomponent.com</authorEmail>
    <authorUrl>www.redcomponent.com</authorUrl>
    <copyright>Copyright (C) 2008 - 2013 redCOMPONENT.com. All rights reserved.</copyright>
    <license>GNU General Public License version 2 or later, see LICENSE.</license>
    <version>1.0.0</version>
    <description>COM_REDSOURCE_DESC</description>
    <scriptfile>install.php</scriptfile>
    <redcore version="1.0" />
```

That information is also stored in the standard component parameters so you can do:

```
<?php
$comParams = JComponentHelper::getParams('com_redshopb');
$redcoreParams = $comParams->get('redcore');
```