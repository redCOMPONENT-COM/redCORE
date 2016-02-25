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
 * Dashboard View.
 *
 * @package     Redcore.Admin
 * @subpackage  Views
 * @since       1.0
 */
class RedcoreViewPayment_Dashboard extends RedcoreHelpersView
{
	/**
	 * @var  object
	 */
	protected $state;

	/**
	 * @var  JForm
	 */
	public $filterForm;

	/**
	 * @var array
	 */
	public $activeFilters;

	/**
	 * @var  array
	 */
	public $paymentData;

	/**
	 * @var  array
	 */
	public $chartData;

	/**
	 * @var  string
	 */
	public $chartType;

	/**
	 * @var  string
	 */
	public $viewType;

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
		$this->state = $model->getState();
		$this->activeFilters = $model->getActiveFilters();
		$this->filterForm = $model->getForm();
		$filters = array();
		$filters['status'] = RApiPaymentStatus::getStatusCompleted();

		if ($filter = $this->state->get('filter.payment_name'))
		{
			$filters['payment_name'] = $filter;
		}

		if ($filter = $this->state->get('filter.extension_name'))
		{
			$filters['extension_name'] = $filter;
		}

		if ($startDate = $this->state->get('filter.start_date'))
		{
			$filters['start_date'] = $startDate;
		}

		if ($endDate = $this->state->get('filter.end_date'))
		{
			$filters['end_date'] = $endDate;
		}

		$this->viewType = $this->state->get('filter.dashboard_view_type');
		$this->chartType = $this->state->get('filter.chart_type');

		if (empty($this->viewType))
		{
			$this->viewType = RBootstrap::getConfig('payment_dashboard_view_type', 'payment_name');
			$this->state->set('filter.dashboard_view_type', $this->viewType);
		}

		if (empty($this->chartType))
		{
			$this->chartType = RBootstrap::getConfig('payment_chart_type', 'Line');
			$this->state->set('filter.chart_type', $this->chartType);
		}

		if ($this->viewType == 'status')
		{
			unset($filters['status']);
		}

		$this->paymentData['chart'] = RApiPaymentHelper::getChartData($filters, 7, $this->viewType);

		$filters['start_date'] = date('Y-01-01', strtotime('today -1 year'));
		$filters['end_date'] = date('Y-m-d', strtotime('today'));
		$filters['status'] = RApiPaymentStatus::getStatusCompleted();
		$this->paymentData['overall'] = RApiPaymentHelper::getChartData($filters, 7, 'all');
		$this->chartData = RApiPaymentHelper::prepareChartData($this->paymentData['chart'], $this->chartType);

		parent::display($tpl);
	}

	/**
	 * Get the view title.
	 *
	 * @return  string  The view title.
	 */
	public function getTitle()
	{
		return JText::_('COM_REDCORE_PAYMENT_DASHBOARD');
	}
}
