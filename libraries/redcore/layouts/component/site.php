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

$app = JFactory::getApplication();
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

$input->set('redcore', true);

// Load bootstrap + fontawesome
JHtml::_('rbootstrap.framework');

RHelperAsset::load('component.js', 'redcore');
RHelperAsset::load('component.min.css', 'redcore');

// Load a custom CSS option for this component if exists
if ($comOption = $input->get('option', null))
{
	RHelperAsset::load($comOption . '.css', $comOption);
}

// For Joomla! 2.5 we will add bootstrap alert messages
if (version_compare(JVERSION, '3.0', '<') && JFactory::getApplication()->isAdmin())
{
	// Require the message renderer as it doesn't respect the naming convention.
	$messageRendererPath = JPATH_LIBRARIES . '/redcore/joomla/document/renderer/message.php';

	if (file_exists($messageRendererPath))
	{
		require_once $messageRendererPath;
	}
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

if (method_exists($view, 'getToolbar'))
{
	$toolbar = $view->getToolbar();
}
else
{
	$toolbar = null;
}

// Get the view template.
$tpl = $data['tpl'];

// Get the view render.
$result = $view->loadTemplate($tpl);

if ($result instanceof Exception)
{
	return $result;
}
?>
<div class="redcore">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<?php if ($toolbar instanceof RToolbar) : ?>
					<div class="row-fluid">
						<?php echo $toolbar->render() ?>
					</div>
				<?php endif; ?>

				<?php echo $result; ?>
			</div>
		</div>
	</div>
</div>
