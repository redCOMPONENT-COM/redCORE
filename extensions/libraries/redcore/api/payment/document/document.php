<?php
/**
 * @package     Redcore
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_BASE') or die;

/**
 * Api Document Payment class, provides an easy interface to parse and display json output
 *
 * @package     Redcore
 * @subpackage  Document
 * @since       1.5
 */
class RApiPaymentDocumentDocument extends JDocument
{
	/**
	 * Document name
	 *
	 * @var    string
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
	 */
	public $outputContent = null;

	/**
	 * @var    RApiPaymentPayment  Payment object
	 */
	public $payment = null;

	/**
	 * Class constructor
	 *
	 * @param   array   $options   Associative array of options
	 * @param   string  $mimeType  Document type
	 *
	 * @since  1.4
	 */
	public function __construct($options = array(), $mimeType = 'json')
	{
		parent::__construct($options);

		$this->documentFormat = $options['documentFormat'];

		if (!in_array($this->documentFormat, array('json')))
		{
			$this->documentFormat = 'json';
		}

		// Set default mime type.
		$this->_mime = 'application/' . $mimeType;

		// Set document type.
		$this->_type = 'json';

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
		$app = JFactory::getApplication();
		$runtime = microtime(true) - $this->payment->startTime;
		$language = explode('-', $app->input->get('lang', RTranslationHelper::getSiteLanguage()));
		$language = $language[0];

		// Get the payment string from the buffer.
		$content = $this->getBuffer();
		$jsonEncodedContent = json_encode($content, JSON_UNESCAPED_SLASHES);

		RApi::setHeader('Status', $this->payment->statusCode . ' ' . $this->payment->statusText, true);
		RApi::setHeader('Server', '', true);
		RApi::setHeader('X-Runtime', $runtime, true);
		RApi::setHeader('Access-Control-Allow-Origin', '*', true);
		RApi::setHeader('Pragma', 'public', true);
		RApi::setHeader('Expires', '0', true);
		RApi::setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
		RApi::setHeader('Cache-Control', 'private', false);
		RApi::setHeader('Content-type', $this->_mime . '; charset=UTF-8', true);
		RApi::setHeader('Content-length', strlen($jsonEncodedContent), true);
		RApi::setHeader('Content-Language', $language, true);

		RApi::sendHeaders();

		// Check for defined constants
		if (!defined('JSON_UNESCAPED_SLASHES'))
		{
			define('JSON_UNESCAPED_SLASHES', 64);
		}

		echo json_encode($content, JSON_UNESCAPED_SLASHES);
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
	 * Sets Payment object to the document
	 *
	 * @param   RApiPaymentPayment  $payment  Payment object
	 *
	 * @return   RApiPaymentDocumentDocument
	 */
	public function setApiObject($payment)
	{
		$this->payment = $payment;

		return $this;
	}

	/**
	 * Sets the document name
	 *
	 * @param   string  $name  Document name
	 *
	 * @return  RApiPaymentDocumentDocument instance of $this to allow chaining
	 */
	public function setName($name = 'joomla')
	{
		$this->name = $name;

		return $this;
	}
}
