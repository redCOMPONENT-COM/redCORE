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

if (isset($data['display_joomla_menu']))
{
	$displayJoomlaMenu = (bool) $data['display_joomla_menu'];
}

if ($displayJoomlaMenu)
{
	JLoader::import('joomla.application.module.helper');
	$modules = JModuleHelper::getModules('menu');
}
?>
<header class="navbar navbar-fixed-top topbar">
	<div class="navbar-inner">
		<div class="container-fluid">
			<?php if (!$displayJoomlaMenu) : ?>
				<a class="back2joomla" href="<?php echo JRoute::_('index.php') ?>">
					<i class="icon-undo"></i> Back to Joomla
				</a>
				<span class="divider-vertical pull-left"></span>
			<?php endif; ?>
			<a class="brand" href="<?php echo $componentUri ?>"><?php echo $componentTitle ?></a>
			<?php if ($displayJoomlaMenu) : ?>
				<?php foreach ($modules as $module): ?>
					<?php echo JModuleHelper::renderModule($module, array('style' => 'standard')); ?>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php if ($displayTopbarInnerLayout) : ?>
				<?php echo RLayoutHelper::render($topbarInnerLayout, $topbarInnerLayoutData) ?>
			<?php endif; ?>
			<div class="nav-right pull-right hidden-tablet hidden-phone">
				<div class="datetime pull-right"></div>
				<span class="divider-vertical pull-right"></span>

				<div class="logout pull-right">
					<a href="<?php echo
					JRoute::_('index.php?option=com_login&task=logout&' . JSession::getFormToken() . '=1&return=' . $returnUri)
					?>">
						<i class="icon-signout"></i> Sign out
					</a>
				</div>
				<span class="divider-vertical pull-right"></span>

				<div class="welcome pull-right">
					<i class="icon-user"></i>
					Welcome <?php echo $userName ?>
				</div>
			</div>
		</div>
	</div>
</header>
