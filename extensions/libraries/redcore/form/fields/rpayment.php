<?php
/**
 * @package     Redcore
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field to load a list of payments
 *
 * @package     Redcore
 * @subpackage  Field
 * @since       1.5
 */
class JFormFieldRpayment extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	protected $type = 'Rpayment';

	/**
	 * Cached array of the items.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected static $options = array();

	/**
	 * Translate options labels ?
	 *
	 * @var  boolean
	 * @since  1.0
	 */
	protected $translate = false;

	/**
	 * Method to get the options to populate list
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.0
	 */
	protected function getOptions()
	{
		JPluginHelper::importPlugin('redpayment');
		$app = JFactory::getApplication();

		$extensionName = !empty($this->element['extensionName']) ? $this->element['extensionName'] : $app->input->get->getString('option', '');
		$ownerName = !empty($this->element['ownerName']) ? $this->element['ownerName'] : '';
		$payments = array();

		$app->triggerEvent('onRedpaymentListPayments', array($extensionName, $ownerName, &$payments));

		$options = array_merge(parent::getOptions(), $payments);

		return $options;
	}

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$app = JFactory::getApplication();
		$listType = !empty($this->element['listType']) ?
			$this->element['listType'] : RBootstrap::getConfig('payment_list_payments_type', 'radio');

		$extensionName = !empty($this->element['extensionName']) ? $this->element['extensionName'] : $app->input->get->getString('option', '');
		$ownerName = !empty($this->element['ownerName']) ? $this->element['ownerName'] : '';

		// Get the field options.
		$options = $this->getOptions();

		return RLayoutHelper::render(
			'redpayment.list.' . strtolower($listType),
			array(
				'options' => array(
					'payments' => $options,
					'extensionName' => $extensionName,
					'ownerName' => $ownerName,
					'name' => $this->name,
					'value' => $this->value,
					'id' => $this->id,
					'attributes' => $this->getAttributes(),
					'selectSingleOption' => true,
				)
			)
		);
	}

	/**
	 * Method to get the field attributes
	 *
	 * @return  string  The field input attributes.
	 */
	protected function getAttributes()
	{
		$attr = '';

		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1'|| (string) $this->disabled == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

		return $attr;
	}
}
