<?php
/**
 * @package     Redcore.Webservice
 * @subpackage  Layouts
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

$view = $displayData['view'];
$model = isset($displayData['model']) ? $displayData['model'] : $view->getModel();

$operation = !empty($displayData['options']['operation']) ? $displayData['options']['operation'] : 'read';
$fieldList = !empty($displayData['options']['fieldList']) ? $displayData['options']['fieldList'] : array();
$tabActive = isset($displayData['options']['tabActive']) ? $displayData['options']['tabActive'] : ' active in ';
$form = !empty($displayData['options']['form']) ? $displayData['options']['form'] : null;

?>
<div role="tabpanel" class="tab-pane <?php echo $tabActive; ?>" id="operationTab<?php echo $operation; ?>">
	<div class="ws-params ws-params-<?php echo $operation; ?>">
		<?php echo $this->sublayout('attributes', $displayData); ?>
		<fieldset class="ws-operation-configuration ws-use-operation-fieldset">
			<?php
			echo RLayoutHelper::render(
				'webservice.fields',
				array(
					'view' => $view,
					'model' => $model,
					'options' => array(
						'operation' => $operation,
						'fieldList' => $fieldList,
						'form'      => $form,
					)
				)
			);
			?>

			<?php echo RLayoutHelper::render(
				'webservice.resources',
				array(
					'view' => $view,
					'model' => $model,
					'options' => array(
						'operation' => $operation,
						'form'      => $form,
					)
				)
			); ?>
		</fieldset>
	</div>
</div>
