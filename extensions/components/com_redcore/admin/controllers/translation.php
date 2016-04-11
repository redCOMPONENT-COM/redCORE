<?php
/**
 * @package     Redcore.Backend
 * @subpackage  Controllers
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

/**
 * Translation Controller
 *
 * @package     Redcore.Backend
 * @subpackage  Controllers
 * @since       1.0
 */
class RedcoreControllerTranslation extends RControllerForm
{
	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'rctranslations_id')
	{
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);

		$append .= $this->getCommonRedirectAppend($append);

		if ($rctranslations_id = $this->input->get('rctranslations_id'))
		{
			$append .= '&rctranslations_id=' . $rctranslations_id;
		}

		if ($id = $this->input->get('id'))
		{
			$append .= '&id=' . $id;
		}

		return $append;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   string  $append  String to append to
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 */
	protected function getCommonRedirectAppend($append = '')
	{
		if ($translationTableName = $this->input->get('translationTableName'))
		{
			$append .= '&translationTableName=' . $translationTableName;
		}

		if ($component = $this->input->get('component'))
		{
			$append .= '&component=' . $component;
		}

		if ($language = $this->input->get('language'))
		{
			$append .= '&language=' . $language;
		}

		return $append;
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 */
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();

		$append .= $this->getCommonRedirectAppend($append);

		return $append;
	}
}
