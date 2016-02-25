<?php
/**
 * @package     Redcore
 * @subpackage  Browser
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
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
	protected function __construct($history)
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
	 * Get Uri
	 *
	 * @param   string  $url  URL
	 *
	 * @return  JUri
	 */
	public function getUri($url = 'SERVER')
	{
		static $uriArray = array();

		if (!array_key_exists($url, $uriArray))
		{
			// This will enable both SEF and non-SEF URI to be parsed properly
			$router = clone JRouter::getInstance('site');
			$uri = clone JUri::getInstance($url);
			$langCode = JLanguageHelper::detectLanguage();
			$lang = $uri->getVar('lang', $langCode);
			$uri->setVar('lang', $lang);
			$sefs = JLanguageHelper::getLanguages('lang_code');

			if (isset($sefs[$lang]))
			{
				$lang = $sefs[$lang]->sef;
				$uri->setVar('lang', $lang);
			}

			$router->setVars(array(), false);
			$query = $router->parse($uri);
			$query = array_merge($query, $uri->getQuery(true));
			$uri->setQuery($query);

			// We are removing format because of default value is csv if present and if not set
			// and we are never going to remember csv page in a browser history anyway
			$uri->delVar('format');

			$uriArray[$url] = $uri;
		}

		return $uriArray[$url];
	}

	/**
	 * Browse the given uri.
	 *
	 * @param   string   $url            The uri
	 * @param   boolean  $duplicateLast  True to duplicate the last element if it's the same.
	 *
	 * @return  void
	 */
	public function browse($url = 'SERVER', $duplicateLast = false)
	{
		if ($url == null)
		{
			$url = 'SERVER';
		}

		$uri = $this->getUri($url);
		$url = 'index.php?' . $uri->getQuery();

		$this->history->enqueue($url, $duplicateLast);
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

		$uri = $this->getUri($currentUri);

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

		$uri = $this->getUri($lastUri);

		return $uri->getVar('view');
	}

	/**
	 * Get the history.
	 *
	 * @return  array  The history
	 */
	public function getHistory()
	{
		return $this->history->getQueue();
	}

	/**
	 * Clear the history until the uri.
	 * Two uris are equal if their view and id vars are the same.
	 *
	 * @param   mixed  $url  The uri until
	 *
	 * @return  void
	 */
	public function clearHistoryUntil($url = 'SERVER')
	{
		$history = $this->history->getQueue();

		if (empty($history))
		{
			return;
		}

		$uri = $this->getUri($url);
		$view = $uri->getVar('view');
		$id = $uri->getVar('id');
		$newHistory = array();

		foreach ($history as $oldLink)
		{
			$oldUri = $this->getUri($oldLink);
			$oldView = $oldUri->getVar('view');
			$oldId = $oldUri->getVar('id');

			if ($oldView === $view && $oldId === $id)
			{
				break;
			}

			$newHistory[] = $oldLink;
		}

		$this->history->setQueue($newHistory);
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
