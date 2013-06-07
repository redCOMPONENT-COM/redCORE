<?php
/**
 * @package     RedRad
 * @subpackage  Toolbar
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_REDRAD') or die;

/**
 * Class helping to build toolbars.
 *
 * @package     RedRad
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

		return new RToolbarButtonStandard('JTOOLBAR_NEW', $task, $class, 'icon-new', '', false);
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
		return new RToolbarButtonStandard('JTOOLBAR_PUBLISH', $task, $class, 'icon-publish');
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
		return new RToolbarButtonStandard('JTOOLBAR_UNPUBLISH', $task, $class, 'icon-unpublish');
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
		return new RToolbarButtonStandard('JTOOLBAR_EMPTY_TRASH', $task, $class, 'icon-delete');
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
		return new RToolbarButtonStandard('JTOOLBAR_CHECKIN', $task, $class, 'icon-checkin');
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
		return new RToolbarButtonStandard('JTOOLBAR_CANCEL', $task, $class, 'icon-cancel', false);
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
		return new RToolbarButtonStandard('JTOOLBAR_APPLY', $task, $class, 'icon-apply', false);
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
		return new RToolbarButtonStandard('JTOOLBAR_SAVE', $task, $class, 'icon-apply', false);
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
		return new RToolbarButtonStandard('JTOOLBAR_SAVE_AND_NEW', $task, $class, 'icon-save-new', false);
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

		return new RToolbarButtonLink('JToolbar_Options', $link, $class, 'icon-options');
	}
}
