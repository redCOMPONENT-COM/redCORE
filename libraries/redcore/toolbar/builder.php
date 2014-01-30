<?php
/**
 * @package     Redcore
 * @subpackage  Toolbar
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Class helping to build toolbars.
 *
 * @package     Redcore
 * @subpackage  Toolbar
 * @since       1.0
 */
final class RToolbarBuilder
{
	/**
	 * Create an edit button.
	 *
	 * @param   string  $task   The task name.
	 * @param   string  $class  A css class to add to the button.
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createEditButton($task, $class = '')
	{
		return new RToolbarButtonStandard('JTOOLBAR_EDIT', $task, $class, 'icon-edit');
	}

	/**
	 * Create a new button.
	 *
	 * @param   string  $task   The task name.
	 * @param   string  $class  A css class to add to the button.
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createNewButton($task, $class = '')
	{
		if (empty($class))
		{
			$class = 'btn-success';
		}

		else
		{
			$class .= ' btn-success';
		}

		return new RToolbarButtonStandard('JTOOLBAR_NEW', $task, $class, 'icon-file-text-alt', '', false);
	}

	/**
	 * Create a publish button.
	 *
	 * @param   string  $task   The task name.
	 * @param   string  $class  A css class to add to the button.
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createPublishButton($task, $class = '')
	{
		return new RToolbarButtonStandard('JTOOLBAR_PUBLISH', $task, $class, 'icon-plus-sign');
	}

	/**
	 * Create an unpublish button.
	 *
	 * @param   string  $task   The task name.
	 * @param   string  $class  A css class to add to the button.
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createUnpublishButton($task, $class = '')
	{
		return new RToolbarButtonStandard('JTOOLBAR_UNPUBLISH', $task, $class, 'icon-minus-sign');
	}

	/**
	 * Create a trash button.
	 *
	 * @param   string  $task   The task name.
	 * @param   string  $class  A css class to add to the button.
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createTrashButton($task, $class = '')
	{
		return new RToolbarButtonStandard('JTOOLBAR_TRASH', $task, $class, 'icon-trash');
	}

	/**
	 * Create an archive button.
	 *
	 * @param   string  $task   The task name.
	 * @param   string  $class  A css class to add to the button.
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createArchiveButton($task, $class = '')
	{
		return new RToolbarButtonStandard('JTOOLBAR_ARCHIVE', $task, $class, 'icon-archive');
	}

	/**
	 * Create a featured button.
	 *
	 * @param   string  $task   The task name.
	 * @param   string  $class  A css class to add to the button.
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createFeaturedButton($task, $class = '')
	{
		return new RToolbarButtonStandard('JFEATURED', $task, $class, 'icon-featured');
	}

	/**
	 * Create a delete button.
	 *
	 * @param   string  $task   The task name.
	 * @param   string  $class  A css class to add to the button.
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createDeleteButton($task, $class = '')
	{
		if (empty($class))
		{
			$class = 'btn-danger';
		}

		else
		{
			$class .= ' btn-danger';
		}

		return new RToolbarButtonStandard('JTOOLBAR_DELETE', $task, $class, 'icon-remove-sign');
	}

	/**
	 * Create a checkin button.
	 *
	 * @param   string  $task   The task name.
	 * @param   string  $class  A css class to add to the button.
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createCheckinButton($task, $class = '')
	{
		return new RToolbarButtonStandard('JTOOLBAR_CHECKIN', $task, $class, 'icon-check');
	}

	/**
	 * Create a cancel button.
	 *
	 * @param   string  $task   The task name.
	 * @param   string  $class  A css class to add to the button.
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createCancelButton($task, $class = '')
	{
		if (empty($class))
		{
			$class = 'btn-danger';
		}

		else
		{
			$class .= ' btn-danger';
		}

		return new RToolbarButtonStandard('JTOOLBAR_CANCEL', $task, $class, 'icon-remove', false);
	}

	/**
	 * Create a close button.
	 *
	 * @param   string  $task   The task name.
	 * @param   string  $class  A css class to add to the button.
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createCloseButton($task, $class = '')
	{
		if (empty($class))
		{
			$class = 'btn-danger';
		}

		else
		{
			$class .= ' btn-danger';
		}

		return new RToolbarButtonStandard('JTOOLBAR_CLOSE', $task, $class, 'icon-remove', false);
	}

	/**
	 * Create a save button.
	 *
	 * @param   string  $task   The task name.
	 * @param   string  $class  A css class to add to the button.
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createSaveButton($task, $class = '')
	{
		if (empty($class))
		{
			$class = 'btn-success';
		}

		else
		{
			$class .= ' btn-success';
		}

		return new RToolbarButtonStandard('JTOOLBAR_APPLY', $task, $class, 'icon-save', false);
	}

	/**
	 * Create a save and close button.
	 *
	 * @param   string  $task   The task name.
	 * @param   string  $class  A css class to add to the button.
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createSaveAndCloseButton($task, $class = '')
	{
		return new RToolbarButtonStandard('JTOOLBAR_SAVE', $task, $class, 'icon-save', false);
	}

	/**
	 * Create a save and new button.
	 *
	 * @param   string  $task   The task name.
	 * @param   string  $class  A css class to add to the button.
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createSaveAndNewButton($task, $class = '')
	{
		return new RToolbarButtonStandard('JTOOLBAR_SAVE_AND_NEW', $task, $class, 'icon-save', false);
	}

	/**
	 * Create a save as copy button.
	 *
	 * @param   string  $task   The task name.
	 * @param   string  $class  A css class to add to the button.
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createSaveAsCopyButton($task, $class = '')
	{
		return new RToolbarButtonStandard('JTOOLBAR_SAVE_AS_COPY', $task, $class, 'icon-copy', false);
	}

	/**
	 * Create a link button.
	 *
	 * @param   string  $url        The button task.
	 * @param   string  $text       The button text.
	 * @param   string  $iconClass  The icon class.
	 * @param   string  $class      The button class.
	 *
	 * @return  RToolbarButtonLink  The button.
	 */
	public static function createLinkButton($url, $text, $iconClass = '', $class = '')
	{
		return new RToolbarButtonLink($text, $url, $class, $iconClass);
	}

	/**
	 * Create an options (preferences) button.
	 *
	 * @param   string  $component  The component name.
	 * @param   string  $path       The path.
	 * @param   string  $class      A class attribute for the button.
	 *
	 * @return  RToolbarButtonLink  The button.
	 */
	public static function createOptionsButton($component, $path = '', $class = '')
	{
		$component = urlencode($component);
		$path = urlencode($path);
		$uri = (string) JUri::getInstance();
		$return = urlencode(base64_encode($uri));
		$link = 'index.php?option=com_config&amp;view=component&amp;component=' .
			$component . '&amp;path=' . $path . '&amp;return=' . $return;

		return new RToolbarButtonLink('JToolbar_Options', $link, $class, 'icon-cogs');
	}

	/**
	 * Create an options (preferences) button.
	 *
	 * @param   string  $component  The component name.
	 * @param   string  $class      A class attribute for the button.
	 *
	 * @return  RToolbarButtonLink  The button.
	 */
	public static function createRedcoreOptionsButton($component, $class = '')
	{
		$uri = JUri::getInstance();
		$return = base64_encode('index.php' . $uri->toString(array('query')));

		$link = 'index.php?option=com_redcore&view=config&layout=edit&component=' .
			$component . '&return=' . $return;

		return new RToolbarButtonLink('JToolbar_Options', $link, $class, 'icon-cogs');
	}

	/**
	 * Create a standard button (mapped to a controller task).
	 *
	 * @param   string   $task       The button task.
	 * @param   string   $text       The button text.
	 * @param   string   $class      The button class.
	 * @param   string   $iconClass  The icon class.
	 * @param   boolean  $list       Is the button applying on a list ?
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createStandardButton($task, $text, $class = '', $iconClass = '', $list = true)
	{
		return new RToolbarButtonStandard($text, $task, $class, $iconClass, $list);
	}

	/**
	 * Create a modal button.
	 *
	 * @param   string   $dataTarget  The data-target selector.
	 * @param   string   $text        The button text.
	 * @param   string   $class       The button class.
	 * @param   string   $iconClass   The icon class.
	 * @param   boolean  $list        Is the button applying on a list ?
	 *
	 * @return  RToolbarButtonStandard  The button.
	 */
	public static function createModalButton($dataTarget, $text, $class = '', $iconClass = '', $list = false)
	{
		return new RToolbarButtonModal($text, $dataTarget, $class, $iconClass, $list);
	}

	/**
	 * Create a csv button to redirecto to a view.
	 *
	 * @param   mixed  $link  The link to the view.
	 *                        If null, it will append &format=csv to the current view.
	 *
	 * @return  RToolbarButtonLink  The button
	 */
	public static function createCsvButton($link = null)
	{
		if (null === $link)
		{
			$uri = JUri::getInstance();
			$uri->setVar('format', 'csv');
			$link = $uri->toString();
		}

		return self::createLinkButton($link, 'LIB_REDCORE_CSV', 'icon-table');
	}
}
