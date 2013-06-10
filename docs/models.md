# Models

# Form field errors

With RModelAdmin it's possible to get form validation errors per field.

If you have a form field like :

```xml
<field name="name"
	type="text"
	validate="boolean"
	message="Not a boolean"
/>
```

You can display validation error close to the field in the template :

```php
<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('name'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('name'); ?>
	</div>
	// Display the validation error or an empty string if no error.
	<?php echo $this->getModel()->getFieldError('name') ?>
</div>
```
