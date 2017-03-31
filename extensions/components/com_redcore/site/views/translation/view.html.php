<?php
/**
 * @package     Redcore.Admin
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;
jimport('joomla.html.editor');

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
	public $translationTableName;

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
		$app = JFactory::getApplication();
		$this->translationTableName = $app->input->get('translationTableName', '');
		$this->translationTable = RTranslationTable::setTranslationTableWithColumn($this->translationTableName);

		$this->item	= $this->get('Item');

		$editor = JFactory::getConfig()->get('editor');
		$this->editor = JEditor::getInstance($editor);

		$this->columns = array();
		$this->noTranslationColumns = array();
		$tableColumns = (array) $this->translationTable->columns;

		foreach ($this->translationTable->allColumns as $field)
		{
			foreach ($tableColumns as $column)
			{
				if ($column == $field['name'])
				{
					$this->columns[$column] = $field;

					break;
				}
			}

			if ($field['column_type'] != RTranslationTable::COLUMN_TRANSLATE && $field['value_type'] != 'referenceid')
			{
				$this->noTranslationColumns[$field['name']] = $field;
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
		return JText::_('COM_REDCORE_TRANSLATIONS') . ' <small>' . JText::_('JEDIT') . ' ' . $this->translationTable->title . '</small>';
	}

	/**
	 * Get the protected properties that are needed to create translation layout.
	 *
	 * @return  array
	 */
	public function getLayoutProperties()
	{
		$array = array(
				'item' => $this->item,
				'form' => $this->form,
				'editor' => $this->editor,
			);

		return $array;
	}

	/**
	 * Set the current item to a specific id.
	 *
	 * @param   array  $transId  Id the current item should be set to.
	 *
	 * @return  void
	 */
	public function setItem($transId)
	{
		$input = JFactory::getApplication()->input;
		$input->set('rctranslations_id', $transId);

		$item = $this->get('Item');
		$this->item = $item;
		$this->form = $this->get('Form');
	}
}
