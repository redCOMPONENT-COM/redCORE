<?php
/**
 * @package     Redcore
 * @subpackage  Menu
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Represents a tree.
 * The tree is only composed of the root node
 * (because the root node, knows his children, etc..)
 *
 * @package     Redcore
 * @subpackage  Menu
 * @since       1.0
 */
class RMenuTree
{
	/**
	 * The root node.
	 *
	 * @var  RMenuNode
	 */
	protected $rootNode;

	/**
	 * The name of the tree (it has the same name as the root node).
	 *
	 * @var  string
	 */
	protected $name;

	/**
	 * Constructor.
	 *
	 * @param   RMenuNode  $root  The root node.
	 */
	public function __construct(RMenuNode $root)
	{
		$this->rootNode = $root;
		$this->name = $root->getName();
	}

	/**
	 * Get the tree name.
	 *
	 * @return  string  The tree name.
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set the root node.
	 *
	 * @param   RMenuNode  $root  The root node.
	 *
	 * @return  RMenuTree  This method is chainable.
	 */
	public function setRootNode(RMenuNode $root)
	{
		$this->rootNode = $root;

		return $this;
	}

	/**
	 * Get the active node in this tree, if any.
	 *
	 * @return  RMenuNode|boolean  The active node or false if no active nodes.
	 */
	public function getActiveNode()
	{
		if ($this->rootNode->isActive())
		{
			return $this->rootNode;
		}

		return $this->rootNode->getActiveChild();
	}

	/**
	 * Get the root node.
	 *
	 * @return  RMenuNode
	 */
	public function getRootNode()
	{
		return $this->rootNode;
	}
}
