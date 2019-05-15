<?php
/**
 * @package     redCORE
 * @subpackage  Cept
 * @copyright   Copyright (C) 2008 - 2019 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
use Step\Acceptance\AbstractStep;

// Load the Step Object Page
class CheckForWarningsAndNoticesCest
{
	/**
	 * @param AbstractStep $I
	 * @throws Exception
	 */
	public function CheckForWarningsAndNotices(AbstractStep $I)
	{
		$I->doAdministratorLogin();
		$I->Enabletranslations();
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translation_tables');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=banner_clients');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=banners&return');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=categories');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=contact_details');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=content');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=languages');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=menu');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=modules');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=extensions');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=redcore_country');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=redcore_currency');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=users');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=webservices');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=oauth_clients');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=payment_dashboard');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=payment_configurations');
		$I->checkForPhpNoticesOrWarnings('administrator/index.php?option=com_redcore&view=payments');
	}
}