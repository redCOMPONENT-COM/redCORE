<?php
/**
 * @package     Redcore
 * @subpackage  Fields
 *
 * @copyright   Copyright (C) 2008 - 2015 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_REDCORE') or die;

/**
 * Media field.
 *
 * @package     Redcore
 * @subpackage  Fields
 * @since       1.0
 */
class JFormFieldRmedia extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	protected $type = 'Rmedia';

	/**
	 * The initialised state of the document object.
	 *
	 * @var  boolean
	 */
	protected static $initialised = false;

	/**
	 * Method to get the field input markup for a media selector.
	 * Use attributes to identify specific created_by and asset_id fields
	 *
	 * @return  string  The field input markup.
	 *
	 * @todo    Create a layout and put all the output HTML there!
	 */
	protected function getInput()
	{
		$assetField  = $this->element['asset_field'] ? (string) $this->element['asset_field'] : 'asset_id';
		$authorField = $this->element['created_by_field'] ? (string) $this->element['created_by_field'] : 'created_by';
		$asset = $this->form->getValue($assetField) ? $this->form->getValue($assetField) : (string) $this->element['asset_id'];

		if ($asset == '')
		{
			$asset = JFactory::getApplication()->input->get('option');
		}

		$link = (string) $this->element['link'];

		$modalTitle = isset($this->element['modal_title']) ? JText::_($this->element['modal_title']) : JText::_('LIB_REDCORE_MEDIA_MANAGER');

		$modalId = 'modal-' . $this->id;

		if (!self::$initialised)
		{
			// Build the script.
			$script = array();
			$script[] = '	function jInsertFieldValue(value, id) {';
			$script[] = '		var old_value = document.getElementById(id).value;';
			$script[] = '		if (old_value != value) {';
			$script[] = '			var elem = document.getElementById(id);';
			$script[] = '			elem.value = value;';
			$script[] = '			elem.fireEvent("change");';
			$script[] = '			if (typeof(elem.onchange) === "function") {';
			$script[] = '				elem.onchange();';
			$script[] = '			}';
			$script[] = '			jMediaRefreshPreview(id);';
			$script[] = '		};';
			$script[] = '		jQuery("#' . $modalId . '").modal("hide");';
			$script[] = '	}';

			$script[] = '	function jMediaRefreshPreview(id) {';
			$script[] = '		var value = document.getElementById(id).value;';
			$script[] = '		var img = document.getElementById(id + "_preview");';
			$script[] = '		if (img) {';
			$script[] = '			if (value) {';
			$script[] = '				img.src = "' . JUri::root() . '" + value;';
			$script[] = '				document.getElementById(id + "_preview_empty").setStyle("display", "none");';
			$script[] = '				document.getElementById(id + "_preview_img").setStyle("display", "");';
			$script[] = '			} else { ';
			$script[] = '				img.src = ""';
			$script[] = '				document.getElementById(id + "_preview_empty").setStyle("display", "");';
			$script[] = '				document.getElementById(id + "_preview_img").setStyle("display", "none");';
			$script[] = '			} ';
			$script[] = '		} ';
			$script[] = '	}';

			$script[] = '	function jMediaRefreshPreviewTip(tip)';
			$script[] = '	{';
			$script[] = '		var img = tip.getElement("img.media-preview");';
			$script[] = '		tip.getElement("div.tip").setStyle("max-width", "none");';
			$script[] = '		var id = img.getProperty("id");';
			$script[] = '		id = id.substring(0, id.length - "_preview".length);';
			$script[] = '		jMediaRefreshPreview(id);';
			$script[] = '		tip.setStyle("display", "block");';
			$script[] = '	}';

			$script[] = '	function jSetIframeHeight(iframe)';
			$script[] = '	{';
			$script[] = '		var newheight;';
			$script[] = '		if(iframe) {';
			$script[] = '			newheight = iframe.contentWindow.document.body.scrollHeight;';
			$script[] = '			iframe.height= (newheight) + "px";';
			$script[] = '			iframe.setStyle("max-height", iframe.height);';
			$script[] = '		}';
			$script[] = '	}';

			$script[] = "
				function closeModal(fieldId)
				{
					jQuery('#modal-' + fieldId).modal('hide');
				}
			";

			// Add the script to the document head.
			JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

			self::$initialised = true;
		}

		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		// The text field.
		$html[] = '<div class="input-prepend input-append input-group">';

		// The Preview.
		$preview = (string) $this->element['preview'];
		$showPreview = true;
		$showAsTooltip = false;

		switch ($preview)
		{
			case 'no': // Deprecated parameter value
			case 'false':
			case 'none':
				$showPreview = false;
				break;

			case 'yes': // Deprecated parameter value
			case 'true':
			case 'show':
				break;

			case 'tooltip':
			default:
				$showAsTooltip = true;
				$options = array(
					'onShow' => 'jMediaRefreshPreviewTip',
				);
				JHtml::_('rbootstrap.tooltip', '.hasTipPreview', $options);
				break;
		}

		if ($showPreview)
		{
			if ($this->value && file_exists(JPATH_ROOT . '/' . $this->value))
			{
				$src = JUri::root() . $this->value;
			}
			else
			{
				$src = '';
			}

			$width = isset($this->element['preview_width']) ? (int) $this->element['preview_width'] : 300;
			$height = isset($this->element['preview_height']) ? (int) $this->element['preview_height'] : 200;
			$style = '';
			$style .= ($width > 0) ? 'max-width:' . $width . 'px;' : '';
			$style .= ($height > 0) ? 'max-height:' . $height . 'px;' : '';

			$imgattr = array(
				'id' => $this->id . '_preview',
				'class' => 'media-preview',
				'style' => $style,
			);
			$img = JHtml::image($src, JText::_('JLIB_FORM_MEDIA_PREVIEW_ALT'), $imgattr);
			$previewImg = '<div id="' . $this->id . '_preview_img"' . ($src ? '' : ' style="display:none"') . '>' . $img . '</div>';
			$previewImgEmpty = '<div id="' . $this->id . '_preview_empty"' . ($src ? ' style="display:none"' : '') . '>'
				. JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '</div>';

			if ($showAsTooltip)
			{
				$html[] = '<div class="media-preview add-on input-group-addon">';
				$tooltip = $previewImgEmpty . $previewImg;
				$options = array(
					'title' => JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'),
					'text' => '<i class="icon-eye"></i>',
					'class' => 'hasTipPreview'
				);
				$html[] = RHtml::tooltip($tooltip, $options);
				$html[] = '</div>';
			}
			else
			{
				$html[] = '<div class="media-preview add-on input-group-addon" style="height:auto">';
				$html[] = ' ' . $previewImgEmpty;
				$html[] = ' ' . $previewImg;
				$html[] = '</div>';
			}
		}

		$html[] = '	<input type="text" class="input-small input-sm" name="' . $this->name . '" id="' . $this->id . '" value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" readonly="readonly"' . $attr . ' />';

		$directory = (string) $this->element['directory'];

		if ($this->value && file_exists(JPATH_ROOT . '/' . $this->value))
		{
			$folder = explode('/', $this->value);
			$folder = array_diff_assoc($folder, explode('/', JComponentHelper::getParams('com_media')->get('image_path', 'images')));
			array_pop($folder);
			$folder = implode('/', $folder);
		}
		elseif (file_exists(JPATH_ROOT . '/' . JComponentHelper::getParams('com_media')->get('image_path', 'images') . '/' . $directory))
		{
			$folder = $directory;
		}
		else
		{
			$folder = '';
		}

		$link = ($link ? $link : 'index.php?option=com_media&amp;view=images&amp;layout=modal&amp;tmpl=component&amp;asset='
				. $asset . '&amp;author=' . $this->form->getValue($authorField)) . '&amp;fieldid='
			. $this->id . '&amp;folder=' . $folder
			. '&amp;redcore=true';

		// Create the user select button.
		if ($this->readonly === false)
		{
			$html[] = RLayoutHelper::render(
				'modal.iframe',
				array(
					'view' => $this,
					'options' => array(
						'id'    => $modalId,
						'class' => 'modal fade',
						'tabindex' => '-1',
						'role' => 'dialog',
						'aria-hidden' => true,
						'header' => $modalTitle,
						'linkName' => '<i class="icon-upload"></i>',
						'link' => $link,
						'linkClass' => 'btn btn-primary',
						'showHeader'      => true,
						'showFooter'      => false,
						'showHeaderClose' => true,
						'events' => array (
							'onload' => 'jSetIframeHeight'
						)
					),
				)
			);
		}

		$html[] = '</div>';

		// Create the real field, hidden, that stored the user id.
		$html[] = '<input type="hidden" id="' . $modalId . '_id" name="' . $this->name . '" value="' . $this->value . '" />';

		return implode("\n", $html);
	}
}
