<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 5/16/2019
 * Time: 9:29 AM
 */

namespace Page;

/**
 * Class UrlPage
 * @package Page
 */
class UrlPage
{
	/**
	 * @var string
	 */
	public static $url1 = 'administrator/index.php?option=com_redcore';

	/**
	 * @var string
	 */
	public static $urlTranslationTables = 'administrator/index.php?option=com_redcore&view=translation_tables';

	/**
	 * @var string
	 */
	public static $urlBannerClients = 'administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=banner_clients';

	/**
	 * @var string
	 */
	public static $urlBannersReturn ='administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=banners&return';

	/**
	 * @var string
	 */
	public static $urlCategories ='administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=categories';

	/**
	 * @var string
	 */
	public static $urlContactDetails ='administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=contact_details';

	/**
	 * @var string
	 */
	public static $urlContent ='administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=content';

	/**
	 * @var string
	 */
	public static $urlLanguages ='administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=languages';

	/**
	 * @var string
	 */
	public static $urlMenu ='administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=menu';

	/**
	 * @var string
	 */
	public static $urlModules ='administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=modules';

	/**
	 * @var string
	 */
	public static $urlExtensions ='administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=extensions';

	/**
	 * @var string
	 */
	public static $urlRedcoreCountry ='administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=redcore_country';

	/**
	 * @var string
	 */
	public static $urlRedcore_Currency ='administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=redcore_currency';

	/**
	 * @var string
	 */
	public static $urlUser ='administrator/index.php?option=com_redcore&view=translations&filter[translationTableName]=users';

	/**
	 * @var string
	 */
	public static $urlWebServices ='administrator/index.php?option=com_redcore&view=webservices';

	/**
	 * @var string
	 */
	public static $urlOauthClients ='administrator/index.php?option=com_redcore&view=oauth_clients';

	/**
	 * @var string
	 */
	public static $urlPaymentDashboard ='administrator/index.php?option=com_redcore&view=payment_dashboard';

	/**
	 * @var string
	 */
	public static $urlPaymentConfigurations ='administrator/index.php?option=com_redcore&view=payment_configurations';

	/**
	 * @var string
	 */
	public static $urlPayments ='administrator/index.php?option=com_redcore&view=payments';

}