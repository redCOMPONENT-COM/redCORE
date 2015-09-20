<?php
/**
 * @package     Redcore
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * ApiDocumentHal class, provides an easy interface to parse and display HAL+JSON or HAL+XML output
 *
 * @package     Redcore
 * @subpackage  Document
 * @see         http://stateless.co/hal_specification.html
 * @since       1.2
 */
class RApiHalDocumentDocument extends JDocument
{
	/**
	 * Document name
	 *
	 * @var    string
	 * @since  1.2
	 */
	protected $name = 'joomla';

	/**
	 * Render all hrefs as absolute, relative is default
	 */
	protected $absoluteHrefs = false;

	/**
	 * Document format (xml or json)
	 */
	protected $documentFormat = false;

	/**
	 * @var    RApiHalHal  Main HAL object
	 * @since  1.2
	 */
	public $hal = null;

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Associative array of options
	 *
	 * @since  1.2
	 */
	public function __construct($options = array())
	{
		parent::__construct($options);

		$this->documentFormat = $options['documentFormat'];

		if (!in_array($this->documentFormat, array('xml', 'json')))
		{
			$this->documentFormat = 'json';
		}

		// Set default mime type.
		$this->_mime = 'application/hal+' . $this->documentFormat;

		// Set document type.
		$this->_type = 'hal+' . $this->documentFormat;

		// Set absolute/relative hrefs.
		$this->absoluteHrefs = isset($options['absoluteHrefs']) ? $options['absoluteHrefs'] : true;

		// Set token if needed
		$this->uriParams = isset($options['uriParams']) ? $options['uriParams'] : array();
	}

	/**
	 * Render the document.
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 *
	 * @return  string   The rendered data
	 *
	 * @since  1.2
	 */
	public function render($cache = false, $params = array())
	{
		// Get the HAL object from the buffer.
		/* @var $hal RApiHalDocumentResource */
		$hal = $this->getBuffer();

		// If required, change relative links to absolute.
		if (is_object($hal))
		{
			// Adjust hrefs in the _links object.
			$this->relToAbs($hal, $this->absoluteHrefs);
		}

		if ($this->documentFormat == 'xml')
		{
			$content = $hal->getXML()->asXML();
		}
		else
		{
			$content = (string) $hal;
		}

		$runtime = microtime(true) - $this->hal->startTime;
		$app = JFactory::getApplication();

		$app->setHeader('Status', $this->hal->statusCode . ' ' . $this->hal->statusText, true);
		$app->setHeader('Server', '', true);
		$app->setHeader('X-Runtime', $runtime, true);
		$app->setHeader('Access-Control-Allow-Origin', '*', true);
		$app->setHeader('Pragma', 'public', true);
		$app->setHeader('Expires', '0', true);
		$app->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
		$app->setHeader('Cache-Control', 'private', false);
		$app->setHeader('Content-type', $this->_mime . '; charset=UTF-8', true);
		$app->setHeader('Webservice-name', $this->hal->webserviceName, true);
		$app->setHeader('Webservice-version', $this->hal->webserviceVersion, true);
		$app->setHeader('Content-length', strlen($content), true);

		$app->sendHeaders();

		echo $content;
	}

	/**
	 * Returns the document name
	 *
	 * @return  string
	 *
	 * @since  1.2
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Sets HAL object to the document
	 *
	 * @param   RApiHalHal  $hal  Hal object
	 *
	 * @return   RApiHalDocumentDocument
	 *
	 * @since  1.2
	 */
	public function setHal($hal)
	{
		$this->hal = $hal;

		return $this;
	}

	/**
	 * Method to convert relative to absolute links.
	 *
	 * @param   RApiHalDocumentResource  $hal            Hal object which contains links (_links).
	 * @param   boolean                  $absoluteHrefs  Should we replace link Href with absolute.
	 *
	 * @return  void
	 */
	protected function relToAbs($hal, $absoluteHrefs)
	{
		if ($links = $hal->getLinks())
		{
			// Adjust hrefs in the _links object.
			/* @var $link RApiHalDocumentLink */
			foreach ($links as $link)
			{
				if (is_array($link))
				{
					foreach ($link as $group => $arrayLink)
					{
						$href = $arrayLink->getHref();
						$href = $this->addUriParameters($href, $absoluteHrefs);
						$arrayLink->setHref($href);
						$hal->setReplacedLink($arrayLink, $group);
					}
				}
				else
				{
					$href = $link->getHref();
					$href = $this->addUriParameters($href, $absoluteHrefs);
					$link->setHref($href);
					$hal->setReplacedLink($link);
				}
			}
		}

		// Adjust hrefs in the _embedded object (if there is one).
		if ($embedded = $hal->getEmbedded())
		{
			foreach ($embedded as $resources)
			{
				if (is_object($resources))
				{
					$this->relToAbs($resources, $absoluteHrefs);
				}
				elseif (is_array($resources))
				{
					foreach ($resources as $resource)
					{
						if (is_object($resource))
						{
							$this->relToAbs($resource, $absoluteHrefs);
						}
					}
				}
			}
		}
	}

	/**
	 * Prepares link
	 *
	 * @param   string   $href           Link location
	 * @param   boolean  $absoluteHrefs  Should we replace link Href with absolute.
	 *
	 * @return  string  Modified link
	 *
	 * @since   1.2
	 */
	public function addUriParameters($href, $absoluteHrefs)
	{
		if ($absoluteHrefs && substr($href, 0, 1) == '/')
		{
			$href = rtrim(JUri::base(), '/') . $href;
		}

		$uri = JUri::getInstance($href);

		if (!empty($this->uriParams))
		{
			foreach ($this->uriParams as $paramKey => $param)
			{
				if (!$uri->hasVar($paramKey))
				{
					$uri->setVar($paramKey, $param);
				}
			}
		}

		return $uri->toString();
	}

	/**
	 * Sets the document name
	 *
	 * @param   string  $name  Document name
	 *
	 * @return  RApiHalDocumentDocument instance of $this to allow chaining
	 *
	 * @since   1.2
	 */
	public function setName($name = 'joomla')
	{
		$this->name = $name;

		return $this;
	}
}
