There is a base system for list helpers. You can use it like:

```
<?php
final class RedshopbHelperDepartments extends RHelperList
```

The important part is the `extends RHelperList`. 

This helper will try to automatically communicate with a model called `RedshopbModelDepartments`.

If you want to specify the folder of the model (which is always a good practice to use the helper from outside the component) you need to create a function like:

```
<?php
	protected static function getModelPath()
	{
		return JPATH_ADMINISTRATOR . '/components/com_redshopb/models/departments.php';
	}
```
Then if the model is not found it will automatically require it. 

The new helper will have automatically two functions:

```
<?php
	/**
	 * Get and instance of the model
	 *
	 * @param   array  $config  Configuration array for model. Optional.
	 *
	 * @return  object          An instance of the model
	 */
	public static function getModel($config = array('ignore_request' => true))

	/**
	 * Search items based on filters
	 *
	 * @param   array  $filters  Filters to apply to the search
	 * @param   array  $options  start, limit, direction, ordering...
	 *
	 * @return  mixed            array -> items found | false -> error
	 *
	 * @todo  Create & use a frontend bookings model
	 */
	public static function search($filters = array(), $options = array())

```
So you can get the associated model fastly from anywhere with: 
```
<?php
RedshopbHelperDepartments::getModel();
```
You can use the search function with something like:

```
<?php
$departments = RedshopbHelperDepartments::search(array('search' => 'developers'), array('limit' => 2, 'ordering' => 'name', 'direction' => 'desc'));
```
So you can filtered data from the model, paginate it, order it, etc.