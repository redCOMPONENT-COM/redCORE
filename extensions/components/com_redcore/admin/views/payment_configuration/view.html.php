<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Payment Configuration View
 *
 * @package     Redcore.Admin
 * @subpackage  Views
 * @since       1.2
 */
class RedcoreViewPayment_Configuration extends RedcoreHelpersView
{
	/**
	 * @var JForm
	 */
	protected $form;

	/**
	 * @var object
	 */
	protected $item;

	/**
	 * Name of the payment plugin
	 *
	 * @var  string
	 */
	public $paymentName = '';

	/**
	 * Display method
	 *
	 * @param   string  $tpl  The template name
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel();
		$model->getState();
		$model->setState('payment_name', JFactory::getApplication()->input->getString('payment_name', ''));
		$model->setState('process_params', '1');
		$this->form	= $model->getForm();
		$this->item	= $model->getItem();

		$this->paymentName = $model->paymentName;

		parent::display($tpl);
	}

	/**
	 * Get the view title.
	 *
	 * @return string  The view title.
	 */
	public function getTitle()
	{
		return JText::_('COM_REDCORE_PAYMENT_CONFIGURATION_FORM_TITLE');
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return RToolbar
	 */
	public function getToolbar()
	{
		$group = new RToolbarButtonGroup;
		$user = JFactory::getUser();

		if ($user->authorise('core.admin', 'com_redcore'))
		{
			$save = RToolbarBuilder::createSaveButton('payment_configuration.apply');
			$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('payment_configuration.save');
			$saveAndNew = RToolbarBuilder::createSaveAndNewButton('payment_configuration.save2new');

			$group->addButton($save)
				->addButton($saveAndClose)
				->addButton($saveAndNew);
		}

		if (empty($this->item->id))
		{
			$cancel = RToolbarBuilder::createCancelButton('payment_configuration.cancel');
		}

		else
		{
			$cancel = RToolbarBuilder::createCloseButton('payment_configuration.cancel');
		}

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
