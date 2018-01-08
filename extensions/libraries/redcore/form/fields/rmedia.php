<?php
/**
 * @package     Redcore
 * @subpackage  Fields
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
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
		$bootstrapVersion = RHtmlMedia::getFramework();
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
			$script[] = '		if (value && old_value != value) {';
			$script[] = '			var elem = document.getElementById(id);';
			$script[] = '			elem.value = value;';
			$script[] = '			var changeEvent = document.createEvent("HTMLEvents");';
			$script[] = '			changeEvent.initEvent("change", true, true);';
			$script[] = '			elem.dispatchEvent(changeEvent);';
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
			$script[] = '			iframe.style.maxHeight = iframe.height';
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

		$inputClass = $bootstrapVersion == 'bootstrap2' ? 'input-prepend input-append' : 'input-group';

		// The text field.
		$html[] = '<div class="' . $inputClass . '">';

		// The Preview.
		$preview       = (string) $this->element['preview'];
		$showPreview   = true;
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
				$options       = array(
					'onShow'  => 'jMediaRefreshPreviewTip',
					'trigger' => 'hover, focus'
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
				);

				if ($bootstrapVersion == 'bootstrap2')
				{
					$options['text'] = '<i class="icon-eye-open"></i>';
					$options['class'] = 'hasTipPreview';
				}
				else
				{
					$options['text'] = '<i class="glyphicon glyphicon-eye-open"></i>';
					$options['data-toggle'] = 'tooltip';
				}

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

		$inputClass = $bootstrapVersion == 'bootstrap2' ? 'input-small' : 'input-sm';

		$html[] = '	<input type="text" class="' . $inputClass . '" name="' . $this->name . '" id="' . $this->id . '" value="'
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

		$hideModal = $bootstrapVersion == 'bootstrap2' ? 'modal hide' : 'modal';
		$style = $bootstrapVersion == 'bootstrap2' ? 'width: 820px; height: 500px; margin-left: -410px; top: 50%; margin-top: -250px;' : '';

		// Create the modal object
		$modal = RModal::getInstance(
			array(
				'attribs' => array(
					'id'    => $modalId,
					'class' => $hideModal,
					'style' => $style,
					'tabindex' => '-1',
					'role' => 'dialog'
				),
				'params' => array(
					'showHeader'      => true,
					'showFooter'      => false,
					'showHeaderClose' => true,
					'title' => $modalTitle,
					'link' => $link,
					'events' => array (
						'onload' => 'jSetIframeHeight'
					)
				)
			),
			$modalId
		);

		$html[] = RLayoutHelper::render(
			'fields.rmedia',
			array(
				'modal' => $modal,
				'field' => $this
			)
		);

		$html[] = '</div>';

		return implode("\n", $html);
	}
}
