## Admin

You can disable the top and side bars.

### Disable the topbar

```
<?php

$input->set('disable_topbar', true);
```

### Disable the sidebar

```
<?php

$input->set('disable_sidebar', true);
```

### Disable both

```
<?php

$input->set('hidemainmenu', true);
```

## Csv
<span id=csv></span>

### RViewCsv

RViewCsv works with RModelList. 
You just need to implement `getColumns()` to automatically generate a csv file.

This method returns the columns you want to display :

- the array keys match the name of the columns returned by the model list method `getItems()`
- the array values correspond to the title you want to display for the corresponding column

```
<?php

class RedshopbViewDepartments extends RViewCsv
{
	/**
	 * Get the columns for the csv file.
	 *
	 * @return  array  An associative array of column names 
         *                 as key and the title as value.
	 */
	protected function getColumns()
	{
		return array(
			'name' => JText::_('COM_REDSHOPB_NAME'),
			'company' => JText::_('COM_REDSHOPB_COMPANY_LABEL'),
			'address' => JText::_('COM_REDSHOPB_ADDRESS_LABEL'),
			'zip' => JText::_('COM_REDSHOPB_ZIP_LABEL'),
			'city' => JText::_('COM_REDSHOPB_CITY_LABEL'),
			'country' => JText::_('COM_REDSHOPB_COUNTRY_LABEL'),
		);
	}
}
```

The view needs to be saved as `view.csv.php`.

## Toolbar Button

You can display a link to the csv view by using the toolbar button.

```
<?php

// If you are in the normal list view 'view.html.php' 
// you don't need to specify the link
$csv = RToolbarBuilder::createCsvButton();
```

