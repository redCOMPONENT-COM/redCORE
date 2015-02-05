<?php
/**
 * @package     Redcore
 * @subpackage  Toolbar
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Represents a version button.
 *
 * @package     Redcore
 * @subpackage  Toolbar
 * @since       1.4.10
 */
class RToolbarButtonVersion extends RToolbarButton
{
	/**
	 * The type alias attribute.
	 *
	 * @var  string
	 */
	protected $typeAlias;

	/**
	 * The item Id attribute
	 *
	 * @var  int
	 */
	protected $itemId;

	/**
	 * The height of modal attribute.
	 *
	 * @var  int
	 */
	protected $height;

	/**
	 * The width of modal attribute.
	 *
	 * @var  int
	 */
	protected $width;

	/**
	 * The text of button.
	 *
	 * @var  string
	 */
	protected $text;

	/**
	 * The class of button.
	 *
	 * @var  string
	 */
	protected $class;

	/**
	 * The class of icon.
	 *
	 * @var  string
	 */
	protected $iconClass;

	/**
	 * Construction
	 *
	 * @param   string  $typeAlias  Type alias
	 * @param   int     $itemId     item ID
	 * @param   int     $height     Height of modal
	 * @param   int     $width      Width of modal
	 * @param   string  $text       Text of button
	 * @param   string  $class      Class of button
	 * @param   string  $iconClass  Class of icon
	 */
	public function __construct($typeAlias, $itemId, $height = 800, $width = 500, $text = '', $class = '', $iconClass = '')
	{
		parent::__construct($text, '', '');

		$this->typeAlias = $typeAlias;
		$this->itemId    = (int) $itemId;
		$this->height    = (int) $height;
		$this->width     = (int) $width;
		$this->text      = JText::_('JTOOLBAR_VERSION');
		$this->class     = $class;
		$this->iconClass = 'icon-archive';

		if (!empty($iconClass))
		{
			$this->iconClass = $iconClass;
		}

		if (!empty($text))
		{
			$this->text = JText::_($text);
		}
	}

	/**
	 * Render the button.
	 *
	 * @param   boolean  $isOption  Is menu option?
	 *
	 * @return  string  The rendered button.
	 */
	public function render($isOption = false)
	{
		$contentTypeTable = JTable::getInstance('Contenttype');
		$typeId = $contentTypeTable->getTypeId($this->typeAlias);

		// Options array for JLayout
		$options = array(
			'title'     => $this->title,
			'height'    => $this->height,
			'width'     => $this->width,
			'itemId'    => $this->itemId,
			'typeId'    => $typeId,
			'typeAlias' => $this->typeAlias,
			'class'     => $this->class,
			'iconClass' => $this->iconClass
		);

		return RLayoutHelper::render('toolbar.button.versions', $options);
	}
}
