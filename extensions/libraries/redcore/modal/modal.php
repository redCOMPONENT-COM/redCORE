<?php
/**
 * @package     Redbooking.Libraries
 * @subpackage  Modal
 *
 * @copyright   Copyright (C) 2008 - 2021 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Represents a modal window
 *
 * @package     Redbooking.Libraries
 * @subpackage  Modal
 * @since       1.0
 */
final class RModal extends RDomObject
{
	/**
	 * Header code
	 *
	 * @var  REntityDomobject
	 */
	private $header = null;

	/**
	 * Body of the modal
	 *
	 * @var  REntityDomobject
	 */
	private $body = null;

	/**
	 * Footer of the modal
	 *
	 * @var  REntityDomobject
	 */
	private $footer = null;

	/**
	 * Aditional parameters
	 *
	 * @var  JRegistry
	 */
	public $params = null;

	/**
	 * Get the header content
	 *
	 * @return  string
	 */
	public function getHeader()
	{
		return $this->header;
	}

	/**
	 * Initialise received options
	 *
	 * @param   array  $options  Options of the object
	 *
	 * @return  array
	 */
	public function initOptions($options)
	{
		$params = null;

		if (isset($options['params']))
		{
			$params = $options['params'];
			unset($options['params']);
		}

		$this->setParams($params);

		parent::initOptions($options);
	}

	/**
	 * Set the aditional parameters
	 *
	 * @param   mixed  $params  Array/JRegistry object with aditional parameters
	 *
	 * @return  void
	 */
	public function setParams($params)
	{
		if (is_array($params))
		{
			$this->params = new JRegistry($params);
		}
		elseif ($params instanceof JRegistry)
		{
			$this->params = $params;
		}
		else
		{
			$this->params = new JRegistry;
		}
	}

	/**
	 * Set the body content
	 *
	 * @param   string  $html  HTML content
	 *
	 * @return  void
	 */
	public function setBody($html)
	{
		$this->body = $html;
	}

	/**
	 * Set the footer content
	 *
	 * @param   string  $html  HTML content
	 *
	 * @return  void
	 */
	public function setFooter($html)
	{
		$this->footer = $html;
	}

	/**
	 * Set the header content
	 *
	 * @param   string  $html  HTML content
	 *
	 * @return  void
	 */
	public function setHeader($html)
	{
		$this->header = $html;
	}
}
