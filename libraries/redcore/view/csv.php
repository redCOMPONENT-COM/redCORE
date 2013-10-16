<?php
/**
 * @package     Redcore
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * A csv view working with a RModelList.
 *
 * @package     Redcore
 * @subpackage  View
 * @since       1.0
 */
abstract class RViewCsv extends JViewLegacy
{
	/**
	 * Get the columns for the csv file.
	 *
	 * @return  array  An associative array of column names as key and the title as value.
	 */
	abstract protected function getColumns();

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
		// Get the columns
		$columns = $this->getColumns();

		if (empty($columns))
		{
			throw new RuntimeException(
				sprintf(
					'Empty columns not allowed for the csv view %s',
					get_class($this)
				)
			);
		}

		/** @var RModelList $model */
		$model = $this->getModel();

		// Prepare the items
		$items = $model->getItems();
		$csvLines[0] = $columns;
		$i = 1;

		foreach ($items as $item)
		{
			$csvLines[$i] = array();

			foreach ($columns as $name => $title)
			{
				if (property_exists($item, $name))
				{
					$csvLines[$i][$name] = $item->$name;
				}
			}

			$i++;
		}

		// Get the file name
		$fileName = $this->getFileName();

		// Send the headers
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename=\"$fileName.csv\";");
		header("Content-Transfer-Encoding: binary");

		// Send the csv
		$stream = @fopen('php://output', 'w');

		if (!is_resource($stream))
		{
			throw new RuntimeException('Failed to open the output stream');
		}

		foreach ($csvLines as $line)
		{
			fputcsv($stream, $line);
		}

		fclose($stream);

		JFactory::getApplication()->close();
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
}
