## Transform field formats

redCORE webservices API provide a way to transform your data before rendering it or passing it to the methods on CRUD + Tasks operations.

Benefit of using this is that you can cast from default string type to another type of data. Ex:

```
	<field name="published" transform="int" defaultValue="1" />
```

This will enforce data type `int` on the field `published`. 
We have further defined this field with `defaultValue` which will ensure that your field have value 1 if the field is not set at all.

Note. _Default value for transform is `string`. This is in case no transform attribute is set or if the transform type is not available._

### Where can transform be used?

Transforming of the data can be used in these cases:

1. **Resources** - you can set resource format mappings to the specific type. Ex:

	```
		<resource displayName="id" fieldFormat="{id}" transform="int" />
	```

2. **Fields** - you can set field format mappings to the specific type. Ex:

	```
		<field name="params" transform="array" />
	```

3. **Function parameters** - you can set parameter format mappings to the specific type. 
Syntax is a bit different than `resource` and `field` because all fields are defined inline separated by comma `,` Ex:

	```
		<featured authorization="core.edit,core.edit.own"  optionName="com_contact" isAdminClass="true" functionArgs="id{int},1{value}" />
	```

In our example above you can see 2 parameters

`id{int}` will be transformed to Int before passing the parameter to the function

`1{value}` will use value 1 for the parameter of the function

Note. _If type for example {int} is not defined, system will use default transform type `string`._

### Transform types

There are many types we have created you can use:

1. `array` - this type will get set specific field as an array. This is useful for obtaining data through JSON. Ex. `params` is an array of data

1. `boolean` - this type will return boolean value `true` if data is "true" or "1". It will return `false` if data is "false" or "0"

1. `datetime` - this type will return ISO 8601 format of DateTime data for `resource` and it will transform from ISO 8601 to MySQL data string when using `field`

1. `float` - this type will return floating position of the element depending on the data. Ex: `left`, `right`, `none`, `global` or `undefined`

1. `int` - this type will return data Integer

1. `position` - this type will return position

1. `state` - this type will return state in text version. Ex: `unpublished`, `published`, `undefined` for their respective data integer.

1. `string` - this type will return data string

1. `target` - this type will return target or the URL link from its respective data integer. Ex: `global`, `parent`, `new`, `popup`, `modal`, `undefined`

1. `value` - this type will return `field`, `resource` or `function parameter` name and pass it as an value. 
This is mainly used when you want to pass fixed values as a parameter to the method. Ex: 

	```
	<featured authorization="core.edit,core.edit.own"  optionName="com_contact" isAdminClass="true" functionArgs="id{int},1{value}" />
	```

	In the example above system will pass item Id of the contact and parameter with fixed value "1".

1. `ynglobal` - this type will return data in text version from its respective data integer. Ex: `global`, `no`, `yes`, `undefined` for their respective data integer.

