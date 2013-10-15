<?php
/**
 * @package     Redcore
 * @subpackage  Browser
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * The Browser.
 *
 * @package     Redcore
 * @subpackage  Browser
 * @since       1.0
 */
class RBrowser
{
	/**
	 * The history.
	 *
	 * @var  RBrowserHistory
	 */
	protected $history;

	/**
	 * The navigator
	 *
	 * @var  RBrowser
	 */
	protected static $instances = array();

	/**
	 * Constructor.
	 *
	 * @param   string  $history  The history name (also used for sessions).
	 */
	private function __construct($history)
	{
		$this->history = new RBrowserHistory($history);
	}

	/**
	 * Get an instance or create it
	 *
	 * @param   string  $history  The history name (also used for sessions).
	 *
	 * @return  RBrowser  The navigator
	 */
	public static function getInstance($history = 'history')
	{
		if (!isset(self::$instances[$history]))
		{
			self::$instances[$history] = new static($history);
		}

		return static::$instances[$history];
	}

	/**
	 * Browse the given uri.
	 *
	 * @param   string   $uri            The uri
	 * @param   boolean  $duplicateLast  True to duplicate the last element if it's the same.
	 *
	 * @return  void
	 */
	public function browse($uri = null, $duplicateLast = false)
	{
		if (null === $uri)
		{
			$uri = str_replace(Juri::base(), '', Juri::getInstance()->toString());
		}

		$this->history->enqueue($uri, $duplicateLast);
	}

	/**
	 * Go back.
	 *
	 * @param   boolean  $forget  True to forget the current uri.
	 *
	 * @return  string  The current uri
	 */
	public function back($forget = true)
	{
		if ($forget)
		{
			return $this->history->dequeue();
		}

		return $this->getCurrentUri();
	}

	/**
	 * Get the current uri.
	 *
	 * @return   string  The current uri
	 */
	public function getCurrentUri()
	{
		return $this->history->getCurrent();
	}

	/**
	 * Get the current view.
	 *
	 * @return  mixed  The current view
	 */
	public function getCurrentView()
	{
		$currentUri = $this->getCurrentUri();

		if (!is_string($currentUri) || empty($currentUri))
		{
			return null;
		}

		$uri = JUri::getInstance($currentUri);

		return $uri->getVar('view');
	}

	/**
	 * Get the last uri.
	 *
	 * @return   string  The last uri
	 */
	public function getLastUri()
	{
		return $this->history->getLast();
	}

	/**
	 * Get the last view.
	 *
	 * @return  mixed  The last view
	 */
	public function getLastView()
	{
		$lastUri = $this->getLastUri();

		if (!is_string($lastUri) || empty($lastUri))
		{
			return null;
		}

		$uri = JUri::getInstance($lastUri);

		return $uri->getVar('view');
	}

	/**
	 * Clear the browser history.
	 *
	 * @return  void
	 */
	public function clearHistory()
	{
		$this->history->clear();
	}

	/**
	 * Get the history name.
	 *
	 * @return  string  The history name
	 */
	public function getHistoryName()
	{
		return $this->history->getSessionVariable();
	}
}
