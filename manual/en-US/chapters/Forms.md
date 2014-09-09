## Validation

### Get a single field error message

With RModelAdmin it's possible to get form validation errors per field.

If you have a form field like :

```
<field name="name"
	type="text"
	validate="boolean"
	message="Not a boolean"
/>
```

You can display validation error close to the field in the template :

```
<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('name'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('name'); ?>
	</div>
	<!-- Display the validation error or an empty string if no error -->
	<?php echo $this->getModel()->getFieldError('name') ?>
</div>
```

### Tags inside error messages

It's possible to reference form field attributes in a validation error message :

```
<field
   name="number"
   type="text"
   validate="integer"
   min="5"
   max="8"
   message="Invalid field {name} with value {value}. Must be between {min} and {max}"
/>
```