<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Translation View
 *
 * @package     Redcore.Admin
 * @subpackage  Views
 * @since       1.0
 */
class RedcoreViewTranslation extends RedcoreHelpersView
{
	/**
	 * @var  JForm
	 */
	protected $form;

	/**
	 * @var  object
	 */
	protected $item;

	/**
	 * @var  object
	 */
	protected $editor;

	/**
	 * @var  object
	 */
	public $translationTable;

	/**
	 * @var  object
	 */
	public $contentElement;

	/**
	 * @var  array
	 */
	public $columns;

	/**
	 * @var  array
	 */
	public $noTranslationColumns;

	/**
	 * @var  array
	 */
	public $fieldsXml;

	/**
	 * @var  string
	 */
	public $languageList;

	/**
	 * @var  string
	 */
	public $publishList;

	/**
	 * Display method
	 *
	 * @param   string  $tpl  The template name
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->form	= $this->get('Form');
		$this->translationTable = RedcoreHelpersTranslation::getTranslationTable();
		$this->contentElement = RTranslationHelper::getContentElement($this->translationTable->option, $this->translationTable->xml);
		$this->item	= $this->get('Item');

		$editor = JFactory::getConfig()->get('editor');
		$this->editor = JEditor::getInstance($editor);

		$this->columns = array();
		$tableColumns = (array) $this->translationTable->columns;
		$this->fieldsXml = $this->contentElement->getTranslateFields();

		foreach ($this->fieldsXml as $field)
		{
			foreach ($tableColumns as $column)
			{
				if ($column == (string) $field['name'])
				{
					$attributes = current($field->attributes());
					$attributes['titleLabel'] = (string) $field;
					$this->columns[$column] = $attributes;

					break;
				}
			}

			if ((string) $field['translate'] == '0' && (string) $field['type'] != 'referenceid')
			{
				$attributes = current($field->attributes());
				$attributes['titleLabel'] = (string) $field;
				$this->noTranslationColumns[(string) $field['name']] = $attributes;
			}
		}

		parent::display($tpl);
	}

	/**
	 * Get the view title.
	 *
	 * @return  string  The view title.
	 */
	public function getTitle()
	{
		return JText::_('COM_REDCORE_TRANSLATIONS') . ' <small>' . JText::_('JEDIT') . ' ' . $this->translationTable->name . '</small>';
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$group = new RToolbarButtonGroup;

		$save = RToolbarBuilder::createSaveButton('translation.apply');
		$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('translation.save');
		$cancel = RToolbarBuilder::createCancelButton('translation.cancel');

		$group->addButton($save)
			->addButton($saveAndClose)
			->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group);

		return $toolbar;
	}
}
