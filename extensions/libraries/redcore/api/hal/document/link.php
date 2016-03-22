<?php
/**
 * @package     Redcore
 * @subpackage  Api
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * Object to represent a hypermedia link in HAL.
 *
 * @since  1.2
 */
class RApiHalDocumentLink extends RApiHalDocumentBase
{
	/**
	 * For labeling the destination of a link with a human-readable identifier.
	 * @var string
	 */
	protected $_title;

	/**
	 * For identifying how the target URI relates to the 'Subject Resource'.
	 * The Subject Resource is the closest parent Resource element.
	 *
	 * @var string
	 */
	protected $_rel;

	/**
	 * - <b>As a resource:</b>
	 *     Content embedded within a Resource element MAY be a full, partial, summary,
	 *     or incorrect representation of the content available at the target URI.
	 *     Applications which use HAL MAY clarify the integrity of specific embedded
	 *     content via the description of the relevant @rel value.
	 * - <b>As a link:</b>
	 *     This attribute MAY contain a URI template. Whether or not this is the case
	 *     SHOULD be indicated to clients by the @rel value.
	 *
	 * @var string
	 */
	protected $_href;

	/**
	 *
	 * For distinguishing between Resource and Link elements that share the
	 * same @rel value. The @name attribute SHOULD NOT be used exclusively
	 * for identifying elements within a HAL representation, it is intended
	 * only as a 'secondary key' to a given @rel value.
	 * @var string
	 */
	protected $_name;

	/**
	 * For indicating what the language of the result of dereference the
	 * link should be.
	 * @var string
	 */
	protected $_hreflang;

	/**
	 * Whether this link is "templated"
	 * @var boolean
	 * @link https://tools.ietf.org/html/rfc6570
	 */
	protected $_templated;

	/**
	 * Constructor.
	 *
	 * @param   string   $href       Href string
	 * @param   string   $rel        Rel
	 * @param   string   $title      Title
	 * @param   string   $name       Name
	 * @param   string   $hreflang   href Language
	 * @param   boolean  $templated  Is it templated
	 */
	public function __construct($href, $rel = 'self', $title = null, $name = null, $hreflang = null, $templated = false)
	{
		$this->setHref($href)
			->setRel($rel)
			->setName($name)
			->setTitle($title)
			->setHreflang($hreflang)
			->setTemplated($templated);
	}

	/**
	 * Gets Rel element
	 *
	 * @return string
	 */
	public function getRel ()
	{
		return $this->_rel;
	}

	/**
	 * Gets Href element
	 *
	 * @return string
	 */
	public function getHref ()
	{
		return $this->_href;
	}

	/**
	 * Gets Name element
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Gets Title element
	 *
	 * @return string
	 */
	public function getTitle ()
	{
		return $this->_title;
	}

	/**
	 * Gets Href lang element
	 *
	 * @return string
	 */
	public function getHreflang ()
	{
		return $this->_hreflang;
	}

	/**
	 * Gets templated element
	 *
	 * @return boolean
	 */
	public function getTemplated()
	{
		return $this->_templated;
	}

	/**
	 * Sets Rel element
	 *
	 * @param   string  $rel  Rel element
	 *
	 * @return RApiHalDocumentLink
	 */
	public function setRel ($rel)
	{
		$this->_rel = $rel;

		return $this;
	}

	/**
	 * Sets Rel element
	 *
	 * @param   string  $href  Href element
	 *
	 * @return RApiHalDocumentLink
	 */
	public function setHref ($href)
	{
		$this->_href = $href;

		return $this;
	}

	/**
	 * Sets Name element
	 *
	 * @param   string  $name  Name element
	 *
	 * @return RApiHalDocumentLink
	 */
	public function setName($name)
	{
		$this->_name = $name;

		return $this;
	}

	/**
	 * Sets Title element
	 *
	 * @param   string  $title  Title element
	 *
	 * @return RApiHalDocumentLink
	 */
	public function setTitle ($title)
	{
		$this->_title = $title;

		return $this;
	}

	/**
	 * Sets Href language element
	 *
	 * @param   string  $hreflang  Href language element
	 *
	 * @return RApiHalDocumentLink
	 */
	public function setHreflang ($hreflang)
	{
		$this->_hreflang = $hreflang;

		return $this;
	}

	/**
	 * Sets Templated element
	 *
	 * @param   boolean  $templated  Templated element
	 *
	 * @return RApiHalDocumentLink
	 */
	public function setTemplated($templated)
	{
		$this->_templated = $templated;

		return $this;
	}

	/**
	 * Converts current RApiHalDocumentLink object to Array
	 *
	 * @return array
	 */
	public function toArray()
	{
		$href = $this->getHref();

		if (empty($href))
		{
			return array();
		}

		$link = array('href' => $href);

		if ($this->getTitle())
		{
			$link['title'] = $this->getTitle();
		}

		if ($this->getTitle())
		{
			$link['title'] = $this->getTitle();
		}

		if ($this->getName())
		{
			$link['name'] = $this->getName();
		}

		if ($this->getHreflang())
		{
			$link['hreflang'] = $this->getHreflang();
		}

		if ($this->getTemplated())
		{
			$link['templated'] = $this->getTemplated();
		}

		return $link;
	}
}
