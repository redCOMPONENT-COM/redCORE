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
abstract class RApiHalDocumentBase
{
	/**
	 * @var SimpleXMLElement
	 */
	protected $xml;

	/**
	 * Sets XML attributes for RApiHalDocumentLink
	 * 
	 * @param   SimpleXMLElement     $xml   XML document
	 * @param   RApiHalDocumentLink  $link  Link element
	 *
	 * @return RApiHalDocumentBase
	 */
	public function setXMLAttributes(SimpleXMLElement $xml, RApiHalDocumentLink $link)
	{
		$xml->addAttribute('href', $link->getHref());

		if ($link->getRel() && $link->getRel() !== 'self')
		{
			$xml->addAttribute('rel', $link->getRel());
		}

		if ($link->getName())
		{
			$xml->addAttribute('name', $link->getName());
		}

		if ($link->getTitle())
		{
			$xml->addAttribute('title', $link->getTitle());
		}

		if ($link->getHreflang())
		{
			$xml->addAttribute('hreflang', $link->getHreflang());
		}

		return $this;
	}
}
