This helper provides functions to help dealing with ISO 4217 currencies.

```
	/**
	 * Return iso code from iso number
	 *
	 * @param   string  $number  iso number
	 *
	 * @return multitype:string |boolean code or false if not found
	 */
	public static function getIsoCode($number)
```
```
	/**
	 * Return number corresponding to iso code
	 *
	 * @param   string  $code  3 letters code (e.g USD, EUR,...)
	 *
	 * @return string
	 */
	public static function getIsoNumber($code)
```
```
/**
* Return decimal precision in number of digits
*
* @param string $code iso 4217 3 letters currency code
*
* @throws OutOfRangeException
*
* @return int
*/
public static function getPrecision($code)
```
```
	/**
	 * get currencies as options, with code as value
	 *
	 * @return array
	 */
	public static function getCurrencyOptions()
```
```
	/**
	 * Check if given currency code is valid
	 *
	 * @param   string  $currency  iso 4217 currency code
	 *
	 * @return boolean true if exists
	 */
	public static function isValid($currency)
```

You can find an example of usage in redcomponent [currency converter plugins](https://github.com/redCOMPONENT-COM/CurrencyConverters/blob/master/plugins/openexchangerates/openexchangerates.php)