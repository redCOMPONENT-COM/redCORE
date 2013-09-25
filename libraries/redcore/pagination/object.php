<?php
/**
 * @package     Redcore
 * @subpackage  Pagination
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

/**
 * Pagination object representing a particular item in the pagination lists.
 *
 * @package     Redcore
 * @subpackage  Pagination
 * @since       1.0
 */
class RPaginationObject
{
	/**
	 * @var    string  The link text.
	 * @since  1.0
	 */
	public $text;

	/**
	 * @var    integer  The number of rows as a base offset.
	 * @since  1.0
	 */
	public $base;

	/**
	 * @var    string  The link URL.
	 * @since  1.0
	 */
	public $link;

	/**
	 * @var    integer  The prefix used for request variables.
	 * @since  1.6
	 */
	public $prefix;

	/**
	 * @var    boolean  Flag whether the object is the 'active' page
	 * @since  3.0
	 */
	public $active;

	/**
	 * Associated form
	 *
	 * @var  string
	 */
	public $formName = 'adminForm';

	/**
	 * Class constructor.
	 *
	 * @param   string   $text      The link text.
	 * @param   integer  $prefix    The prefix used for request variables.
	 * @param   integer  $base      The number of rows as a base offset.
	 * @param   string   $link      The link URL.
	 * @param   boolean  $active    Flag whether the object is the 'active' page
	 * @param   string   $formName  DOM form selector
	 *
	 * @since   1.0
	 */
	public function __construct($text, $prefix = '', $base = null, $link = null, $active = false, $formName = 'adminForm')
	{
		$this->text     = $text;
		$this->prefix   = $prefix;
		$this->base     = $base;
		$this->link     = $link;
		$this->active   = $active;
		$this->formName = $formName;
	}

	/**
	 * Set the name of the associated form
	 *
	 * @param   string  $formName  Name attribute of the form
	 *
	 * @return  void
	 */
	public function setFormName($formName)
	{
		$this->formName = $formName;
	}
}
