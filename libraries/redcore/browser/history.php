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
 * Browser History implemented as a queue.
 *
 * @package     Redcore
 * @subpackage  Browser
 * @since       1.0
 */
class RBrowserHistory
{
	/**
	 * The session variable name.
	 *
	 * @var  string
	 */
	protected $sessionVariable;

	/**
	 * The session.
	 *
	 * @var  JSession
	 */
	protected $session;

	/**
	 * Constructor.
	 *
	 * @param   string  $sessionVariable  The session variable name.
	 */
	public function __construct($sessionVariable)
	{
		$this->sessionVariable = $sessionVariable;
		$this->session = JFactory::getSession();
	}

	/**
	 * Enqueue an element.
	 *
	 * @param   mixed    $data           The data to enqueue.
	 * @param   boolean  $duplicateLast  True to duplicate the last element if it's the same.
	 *
	 * @return  RBrowserHistory
	 */
	public function enqueue($data, $duplicateLast)
	{
		$queue = $this->getQueue();
		$last = end($queue);

		// We don't duplicate the last element.
		// It happens when saving multiple times
		if (!$duplicateLast && $data === $last)
		{
			return $this;
		}

		$queue[] = $data;
		end($queue);
		$this->setQueue($queue);

		return $this;
	}

	/**
	 * Dequeue an element.
	 *
	 * @return  mixed  The dequeued element or NULL.
	 */
	public function dequeue()
	{
		$queue = $this->getQueue();

		if (!empty($queue))
		{
			$element = array_pop($queue);
			end($queue);
			$this->setQueue($queue);

			return $element;
		}

		return null;
	}

	/**
	 * Get the current element.
	 *
	 * @return  mixed  The current element
	 */
	public function getCurrent()
	{
		$queue = $this->getQueue();

		if (!empty($queue))
		{
			return current($queue);
		}

		return null;
	}

	/**
	 * Get the last element without dequeue.
	 *
	 * @return  mixed  The last element
	 */
	public function getLast()
	{
		$queue = $this->getQueue();

		if (!empty($queue))
		{
			// Remove current element
			array_pop($queue);

			// Return the last element
			if (!empty($queue))
			{
				return end($queue);
			}
		}

		return null;
	}

	/**
	 * Get the inner queue.
	 *
	 * @return  array  The queue.
	 */
	public function getQueue()
	{
		$queue = $this->session->get($this->sessionVariable, null, 'rbrowser');

		if (is_array($queue))
		{
			end($queue);

			return $queue;
		}

		$queue = array();
		$this->setQueue($queue);

		return $queue;
	}

	/**
	 * Set the queue.
	 *
	 * @param   array  $queue  The queue.
	 *
	 * @return  void
	 */
	public function setQueue(array $queue)
	{
		$this->session->set($this->sessionVariable, $queue, 'rbrowser');
	}

	/**
	 * Clear the history.
	 *
	 * @return  array  The old queue.
	 */
	public function clear()
	{
		$old = $this->getQueue();
		$this->setQueue(array());

		return $old;
	}

	/**
	 * Get the session variable.
	 *
	 * @return  string  The session variable.
	 */
	public function getSessionVariable()
	{
		return $this->sessionVariable;
	}
}
