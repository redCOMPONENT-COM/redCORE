<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Payments View
 *
 * @package     Redcore.Admin
 * @subpackage  Views
 * @since       1.5
 */
class RedcoreViewPayments extends RedcoreHelpersView
{
	/**
	 * @var  array
	 */
	protected $items;

	/**
	 * @var  object
	 */
	protected $state;

	/**
	 * @var  JPagination
	 */
	public $pagination;

	/**
	 * @var  JForm
	 */
	public $filterForm;

	/**
	 * @var array
	 */
	public $activeFilters;

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

		$this->items         = $model->getItems();
		$this->state         = $model->getState();
		$this->pagination    = $model->getPagination();
		$this->activeFilters = $model->getActiveFilters();
		$this->filterForm    = $model->getForm();

		parent::display($tpl);
	}

	/**
	 * Get the view title.
	 *
	 * @return  string  The view title.
	 */
	public function getTitle()
	{
		return JText::_('COM_REDCORE_PAYMENTS');
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$user = JFactory::getUser();

		$firstGroup  = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup('', true, 'icon-cogs', JText::_('COM_REDCORE_PAYMENT_OPTIONS'));
		$thirdGroup  = new RToolbarButtonGroup;

		if ($user->authorise('core.admin', 'com_redcore'))
		{
			$button = new RToolbarButtonStandard('COM_REDCORE_PAYMENT_INSPECT_PAYMENT', 'payment.edit', '', 'icon-edit');
			$firstGroup->addButton($button);

			$button = new RToolbarButtonStandard('COM_REDCORE_PAYMENT_CHECK_PAYMENT', 'payment.checkPayment', '', 'icon-refresh');
			$secondGroup->addButton($button);

			$button = new RToolbarButtonStandard('COM_REDCORE_PAYMENT_CAPTURE_PAYMENT', 'payment.capturePayment', '', 'icon-money');
			$secondGroup->addButton($button);

			$button = new RToolbarButtonStandard('COM_REDCORE_PAYMENT_REFUND_PAYMENT', 'payment.refundPayment', '', 'icon-money');
			$secondGroup->addButton($button);

			$button = new RToolbarButtonStandard('COM_REDCORE_PAYMENT_DELETE_PAYMENT', 'payment.deletePayment', '', 'icon-money');
			$secondGroup->addButton($button);

			$delete = RToolbarBuilder::createDeleteButton('payments.delete');
			$thirdGroup->addButton($delete);
		}

		$toolbar = new RToolbar;
		$toolbar->addGroup($firstGroup)
			->addGroup($secondGroup)
			->addGroup($thirdGroup);

		return $toolbar;
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   string  $section    The section.
	 * @param   mixed   $assetName  The asset name.
	 *
	 * @return  Registry
	 */
	public function getActions($section = 'component', $assetName = 'com_redcore')
	{
		$user    = JFactory::getUser();
		$result  = new Registry;
		$actions = JAccess::getActionsFromFile('com_redcore', $section) ?: array();

		foreach ($actions as $action)
		{
			$result->set($action->name,	$user->authorise($action->name, $assetName));
		}

		return $result;
	}
}
