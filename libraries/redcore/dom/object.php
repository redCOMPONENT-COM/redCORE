<?php
/**
 * @package     Redbooking.Libraries
 * @subpackage  Domobject
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Represents a DOM object
 *
 * @package     Redbooking.Libraries
 * @subpackage  Domobject
 * @since       1.0
 */
class RDomObject
{
	/**
	 * Array of object attributes
	 *
	 * @var  array
	 */
	private $attribs = array();

	/**
	 * Array of children objects
	 *
	 * @var  array
	 */
	private $children = array();

	/**
	 * CSS Identifier of the modal window
	 *
	 * @var  string
	 */
	private $id = null;

	/**
	 * An array of modal instances.
	 *
	 * @var  REntityModal[]
	 */
	protected static $instances = array();

	/**
	 * Tag of the object
	 *
	 * @var  string
	 */
	private $tag = 'div';

	/**
	 * Constructor
	 *
	 * @param   array    $options  Optional array with settings
	 * @param   integer  $id       The currency id.
	 */
	public function __construct($options = array(), $id = null)
	{
		if ($id === null)
		{
			$id = static::generateId();
		}

		$this->id = $id;

		$this->initOptions($options);
	}

	/**
	 * Add one attribute to the object
	 *
	 * @param   string   $attribute  Name of the attribute
	 * @param   mixed    $value      String or array of strings
	 * @param   boolean  $reset      Reset the content of the attribute ?
	 *
	 * @return  void
	 */
	public function addAttribute($attribute, $value, $reset = false)
	{
		if (!isset($this->attribs[$attribute]))
		{
			$this->attribs[$attribute] = array();
		}

		if (is_array($value))
		{
			array_merge($this->attribs[$attribute], $value);
		}
		else
		{
			$this->attribs[$attribute][] = $value;
		}
	}

	/**
	 * Add a children object
	 *
	 * @param   RDomObject  $child  Child object
	 *
	 * @return  void
	 */
	public function addChild($child)
	{
		$this->children[$child->id] = $child;
	}

	/**
	 * Add a class to the object
	 *
	 * @param   string  $class  Class to add
	 *
	 * @return  void
	 */
	public function addClass($class)
	{
		$this->addClasses('class', array($class));
	}

	/**
	 * Add an array of classes to the object
	 *
	 * @param   array  $classes  Array of classes to add
	 *
	 * @return  void
	 */
	public function addClasses($classes)
	{
		$this->addAttribute('class', $classes);
	}

	/**
	 * Generate an object id
	 *
	 * @return  string
	 */
	public static function generateId()
	{
		return (string) uniqid();
	}

	/**
	 * Get 1 attribute value
	 *
	 * @param   string  $attribute  Name of the attribute to get
	 *
	 * @return  string
	 */
	public function getAttribute($attribute)
	{
		$value = null;

		if (isset($this->attribs[$attribute]))
		{
			$value = implode(' ', $this->attribs[$attribute]);
		}

		return $value;
	}

	/**
	 * Get an instance or create it.
	 *
	 * @param   array    $options  Optional array with settings
	 * @param   integer  $id       The currency id.
	 *
	 * @return  RDomObject
	 */
	public static function getInstance($options = array(), $id = null)
	{
		if ($id === null)
		{
			$id = static::generateId();
		}

		if (empty(static::$instances[$id]))
		{
			static::$instances[$id] = new static($options, $id);
		}

		return static::$instances[$id];
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
		// Get the object attributes
		if (isset($options['attribs']))
		{
			foreach ($options['attribs'] as $attribute => $value)
			{
				$this->attribs[$attribute] = array($value);
			}

			unset($options['attribs']);
		}
	}

	/**
	 * Remove one attribute
	 *
	 * @param   string  $attribute  Name of the attribute to render
	 *
	 * @return  string
	 */
	public function removeAttribute($attribute)
	{
		if (isset($this->attribs[$attribute]))
		{
			unset($this->attribs[$attribute]);
		}
	}

	/**
	 * Remove one child
	 *
	 * @param   string  $childId  Id of the children to remove
	 *
	 * @return  string
	 */
	public function removeChild($childId)
	{
		if (isset($this->children[$childId]))
		{
			unset($this->children[$childId]);
		}
	}

	/**
	 * Render this DOM object
	 *
	 * @return  string  The output
	 */
	public function render()
	{
		$output = null;

		if (!empty($this->tag))
		{
			$output .= '<' . $this->tag . ' ' . $this->renderAttributes() . '>';
			$output .= '</' . $this->tag . '>';
		}

		return $output;
	}

	/**
	 * Render one single attribute of the object
	 *
	 * @param   string  $attribute  Name of the attribute to render
	 *
	 * @return  string
	 */
	public function renderAttribute($attribute)
	{
		$output = null;

		if (isset($this->attribs[$attribute]))
		{
			$output = $attribute . '="' . implode(' ', $this->attribs[$attribute]) . '"';
		}

		return $output;
	}

	/**
	 * Render all the attributes of the object
	 *
	 * @return  string
	 */
	public function renderAttributes()
	{
		$output = null;

		if (!empty($this->attribs))
		{
			foreach ($this->attribs as $attribute => $values)
			{
				if ($output !== null)
				{
					$output .= ' ';
				}

				$output .= $this->renderAttribute($attribute);
			}
		}

		return $output;
	}
}
