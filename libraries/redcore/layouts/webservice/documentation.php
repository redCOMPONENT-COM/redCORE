<?php
/**
 * @package     Redcore.Webservice
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JHtml::_('rbootstrap.tooltip');

$view = !empty($displayData['view']) ? $displayData['view'] : null;
$xml = !empty($displayData['options']['xml']) ? $displayData['options']['xml'] : array();
$date   = new JDate;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1\" />
	<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" />
</head>
<body>
<div class="container-fluid">
	<?php if (empty($xml)) : ?>
		<h1><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_NONE'); ?></h1>
	<?php else : ?>
		<h1><?php echo $xml->name; ?> (<?php echo JText::_('JVERSION'); ?> <?php echo $xml->config->version; ?>)</h1>
		<div class="well">
			<?php if (isset($xml->author)) : ?>
				<strong><?php echo JText::_('JAUTHOR'); ?></strong>: <?php echo (string) $xml->author; ?><br />
			<?php endif; ?>
			<?php if (isset($xml->copyright)) : ?>
				<strong><?php echo JText::_('LIB_REDCORE_COPYRIGHT'); ?></strong>: <?php echo (string) $xml->copyright; ?><br />
			<?php endif; ?>
			<strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_GENERATED'); ?></strong>: <?php echo $date->toRFC822(); ?><br />
			<strong>
				<?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_ACCESS_OPTION'); ?>:
			</strong>
			<?php echo $xml->config->name; ?> (com_<?php echo $xml->config->name; ?>)<br />
			<strong>
				<?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_CLIENT'); ?>:
			</strong>
			<?php echo ucfirst($view->client); ?><br />
			<strong><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_SUPPORTED_FORMATS'); ?></strong>
			: json (<?php echo JText::_('JDEFAULT'); ?>), xml<br />
		</div>
			<?php if (isset($xml->description)) : ?>
			<div class="well">
				<h4><?php echo JText::_('JGLOBAL_DESCRIPTION'); ?></h4>
				<p><?php echo (string) $xml->description; ?></p>
			</div>
			<?php endif; ?>

		<h2><?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_ALLOWED_OPERATIONS'); ?></h2>
		<p>
			<?php echo JText::_('LIB_REDCORE_API_HAL_WEBSERVICE_DOCUMENTATION_ALLOWED_OPERATIONS_DESC'); ?>
		</p>
		<?php foreach ($xml->operations as $operations) : ?>
			<?php foreach ($operations as $operationName => $operation) : ?>
					<?php if ($operationName == 'documentation') :
						continue;
					elseif ($operationName == 'read') :
						if (isset($xml->operations->read->list)) : ?>
							<a name="<?php echo $operationName . 'list'; ?>"></a>
							<?php echo RLayoutHelper::render(
								'webservice.documentationoperation',
								array(
									'view' => $view,
									'options' => array (
										'xml' => $xml,
										'operationXml' => $operation->list,
										'operationName' => $operationName . ' ' . 'list',
									)
								)
							);?>
							<br />
						<?php endif;

						if (isset($xml->operations->read->item)) : ?>
							<a name="<?php echo $operationName . 'item'; ?>"></a>
							<?php echo RLayoutHelper::render(
								'webservice.documentationoperation',
								array(
									'view' => $view,
									'options' => array (
										'xml' => $xml,
										'operationXml' => $operation->item,
										'operationName' => $operationName . ' ' . 'item',
									)
								)
							);?>
							<br />
						<?php endif;
					elseif ($operationName == 'task') :
						foreach ($operation as $taskName => $task) : ?>
							<a name="<?php echo $operationName . $taskName; ?>"></a>
							<?php echo RLayoutHelper::render(
								'webservice.documentationoperation',
								array(
									'view' => $view,
									'options' => array (
										'xml' => $xml,
										'operationXml' => $task,
										'operationName' => $operationName . ' ' . $taskName,
									)
								)
							);?>
							<br />
						<?php endforeach;
					else : ?>
						<a name="<?php echo $operationName; ?>"></a>
							<?php echo RLayoutHelper::render(
								'webservice.documentationoperation',
								array(
									'view' => $view,
									'options' => array (
										'xml' => $xml,
										'operationXml' => $operation,
										'operationName' => $operationName,
									)
								)
							);?>
						<br />
					<?php endif; ?>
			<?php endforeach; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>
</body>
</html>
