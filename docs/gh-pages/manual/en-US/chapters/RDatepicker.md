This is a jQuery-UI datepicker field.

It supports almost all the native [datapicker options](http://api.jqueryui.com/datepicker/)

### Example 1: 
Sample field call:

```
	<field
		name="inline_to_date"
		type="rdatepicker"
		class="js-to-inline-calendar"
	/>
```

### Example 2: 
Force the week to start on monday (`firstDay`), set the default date to today + 60 days (`defaultDate`) and specify the date format to yy-mm-dd (`dateFormat`):
```
	<field
		name="inline_to_date"
		type="rdatepicker"
		defaultDate="+60"
		firstDay="1"
		dateFormat="yy-mm-dd"
		class="js-to-inline-calendar"
	/>
```
### Example 3: 
Same as Example 2 but in this case the calendar will be always shown (`inline = true`) and it will update a hidden field called `return_date` (with `altField="#jform_return_date"` that is the DOM id of the field) 
```
	<field
		name="inline_to_date"
		type="rdatepicker"
		defaultDate="+60"
		inline="true"
		firstDay="1"
		dateFormat="yy-mm-dd"
		altField="#jform_return_date"
		class="js-to-inline-calendar"
	/>
	<field
		name="return_date"
		type="hidden"
		class="js-return-date"
	/>
```