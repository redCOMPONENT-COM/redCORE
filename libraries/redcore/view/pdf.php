<?php
/**
 * @package     Redcore
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

require_once JPATH_REDCORE . '/tcpdf/tcpdf_import.php';

/**
 * A pdf view.
 *
 * @package     Redcore
 * @subpackage  View
 * @since       1.0
 */
abstract class RViewPdf extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @throws  RuntimeException
	 */
	public function display($tpl = null)
	{
		$result = $this->loadTemplate($tpl);

		if ($result instanceof Exception)
		{
			return $result;
		}

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// Prepare the pdf
		$this->preparePdf($pdf, $result);

		// Render the pdf
		$this->renderPdf($pdf);

		JFactory::getApplication()->close();

		return true;
	}

	/**
	 * Get the csv file name.
	 *
	 * @return  string  The file name.
	 */
	protected function getFileName()
	{
		$date = md5(date('Y-m-d-h-i-s'));
		$fileName = $this->getName() . '_' . $date;

		return $fileName;
	}

	/**
	 * Prepare the pdf.
	 *
	 * @param   TCPDF   $pdf   The pdf
	 * @param   string  $html  The html data
	 *
	 * @return  void
	 */
	protected function preparePdf(TCPDF $pdf, $html)
	{
		$pdf->setHeaderData('', 0, '', '', array(0, 0, 0), array(255, 255, 255));
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// Add a new page
		$pdf->AddPage();

		// Write the template output
		$pdf->writeHTML($html, true, false, true, false, '');
	}

	/**
	 * Render the pdf. Override it for custom rendering or additionnal processing.
	 *
	 * @param   TCPDF  $pdf  The pdf
	 *
	 * @return  void
	 */
	protected function renderPdf(TCPDF $pdf)
	{
		$pdf->Output(sprintf('%s.pdf', $this->getFileName()), 'I');
	}
}
