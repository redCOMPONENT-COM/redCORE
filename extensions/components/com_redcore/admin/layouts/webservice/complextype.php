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

$operation = !empty($displayData['options']['operation']) ? $displayData['options']['operation'] : 'read-list';
$fieldList = !empty($displayData['options']['fieldList']) ? $displayData['options']['fieldList'] : array();
$form = !empty($displayData['options']['form']) ? $displayData['options']['form'] : null;
$readListValues = $operation == 'read-list' ? ',isFilterField,isSearchableField' : '';
$tabActive = !empty($displayData['options']['tabActive']) ? $displayData['options']['tabActive'] : null;
?>
<div role="tabpanel" class="tab-pane <?php echo $tabActive; ?>" id="operationTab<?php echo $operation; ?>">
	<?php
	if (substr($operation, 0, strlen('type-')) === 'type-') :
		echo RLayoutHelper::render(
			'webservice.fields',
			array(
				'view' => $view,
				'options' => array(
					'operation' => $operation,
					'form'      => $form,
					'tabActive' => $tabActive,
					'fieldList' => array('defaultValue', 'isRequiredField', 'isPrimaryField'),
				)
			)
		);

		$firstContentActive = false;
	endif;
	?>
</div>
