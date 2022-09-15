<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Views
 *
 * @copyright   Copyright (C) 2008 - 2021 redWEB.dk. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Product View
 *
 * @package     Redcore.Backend
 * @subpackage  Views
 * @since       1.0
 */
class RedcoreViewConfig extends RedcoreHelpersView
{
	/**
	 * @var  JForm|boolean
	 */
	protected $form;

	/**
	 * @var  object
	 */
	protected $component;

	/**
	 * @var  string
	 */
	protected $return;

	/**
	 * @var  array
	 */
	protected $components;

	/**
	 * @var  array
	 */
	protected $contentElements;

	/**
	 * @var  string
	 */
	protected $componentName;

	/**
	 * Display method
	 *
	 * @param   string  $tpl  The template name
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		/** @var RedcoreModelConfig $model */
		$model = $this->getModel('Config');
		$option = JFactory::getApplication()->input->getString('component', 'com_redcore');
		$this->componentName = $option;

		$lang = JFactory::getLanguage();

		// Load component language files
		$lang->load($option, JPATH_ADMINISTRATOR, 'en-GB', false, false)
		|| $lang->load($option, JPATH_ADMINISTRATOR . '/components/' . $option, 'en-GB', false, false);

		$this->form	= $model->getForm();
		$this->component = $model->getComponent($option);
		$this->return = JFactory::getApplication()->input->get('return', '', 'Base64');
		$this->componentTitle = RText::getTranslationIfExists($this->component->xml->name, '', '');
		$this->contentElements = RTranslationContentElement::getContentElements(true, $option);

		RLayoutHelper::$defaultBasePath = JPATH_ADMINISTRATOR . '/components/' . $option . '/layouts';

		$extensionXml = RComponentHelper::getComponentManifestFile($option);

		if (isset($extensionXml->redcore))
		{
			$attributes = $extensionXml->redcore->attributes();

			if (!empty($attributes['defaultFramework']))
			{
				RHtmlMedia::setFramework((string) $attributes['defaultFramework']);
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
		return $this->componentTitle . ' ' . JText::_('COM_REDCORE_CONFIG_FORM_TITLE');
	}

	/**
	 * Get the toolbar to render.
	 *
	 * @return  RToolbar
	 */
	public function getToolbar()
	{
		$group = new RToolbarButtonGroup;
		$secondGroup = new RToolbarButtonGroup;
		$user = JFactory::getUser();

		if ($user->authorise('core.admin', 'com_redcore'))
		{
			$save = RToolbarBuilder::createSaveButton('config.apply');
			$saveAndClose = RToolbarBuilder::createSaveAndCloseButton('config.save');

			$group->addButton($save)
				->addButton($saveAndClose);
		}

		$cancel = RToolbarBuilder::createCloseButton('config.cancel');

		$group->addButton($cancel);

		$toolbar = new RToolbar;
		$toolbar->addGroup($group)
			->addGroup($secondGroup);

		return $toolbar;
	}
}
