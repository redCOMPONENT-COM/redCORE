<?php
/**
 * @package     Redcore
 * @subpackage  Fields
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

require_once dirname(__FILE__) . '/rtext.php';

/**
 * Field to select a user id from a modal list.
 *
 * @package     Redcore
 * @subpackage  Fields
 * @since       1.0
 */
class JFormFieldRuser extends JFormFieldRtext
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $type = 'Ruser';

	/**
	 * Method to get the user field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.0
	 */
	protected function getInput()
	{
		$html = array();
		$groups = $this->getGroups();
		$excluded = $this->getExcluded();
		$link = 'index.php?option=com_users&amp;view=users&amp;layout=modal&amp;tmpl=component&amp;field=' . $this->id
			. (isset($groups) ? ('&amp;groups=' . base64_encode(json_encode($groups))) : '')
			. (isset($excluded) ? ('&amp;excluded=' . base64_encode(json_encode($excluded))) : '')
			. '&amp;redcore=true';

		// Initialize some field attributes.
		if (isset($this->element['required']) && $this->element['required'] == 'true')
		{
			$this->addAttribute('required', 'required');
		}

		// Automatically insert any other attribute inserted
		if ($elementAttribs = $this->element->attributes())
		{
			foreach ($elementAttribs as $name => $value)
			{
				if (!in_array($name, $this->forbiddenAttributes))
				{
					$this->addAttribute($name, $value);
				}
			}
		}

		$modalTitle = isset($this->element['modal_title']) ? JText::_($this->element['modal_title']) : JText::_('JLIB_FORM_SELECT_USER');

		$modalId = 'modal-' . $this->id;

		// Build the script.
		$script = array();
		$script[] = '	function jSelectUser_' . $this->id . '(id, title) {';
		$script[] = '		var old_id = document.getElementById("' . $this->id . '_id").value;';
		$script[] = '		if (old_id != id) {';
		$script[] = '			document.getElementById("' . $this->id . '_id").value = id;';
		$script[] = '			document.getElementById("' . $this->id . '").value = title;';
		$script[] = '			document.getElementById("' . $this->id . '").className = document.getElementById("'
			. $this->id . '").className.replace(" invalid" , "");';
		$script[] = '			' . (string) $this->element['onchange'];
		$script[] = '		}';
		$script[] = '		jQuery("#' . $modalId . '").modal("hide")';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Create the modal object
		$modal = RModal::getInstance(
			array(
				'attribs' => array(
					'id'    => $modalId,
					'class' => 'modal hide',
					'style' => 'width: 800px; height: 500px;'
				),
				'params' => array(
					'showHeader'      => true,
					'showFooter'      => false,
					'showHeaderClose' => true,
					'title' => $modalTitle,
					'link' => $link
				)
			),
			$modalId
		);

		$html[] = RLayoutHelper::render('modal', $modal);

		// Load the current username if available.
		$table = JTable::getInstance('user');

		if ($this->value)
		{
			$table->load($this->value);
		}
		else
		{
			$table->username = JText::_('JLIB_FORM_SELECT_USER');
		}

		$attr = $this->parseAttributes();

		// Create a dummy text field with the user name.
		$html[] = '<div class="input-append">';
		$html[] = '	<input type="text" id="' . $this->id . '" value="' . htmlspecialchars($table->name, ENT_COMPAT, 'UTF-8') . '"'
			. ' readonly="readonly"' . $attr . ' />';

		// Create the user select button.
		if ($this->element['readonly'] != 'true')
		{
			$html[] = '		<a class="btn btn-primary modalAjax" data-toggle="modal" title="' . JText::_('JLIB_FORM_CHANGE_USER') . '" href="#' . $modalId . '"'
				. ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
			$html[] = '<i class="icon-user"></i></a>';
		}

		$html[] = '</div>';

		// Create the real field, hidden, that stored the user id.
		$html[] = '<input type="hidden" id="' . $this->id . '_id" name="' . $this->name . '" value="' . (int) $this->value . '" />';

		return implode("\n", $html);
	}

	/**
	 * Method to get the filtering groups (null means no filtering)
	 *
	 * @return  mixed  array of filtering groups or null.
	 *
	 * @since   1.6.0
	 */
	protected function getGroups()
	{
		return null;
	}

	/**
	 * Method to get the users to exclude from the list of users
	 *
	 * @return  mixed  Array of users to exclude or null to to not exclude them
	 *
	 * @since   1.6.0
	 */
	protected function getExcluded()
	{
		return null;
	}
}
