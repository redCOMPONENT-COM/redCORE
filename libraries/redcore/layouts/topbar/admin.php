<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$data = $displayData;

// Component title (html) for the toolbar.
$componentTitle = '';

if (isset($data['component_title']))
{
	$componentTitle = $data['component_title'];
}

// Do we have to display an inner layout ?
$displayTopbarInnerLayout = false;

if (isset($data['topbar_inner_layout_display']))
{
	$displayTopbarInnerLayout = (bool) $data['topbar_inner_layout_display'];
}

$topbarInnerLayout = '';

// The topbar inner layout name.
if ($displayTopbarInnerLayout)
{
	if (!isset($data['topbar_inner_layout']))
	{
		throw new InvalidArgumentException('No topbar inner layout specified in the component layout.');
	}

	$topbarInnerLayout = $data['topbar_inner_layout'];
}

$topbarInnerLayoutData = array();

if (isset($data['topbar_inner_layout_data']))
{
	$topbarInnerLayoutData = $data['topbar_inner_layout_data'];
}

$user = JFactory::getUser();
$userName = $user->name;
$userId = $user->id;

// Prepare the logout uri or the sign out button.
$input = JFactory::getApplication()->input;
$option = $input->getString('option');
$view = $input->getString('view', 'null');
$returnUri = 'index.php?option=' . $option;

// Prepare the component uri
$componentUri = JRoute::_('index.php?option=' . $option);

if ($view)
{
	$returnUri .= '&view=' . $view;
}

$returnUri = base64_encode($returnUri);

// Joomla menu
$displayJoomlaMenu = false;
$displayBackToJoomla = true;
$displayComponentVersion = false;

if (isset($data['display_joomla_menu']))
{
	$displayJoomlaMenu = (bool) $data['display_joomla_menu'];
}

if (isset($data['display_back_to_joomla']))
{
	$displayBackToJoomla = (bool) $data['display_back_to_joomla'];
}

if ($displayJoomlaMenu)
{
	JLoader::import('joomla.application.module.helper');
	$modules = JModuleHelper::getModules('menu');
}

if (isset($data['display_component_version']))
{
	$displayComponentVersion = (bool) $data['display_component_version'];
	$xmlComponentName = strtolower(substr($option, 4));
	$componentName = ucfirst($xmlComponentName);
	$xml = JFactory::getXML(JPATH_ADMINISTRATOR . '/components/' . $option . '/' . $xmlComponentName . '.xml');
	$version = (string) $xml->version;
}

if (!empty($data['logoutReturnUri']))
{
	$logoutReturnUri = base64_encode($data['logoutReturnUri']);
}
else
{
	$logoutReturnUri = base64_encode('index.php');
}
?>
<script type="text/javascript">
	jQuery(document).ready(function () {
		setInterval(function () {
			updateDateTime();
		}, 1000);
	});

	function updateDateTime() {
		var date = new Date();
		jQuery('.datetime').text(date.toLocaleString());
	}
</script>
<header class="navbar navbar-fixed-top topbar navbar-inverse">
	<div class="navbar-inner">
		<div class="container-fluid">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			<?php if (!$displayJoomlaMenu) : ?>
				<?php if ($displayBackToJoomla) : ?>
				<a class="back2joomla" href="<?php echo JRoute::_('index.php') ?>">
					<i class="icon-undo"></i> Back to Joomla
				</a>
				<?php endif; ?>
				<span class="divider-vertical pull-left"></span>
			<?php endif; ?>
			<a class="brand" href="<?php echo $componentUri ?>"><?php echo $componentTitle ?></a>
			<div class="nav-collapse hidden-phone hidden-tablet">
				<?php if ($displayJoomlaMenu) : ?>
					<?php foreach ($modules as $module): ?>
						<?php echo JModuleHelper::renderModule($module, array('style' => 'standard')); ?>
					<?php endforeach; ?>
				<?php endif; ?>
				<?php if ($displayTopbarInnerLayout) : ?>
					<?php echo RLayoutHelper::render($topbarInnerLayout, $topbarInnerLayoutData) ?>
				<?php endif; ?>
			</div>
			<ul class="nav nav-user pull-right">
				<li class="dropdown">
					<a href="#" data-toggle="dropdown" class="dropdown-toggle">
						<span class="icon-user"></span>
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<li class="">
							<a href="index.php?option=com_admin&amp;task=profile.edit&amp;id=<?php echo $userId; ?>">
								<i class="icon-edit-sign icon-2"></i>
								<?php echo $userName ?>
							</a>
						</li>
						<?php if ($displayComponentVersion) : ?>
							<li>
								<a href="#" onclick="return false">
									<i class="icon-info-sign"></i>
									<?php echo $componentName . ' v' . $version; ?>
								</a>
							</li>
						<?php endif; ?>
						<li class="divider"></li>
						<li class="">
							<a href="index.php?option=com_login&amp;task=logout&amp;<?php echo JSession::getFormToken(); ?>=1&amp;return=<?php echo $logoutReturnUri; ?>">
								<span class="icon-off"></span>
								<?php echo JText::_('LIB_REDCORE_ACCOUNT_LOGOUT'); ?>
							</a>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</header>
