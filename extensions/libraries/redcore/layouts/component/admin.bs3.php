<?php
/**
 * @package     Redcore
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

$data = $displayData;

$app   = JFactory::getApplication();
$input = $app->input;

/**
 * Handle raw format
 */
$format = $input->getString('format');

if ('raw' === $format)
{
	/** @var RView $view */
	$view = $data['view'];

	if (!$view instanceof RViewBase)
	{
		throw new InvalidArgumentException(
			sprintf(
				'Invalid view %s specified for the component layout',
				get_class($view)
			)
		);
	}

	$toolbar = $view->getToolbar();

	// Get the view template.
	$tpl = $data['tpl'];

	// Get the view render.
	return $view->loadTemplate($tpl);
}

$templateComponent = 'component' === $input->get('tmpl');
$input->set('tmpl', 'component');
$input->set('redcore', true);

// Load bootstrap + fontawesome
RHtmlMedia::loadFrameworkCss();
RHtmlMedia::loadFrameworkJs();
RHelperAsset::load('component.min.js', 'redcore');

// Load a custom CSS option for this component if exists
if ($comOption = $input->get('option', null))
{
	RHelperAsset::load($comOption . '.css', $comOption);
}

// For Joomla! 2.5 we will add bootstrap alert messages
if (version_compare(JVERSION, '3.0', '<') && JFactory::getApplication()->isAdmin())
{
	// Require the message renderer as it doesn't respect the naming convention.
	$messageRendererPath = JPATH_LIBRARIES . '/redcore/joomla/document/renderer/html/message.php';

	if (file_exists($messageRendererPath))
	{
		require_once $messageRendererPath;
	}
}

// Do we have to display the sidebar ?
$displaySidebar = false;

if (isset($data['sidebar_display']))
{
	$displaySidebar = (bool) $data['sidebar_display'];
}

$sidebarLayout = '';

// The sidebar layout name.
if ($displaySidebar)
{
	if (!isset($data['sidebar_layout']))
	{
		throw new InvalidArgumentException('No sidebar layout specified in the component layout.');
	}

	$sidebarLayout = $data['sidebar_layout'];
}

$sidebarData = array();

if (isset($data['sidebar_data']))
{
	$sidebarData = $data['sidebar_data'];
}

// Do we have to display the topbar ?
$displayTopbar = false;

if (isset($data['topbar_display']))
{
	$displayTopbar = (bool) $data['topbar_display'];
}

$topbarLayout = '';

// The topbar layout name.
if ($displayTopbar)
{
	if (!isset($data['topbar_layout']))
	{
		throw new InvalidArgumentException('No topbar layout specified in the component layout.');
	}

	$topbarLayout = $data['topbar_layout'];
}

$topbarData = array();

if (isset($displayTopbar))
{
	$topbarData = $data;
}

// The view to render.
if (!isset($data['view']))
{
	throw new InvalidArgumentException('No view specified in the component layout.');
}

/** @var RView $view */
$view = $data['view'];

if (!$view instanceof RViewBase)
{
	throw new InvalidArgumentException(
		sprintf(
			'Invalid view %s specified for the component layout',
			get_class($view)
		)
	);
}

$toolbar = $view->getToolbar();

// Get the view template.
$tpl = $data['tpl'];

// Get the view render.
$result = $view->loadTemplate($tpl);

if ($result instanceof Exception)
{
	return $result;
}
?>
<script type="text/javascript">
(function ($) {
	$(document).ready(function () {

		if ($('.message-sys').length) {
			var messageContainer = $('#system-message-container');

			// No messages found. Create an empty div
			if (!messageContainer.length) {
				messageContainer = $("<div>", {id: "system-message-container"});
			}
			$('.message-sys').append(messageContainer);
		}

		<?php if ($input->getBool('disable_topbar') || $input->getBool('hidemainmenu'))
		:
	?>
		jQuery('.topbar').addClass('opacity-70');
		jQuery('.topbar button').prop('disabled', true);
		jQuery('.topbar a').attr('disabled', true).attr('href', '#').addClass('disabled');
		<?php endif; ?>

		<?php if ($input->getBool('disable_sidebar') || $input->getBool('hidemainmenu'))
		:
	?>
		jQuery('.sidebar').addClass('opacity-70');
		jQuery('.sidebar button').prop('disabled', true);
		jQuery('.sidebar a').attr('disabled', true).attr('href', '#').addClass('disabled');
		<?php endif; ?>
	});
})(jQuery);
</script>
<?php if ($view->getLayout() === 'modal')
:
	?>
	<div class="row redcore">
		<section id="component">
			<div class="row-fluid message-sys"></div>
			<div class="container-fluid">
				<?php echo $result ?>
			</div>
		</section>
	</div>
<?php elseif ($templateComponent)
:
	?>
	<div class="container-fluid redcore">
		<div class="col-md-12 content">
			<section id="component">
				<div class="row">
					<h1><?php echo $view->getTitle() ?></h1>
				</div>
				<div class="row-fluid message-sys"></div>
				<hr/>
				<div class="container-fluid">
					<?php echo $result ?>
				</div>
			</section>
		</div>
	</div>
<?php
else

:
	?>
	<div class="redcore">
		<div class="container-fluid">
			<?php if ($displayTopbar)
			:
	?>
				<?php echo RLayoutHelper::render($topbarLayout, $topbarData) ?>
			<?php endif; ?>
			<div class="row">
				<?php if ($displaySidebar)
				:
	?>
					<div class="col-md-2 col-sm-3 sidebar">
						<?php echo RLayoutHelper::render($sidebarLayout, $sidebarData) ?>
					</div>
					<div class="col-md-10 col-sm-9 content">
				<?php else

	:
	?>
					<div class="col-md-12 content">
				<?php endif; ?>
						<section id="component">
							<h1 class="sub-header"><?php echo $view->getTitle() ?></h1>
							<?php if ($toolbar instanceof RToolbar)
							:
	?>
								<div class="row-fluid">
									<?php echo $toolbar->render() ?>
								</div>
								<p></p>
							<?php endif; ?>
							<div class="row-fluid message-sys"></div>
							<div class="row-fluid">
								<?php echo $result ?>
							</div>
						</section>
					</div>
			</div>
		</div>
		<footer class="footer pagination-centered navbar-nav navbar-fixed-bottom hidden-xs">
			Copyright <?php echo date('Y') ?> redcomponent.com. All rights reserved.
		</footer>
	</div>
<?php endif;
