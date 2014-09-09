## Float

Validates a float value.

```
<field 
      name="price" 
      type="text"
      validate="float"
      message="Invalid value for price"
/>
```

## Integer

Validates an integer value.

`signed` : True to allow signed integers like +10, -100. False by default.

```
<field 
      name="price" 
      type="text"
      validate="integer"
      signed="true" 
      message="Invalid value for price"
/>
```

## Date

Validates a date against a format.

```
<field 
      name="name" 
      type="text"
      validate="date"
      format="Y-m-d H:i:s"
      message="Invalid date format for name"
/>
```
Supports all formats supported by DateTime.

## Ranges

All range rules work with min and/or max values specified.

### RangeDate

Validates that a date is superior than min and inferior than max (if specified).
`min` and `max` must be in the same format than `format`.

```
<field 
      name="name" 
      type="text"
      validate="rangedate"
      format="Y-m-d H:i:s"
      min="2012-05-06 14:00:30"
      max="2013-05-06 14:00:30"
      message="Invalid date format for name"
/>
```

### RangeLenght

Validates that a string has a lenght superior than `min` and inferior than `max` (if specified).

```
<field 
      name="name" 
      type="text"
      validate="rangelenght"
      min="2"
      max="10"
      message="Invalid name"
/>
```

### RangeValue

Validates that an integer or float has a value superior than `min` and inferior than `max` (if specified).

```
<field 
      name="price" 
      type="text"
      validate="rangevalue"
      min="2"
      max="10.5"
      message="Invalid price"
/>
```