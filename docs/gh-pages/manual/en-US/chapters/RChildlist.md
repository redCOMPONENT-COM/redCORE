This field allows you to create a list field dependent on a parent field.

```
<field
	name="type"
	type="rchildlist"
	label="COM_REDSOURCE_FIELD_CHANNEL_TYPE"
	description="COM_REDSOURCE_FIELD_CHANNEL_TYPE_DESC"
	class="ch_type js-child-field"
	required="true"
	parent_selector="#jform_name"
	parent_varname="id"
	child_selector=".js-child-field"
	url="{admin}/index.php?option=com_redsource&amp;task=channel.getColumns&amp;format=raw"
	>
		<option value="">COM_REDSOURCE_SELECT_CHANNEL_TYPE</option>
</field>
```

When the parent field changes this field will fire an AJAX request at:  
http://yoursite.com/index.php?option=com_redsource&task=channel.getColumns&format=raw&id=PARENT_VALUE

Specific field attributes:
* `parent_selector` : DOM selector of the parent field
* `parent_varname` : Name of the var that the AJAX request expects to receive to identify the parent field.
* `child_selector` : DOM selector of the child field. This is not using automatic `id` because chosen replaces field id with a random identifier.
* `url` : URL of the AJAX request that we want to fire. The `{admin}` tag will be replaced by the backend URL. We can also use `{site}` to use a frontend controller.

The AJAX function has to return a json encoded list of text / value items. Example:

```
<?php
	/**
	 * Returns channel columns as json
	 *
	 * @return string
	 */
	public function getColumns()
	{
		$app        = JFactory::getApplication();
		$channel_id = $app->input->get('id');

		$model   = $this->getModel();

		if ($columns = $model->getAvailableColumns($channel_id))
		{
			foreach ($columns as &$column)
			{
				$column = (object) array('text' => $column, 'value' => $column);
			}
		}

		echo json_encode($columns);

		$app->close();
	}
```