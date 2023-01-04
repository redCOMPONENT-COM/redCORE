<?php
/**
 * @package     Redcore
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2008 - 2021 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * ApiDocumentSoap class, provides an easy interface to parse and display XML output
 *
 * @package     Redcore
 * @subpackage  Document
 * @since       1.4
 */
class RApiSoapDocumentDocument extends JDocument
{
	/**
	 * Document name
	 *
	 * @var    string
	 * @since  1.4
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
	 * @var    string  Content
	 * @since  1.4
	 */
	public $outputContent = null;

	/**
	 * @var    RApiSoapSoap  Soap object
	 * @since  1.4
	 */
	public $soap = null;

	/**
	 * Class constructor
	 *
	 * @param   array   $options   Associative array of options
	 * @param   string  $mimeType  Document type
	 *
	 * @since  1.4
	 */
	public function __construct($options = array(), $mimeType = 'soap+xml')
	{
		parent::__construct($options);

		$this->documentFormat = $options['documentFormat'];

		if (!in_array($this->documentFormat, array('xml', 'json')))
		{
			$this->documentFormat = 'xml';
		}

		// Set default mime type.
		$this->_mime = 'application/' . $mimeType;

		// Set document type.
		$this->_type = 'xml';

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
	 * @since  1.4
	 */
	public function render($cache = false, $params = array())
	{
		// Get the Soap string from the buffer.
		$content = (string) $this->getBuffer();
		$app = JFactory::getApplication();
		$runtime = microtime(true) - $this->soap->startTime;
		$language = explode('-', $app->input->get('lang', RTranslationHelper::getSiteLanguage()));
		$language = $language[0];

		if ($this->soap->options->get('enable_gzip_compression', 0) && RBootstrap::$config->get('webservice_enable_gzip_compression', '1') == '1')
		{
			$app->setHeader('Content-Encoding', 'gzip', true);
			$content = gzencode($content, 1);
		}

		$app->setHeader('Status', $this->soap->statusCode . ' ' . $this->soap->statusText, true);
		$app->setHeader('Server', '', true);
		$app->setHeader('X-Runtime', $runtime, true);
        if (RBootstrap::$config->get('enable_access_control_header', '1') == '1')
        {
            $app->setHeader('Access-Control-Allow-Origin', '*', true);
        }
		$app->setHeader('Pragma', 'public', true);
		$app->setHeader('Expires', '0', true);
		$app->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
		$app->setHeader('Cache-Control', 'private', false);
		$app->setHeader('Content-type', $this->_mime . '; charset=UTF-8', true);
		$app->setHeader('Content-length', strlen($content), true);
		$app->setHeader('Content-Language', $language, true);

		$app->sendHeaders();

		echo $content;
	}

	/**
	 * Returns the document name
	 *
	 * @return  string
	 *
	 * @since  1.4
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Sets Soap object to the document
	 *
	 * @param   RApiSoapSoap  $soap  Soap object
	 *
	 * @return   RApiSoapDocumentDocument
	 *
	 * @since  1.4
	 */
	public function setApiObject($soap)
	{
		$this->soap = $soap;

		return $this;
	}

	/**
	 * Sets the document name
	 *
	 * @param   string  $name  Document name
	 *
	 * @return  RApiSoapDocumentDocument instance of $this to allow chaining
	 *
	 * @since   1.4
	 */
	public function setName($name = 'joomla')
	{
		$this->name = $name;

		return $this;
	}
}
