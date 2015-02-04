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
	 * Construction
	 *
	 * @param   string  $typeAlias  Type alias
	 * @param   int     $itemId     item ID
	 * @param   int     $height     Height of modal
	 * @param   int     $width      Width of modal
	 * @param   string  $text       Text of button
	 */
	public function __construct($typeAlias, $itemId, $height = 800, $width = 500, $text = 'JTOOLBAR_VERSIONS')
	{
		parent::__construct($text, '', '');

		$this->typeAlias = $typeAlias;
		$this->itemId    = $itemId;
		$this->height    = $height;
		$this->width     = $width;
		$this->text      = $text;
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
		JHtml::_('behavior.modal', 'a.modal_jform_contenthistory');

		$contentTypeTable = JTable::getInstance('Contenttype');
		$typeId = $contentTypeTable->getTypeId($this->typeAlias);

		// Options array for JLayout
		$options = array(
			'title'     => JText::_($this->title),
			'height'    => $this->height,
			'width'     => $this->width,
			'itemId'    => $this->itemId,
			'typeId'    => $typeId,
			'typeAlias' => $this->typeAlias,
		);

		return RLayoutHelper::render('toolbar.button.versions', $options);
	}
}
