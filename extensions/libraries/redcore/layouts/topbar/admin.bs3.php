<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2021 redWEB.dk. All rights reserved.
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
if ($option == 'com_redcore' && $view == 'config')
{
	$componentUri = JRoute::_('index.php?option=' . $input->getString('component', $option));
}
else
{
	$componentUri = JRoute::_('index.php?option=' . $option);
}

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
	$xml = RComponentHelper::getComponentManifestFile($option);
	$componentName = JText::_($xml->name);
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
<header class="navbar navbar-nav navbar-fixed-top topbar navbar-inverse">
	<div class="navbar-inner">
		<div class="container-fluid">
			<?php if (!$displayJoomlaMenu) : ?>
				<?php if ($displayBackToJoomla) : ?>
				<a class="back2joomla" href="<?php echo JRoute::_('index.php') ?>">
					<i class="icon-undo"></i> Back to Joomla
				</a>
				<?php endif; ?>
				<span class="divider-vertical pull-left"></span>
			<?php endif; ?>
			<a class="navbar-brand" href="<?php echo $componentUri ?>"><?php echo $componentTitle ?></a>
			<div class="navbar-left navbar-collapse hidden-xs hidden-sm">
				<?php if ($displayJoomlaMenu) : ?>
					<?php foreach ($modules as $module): ?>
						<?php echo JModuleHelper::renderModule($module, array('style' => 'standard')); ?>
					<?php endforeach; ?>
				<?php endif; ?>
				<?php if ($displayTopbarInnerLayout) : ?>
					<?php echo RLayoutHelper::render($topbarInnerLayout, $topbarInnerLayoutData) ?>
				<?php endif; ?>
			</div>
			<ul class="nav nav-user navbar-right">
				<li class="dropdown">
					<a href="#" data-toggle="dropdown" class="dropdown-toggle">
						<span class="icon-user"></span>
						<b class="caret"></b>
					</a>
					<ul class="dropdown-menu">
						<li class="">
							<a href="index.php?option=com_admin&amp;task=profile.edit&amp;id=<?php echo $userId; ?>">
								<i class="icon-edit icon-2"></i>
								<?php echo $userName ?>
							</a>
						</li>
						<?php if ($displayComponentVersion) : ?>
							<li>
								<a href="#" onclick="return false">
									<i class="icon-info"></i>
									<?php echo $componentName . ' v' . $version; ?>
								</a>
							</li>
						<?php endif; ?>
						<li class="divider"></li>
						<li class="">
							<a href="index.php?option=com_login&amp;task=logout&amp;<?php echo JSession::getFormToken(); ?>=1&amp;return=<?php echo $logoutReturnUri; ?>">
								<span class="icon-power-off"></span>
								<?php echo JText::_('LIB_REDCORE_ACCOUNT_LOGOUT'); ?>
							</a>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</header>
