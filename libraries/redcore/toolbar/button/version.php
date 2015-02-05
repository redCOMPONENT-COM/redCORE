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
	 * Construction
	 *
	 * @param   string   $typeAlias  Type alias
	 * @param   int      $itemId     item ID
	 * @param   int      $height     Height of modal
	 * @param   int      $width      Width of modal
	 * @param   string   $text       The button text
	 * @param   string   $class      The button class
	 * @param   string   $iconClass  The icon class
	 * @param   boolean  $list       Is the button applying on a list ?
	 */
	public function __construct($typeAlias, $itemId, $height = 800, $width = 500, $text = '', $class = '', $iconClass = '', $list = true)
	{
		if (empty($text))
		{
			$text = JText::_('JTOOLBAR_VERSIONS');
		}

		parent::__construct($text, $iconClass, $class);

		$this->typeAlias = $typeAlias;
		$this->itemId    = (int) $itemId;
		$this->height    = (int) $height;
		$this->width     = (int) $width;
	}

	/**
	 * Get the item ID.
	 *
	 * @return  int  The item ID.
	 */
	public function getItemId()
	{
		return (int) $this->itemId;
	}

	/**
	 * Get the type alias.
	 *
	 * @return  int  The type alias.
	 */
	public function getTypeAlias()
	{
		return $this->typeAlias;
	}

	/**
	 * Get the width of modal.
	 *
	 * @return  int  The modal width.
	 */
	public function getModalWidth()
	{
		return (int) $this->width;
	}

	/**
	 * Get the height of modal.
	 *
	 * @return  int  The modal height.
	 */
	public function getModalHeight()
	{
		return (int) $this->height;
	}

	/**
	 * Get the type Id from content history.
	 *
	 * @return  int  The type Id.
	 */
	public function getTypeId()
	{
		$contentTypeTable = JTable::getInstance('Contenttype');
		$typeId = $contentTypeTable->getTypeId($this->typeAlias);

		return (int) $typeId;
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
		return RLayoutHelper::render(
			'toolbar.button.version',
			array(
				'button' => $this,
				'isOption' => $isOption
			)
		);
	}
}
