<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JDocument system message renderer
 *
 * @package     Joomla.Platform
 * @subpackage  Document
 * @since       11.1
 */
class JDocumentRendererMessage extends JDocumentRenderer
{
	/**
	 * Renders the error stack and returns the results as a string
	 *
	 * @param   string  $name     Not used.
	 * @param   array   $params   Associative array of values
	 * @param   string  $content  Not used.
	 *
	 * @return  string  The output of the script
	 */
	public function render($name, $params = array (), $content = null)
	{
		$msgList = $this->getData();
		$buffer = null;
		$app = JFactory::getApplication();
		$chromePath = JPATH_THEMES . '/' . $app->getTemplate() . '/html/message.php';
		$itemOverride = false;

		if (file_exists($chromePath))
		{
			include_once $chromePath;

			if (function_exists('renderMessage'))
			{
				$itemOverride = true;
			}
		}

		$buffer = ($itemOverride) ? renderMessage($msgList) : $this->renderDefaultMessage($msgList);

		return $buffer;
	}

	/**
	 * Get and prepare system message data for output
	 *
	 * @return  array  An array contains system message
	 */
	private function getData()
	{
		// Initialise variables.
		$lists = array();

		// Get the message queue
		$messages = JFactory::getApplication()->getMessageQueue();

		// Build the sorted message list
		if (is_array($messages) && !empty($messages))
		{
			foreach ($messages as $msg)
			{
				if (isset($msg['type']) && isset($msg['message']))
				{
					$lists[$msg['type']][] = $msg['message'];
				}
			}
		}

		return $lists;
	}

	/**
	 * Render the system message if no message template file found
	 *
	 * @param   array  $msgList  An array contains system message
	 *
	 * @return  string  System message markup
	 */
	private function renderDefaultMessage($msgList)
	{
		$buffer  = null;
		$buffer .= "\n<div id=\"system-message-container\">";
		$alert = array('error' => 'alert-error', 'warning' => '', 'notice' => 'alert-info', 'message' => 'alert-success');

		// Only render the message list and the close button if $msgList has items
		if (is_array($msgList) && (count($msgList) >= 1))
		{
			$buffer .= '<button type="button" class="close" data-dismiss="alert">&times;</button>';

			foreach ($msgList as $type => $msgs)
			{
				$buffer .= '<div class="alert ' . $alert[$type] . '">';
				$buffer .= "\n<h4 class=\"alert-heading\">" . JText::_($type) . "</h4>";

				if (count($msgs))
				{
					foreach ($msgs as $msg)
					{
						$buffer .= "\n\t\t<p>" . $msg . "</p>";
					}
				}

				$buffer .= "\n</div>";
			}
		}

		$buffer .= "\n</div>";

		return $buffer;
	}
}
