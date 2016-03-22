## Webservice XML mapping file

This is where all the webservice magic is happening. 
You can create your own `XML mapping file`, deploy it with your extension and it will serve as a webservice for your extension.

### Client for Site or Administrator

There is a difference in the type of client for webservices in Joomla. Client that triggers in `administrator` and other that triggers in `site`.

XML files must be named accordingly. If webservice client is Joomla administration then XML file must start with `administrator.my_webservice.my_version`. 
If webservice client is Joomla site then XML file must start with `site.my_webservice.my_version`.

In the root element of XML file you **must** specify if this webservice is for `administrator` by using `client` attribute. 
Default value is `site` so you do not need to add this attribute it if your webservice is for `site`.

```
<apiservice client="administrator">
...
</apiservice>
```

### Meta information

```
...
	<name>Name of your webservice</name>
    <author>redCOMPONENT</author>
    <copyright>Copyright (C) 2008 - 2014 redCOMPONENT.com. All rights reserved.</copyright>
    <description>HAL configuration for my custom Webservice in Administration</description>
...
```

`name` of the webservice is what users will see when they install your webservice and in the documentation. It is not used for identifying your webservice.
 
`author` is the owner of this file

`copyright` information about copyrighting

`description` of the webservice is what users will see when they install your webservice and as description in the documentation.


### Configuration

Configuration is important part of the `XML` because it tells redCORE API webservice identity `name` and `version`.

```
...
	<config>
        <name>contact</name>
        <version>1.0.0</version>
        <authorizationAssetName>com_contact</authorizationAssetName>
    </config>
...
```

`config -> name` is the identity of the webservice and must be unique among the client webservices (`site` and `administrator`) unless it is another version of webservice. 
There can be one with the same name in each of the clients. Read more about how does API trigger webservice with its identity [here](chapters/webservices/breakdown.md). 
Name will be used for automatic model and table instance in CRUD methods and for Api helper class instance.

`config -> version` is the other part of the identity. There can be many version of the same webservice installed and working together. 
This is good when you have a client who works on an older version of the webservice and cannot upgrade immediately, 
you can have older for old clients, and newer with new features. Versions are formatted using `Semantic Versioning`, you can read more about it [here](http://semver.org/).

`config -> authorizationAssetName` is used with Joomla authorization when preforming checks against ACL (if not authorized by scope). 
In redCORE plugin options you can choose between Joomla ACL and using scopes. If using Joomla ACL then this is important to test permissions against proper asset name.


### Resources

Resources group is used like a container for easier resource definition. 
If you set it up in main element, it will be available in each operation just by defining same `displayName`.

```
...
    <resources>
        <resource displayGroup="_links" displayName="base" fieldFormat="/" linkTitle="Default page" />
        <resource displayGroup="_links" displayName="categories" fieldFormat="/index.php?option=categories&amp;id={catid}" />
        <resource displayName="result" fieldFormat="{result}" transform="boolean" />
    </resources>
...
```

Resources can be grouped with "resourceSpecific" attribute, default value is "rcwsGlobal" and it is optional.

### All Resource attributes

Resources have many attributes which you can define to set your resource properly.

```
...
    <resources>
        <resource resourceSpecific="mySpecificListItem" displayGroup="_links" displayName="documentation" fieldFormat="/index.php?option=contact&amp;format=doc#{rel}" linkTitle="Documentation" linkName="contact" hrefLang="" linkTemplated="true" linkRel="curies" />
                
        <resource displayGroup="_links" displayName="categories" fieldFormat="/index.php?option=categories&amp;id={catid}" />
        <resource displayName="result" fieldFormat="{result}" transform="boolean" />
    </resources>
...
```

##### Default Attributes

`resourceSpecific` is by default set to `rcwsGlobal`, resources can be grouped by specific name if needed (usually not needed). 
Ex: You want to separate resource in embedded resource of the list resource.

`displayGroup` is a group that will be rendered in the output document as per HAL specification. Common usage for this is "_links".

`displayName` is a identity name for this resource and it is used as a Reference key in HAL document and must be unique. 

`fieldFormat` is a definition on how you want your resource to output its value. You can use static text, data variable, text and data variable or multiple variables with text. 
It depends on your usage but you can format your field like this: 
`/index.php?option=mywebservice&amp;id={id}&amp;catid={catid}` where `{id}` and `{catid}` will be replaced by data value if that data exists.

`transform` is used to transform `fieldFormat` to specific data format. You can read more about formats [here](chapters/webservices/transform.md).

##### _links only attributes

`linkTitle` is used only in `_links` group. Value of that attribute will be used for Title of that resource link. 

`linkName` is used only in `_links` group. Value of that attribute will be used for Name of that resource link. 

`hrefLang` is used only in `_links` group. Value of that attribute will be used for Language of that resource link. 

`linkTemplated` is used only in `_links` group. Value of that attribute will be used to identify if this link is templated. 

`linkRel` is used only in `_links` group. Value of that attribute will be used to as Reference key for HAL of that resource link. This is used mainly to define `curies` links.

### Operations in Webservice

Operations are very important in defining your webservice. If one of the operation is commented out or missing, it will not be possible to use it. 
There is already flow for using `CRUD` (Create, Read, Update and Delete) operations on the data but we have extended this with `Tasks` and `Documentation`. 

```
	<operations>
		...
	</operations>
```

#### Operation Create

Operation Create is part of the CRUD flow. This operation will perform Creation (or Updating) of the item. 
This operation will process posted data and call method for saving the item.

```
...
	<create authorization="core.create" dataMode="" optionName="com_contact" modelClassName="" modelClassPath="" isAdminClass="true" functionName="" functionArgs="">
		<fields>
			...
		</fields>

		<resources>
			...
		</resources>
		<description><![CDATA[ This operation creates new Contact item. ]]></description>
	</create>
...
```

`functionName` in Create Operation is `save` by default. 

Read more about Operation attributes [here](#operation-attributes), and Operation child elements [here](#operation-child-elements).

#### Operation Read

Operation Read is part of the CRUD flow and it is break down in two parts: `list` and `item`. 
Both list and item have their own separate `Operation Attributes`, `Fields` and `Resources`. 
This operation will output HAL document with data specified within your resources.

##### Operation Read - List
```
...
	<read>
		<list dataMode="" optionName="com_contact" modelClassName="contacts" modelClassPath="" isAdminClass="true" functionName="" paginationFunction="getPagination" totalFunction="getTotal">
			<fields>
				...
			</fields>
	
			<resources>
				...
			</resources>
			<description><![CDATA[ This operation lists Contact items. ]]></description>
		</list>
	</read>
...
```

`functionName` in Read - List Operation is `getItems` by default.

`paginationFunction` in Read - List Operation is `getPagination` by default.

`totalFunction` in Read - List Operation is `getTotal` by default.

`Read -> List` is default landing page for webservice if no `item Id` is provided. In List items HAL document you can embed items with some specific fields. 
If you specify resource with attribute `resourceSpecific="listItem"` you can put that resource into the embedded resources. Ex:

```
<resource resourceSpecific="listItem" displayName="name" fieldFormat="{name}" />
```

You can see how output for one specific HAL list document looks like [here](chapters/webservices/output_read_list.md).

Standard list output consists of Pagination options (`first`, `previous`, `next`, `last`, `all`, `first`, `limit`, `filter`, `sort`) and embedded items available for this page.

Read more about Operation attributes [here](#operation-attributes), and Operation child elements [here](#operation-child-elements).

##### Operation Read - Item
```
...
	<read>
		<item dataMode="" optionName="com_contact" modelClassName="" modelClassPath="" isAdminClass="true" functionName="">
			<fields>
				...
			</fields>

			<resources>
				...
			</resources>
			<description><![CDATA[ This operation displays one Contact item. ]]></description>
		</item>
	</read>
...
```

`functionName` in Create Operation is `getItem` by default. 

`Read -> Item` is displayed only if `Item Id` is provided. In Item HAL document you can have various tasks specifically for this item. 

You can see how output for one specific HAL Item document looks like [here](chapters/webservices/output_read_item.md).

Read more about Operation attributes [here](#operation-attributes), and Operation child elements [here](#operation-child-elements).

#### Operation Update

Operation Update is part of the CRUD flow. This operation will perform Updating (or Creation) of the item. 
This operation will process posted data and call method for saving the item.

```
...
	<update authorization="core.edit,core.edit.own" dataMode="" optionName="com_contact" modelClassName="" modelClassPath="" isAdminClass="true" functionName="" functionArgs="">
		<fields>
			...
		</fields>

		<resources>
			...
		</resources>
		<description><![CDATA[ This operation updates Contact item. ]]></description>
	</update>
...
```

`functionName` in Update Operation is `save` by default. 

Read more about Operation attributes [here](#operation-attributes), and Operation child elements [here](#operation-child-elements).

#### Operation Delete

Operation Delete is part of the CRUD flow. This operation will perform Deletion of the item(s). 
This operation will process posted data and call method for deleting the item.

```
...
	<delete authorization="core.delete" dataMode="" optionName="com_contact" modelClassName="" modelClassPath="" isAdminClass="true" functionName="" functionArgs="id{int}">
		<fields>
			...
		</fields>

		<resources>
			...
		</resources>
		<description><![CDATA[ This operation deletes Contact item. Expected data: id of the contact. ]]></description>
	</delete>
...
```

`functionName` in Delete Operation is `delete` by default. 

Read more about Operation attributes [here](#operation-attributes), and Operation child elements [here](#operation-child-elements).

#### Operation Task

Operation Task is a container for multiple task you can have in your webservice. Each task have its own `Operation Attributes`, `Fields` and `Resources`.
This operation will process posted data and call method for deleting the item.

Each task can be preformed in separate class and redCORE webservice API will act accordingly.

```
...
	<task>
		<publish authorization="core.edit,core.edit.own" dataMode="" optionName="com_contact" modelClassName="" modelClassPath="" isAdminClass="true" functionName="" functionArgs="id{int}">
			<fields>
				...
			</fields>

			<resources>
				...
			</resources>
			<description><![CDATA[ This task enables you to set specific Contact as published. Expected data: id of the contact. ]]></description>
		</publish>
	</task>
...
```

`functionName` in Task Operation is equal to the task name. 
In the example above, redCORE webservice API would call method `publish` by default. 

Task Operations have one additional attribute: `useOperation` which is used to redirect operation to some of the existing ones: Create, Read, Update, Delete. 
The redCORE webservice API will act as one of the default CRUD Operations is called.

Read more about Operation attributes [here](#operation-attributes), and Operation child elements [here](#operation-child-elements).

#### Operation Documentation

Operation Documentation is used to show documentation about the webservice through HAL document and as a separate link. 
`Operation Attributes` do not apply to Documentation operation, instead it offers its own set of attributes:

```
...
	<documentation authorizationNeeded="false" source="auto" url="" />
...
```

##### Documentation attributes

`authorizationNeeded` is used to separate operations that require permissions to be used and those that do not need authorization. 
This is mainly used for `Site` tasks or documentation. 

`url` is used together with `source` attribute to specify URL of the webservice documentation if you already have your documentation available in some other format ex. `url="http://www.sample.com"`

`source` attribute is used to signal redCORE webservice API where to get Documentation for this Webservice. It has several options:

- `auto` - documentation is auto generated with redCORE webservice API using this XML as template
- `url` - documentation will be loaded from fixed url using attribute `url`
- `none` - documentation will not be included in HAL curries (not recommended)

Note: You can access documentation with `HAL browser` clicking on the documentation icon or directly in your browser by using following URL format

```
http://YOUR-SITE/index.php?option=com_contact&api=Hal&format=doc
```

Notice the ending of the URI `&format=doc` which signals redCORE webservice API to render documentation of the specific webservice.

Note. _Every element have a `description` child element where you can set description of that specific element. This description will be used when creating documentation for your webservice._

#### Operation attributes <a id="operation-attributes"></a>

`authorizationNeeded` is used to separate operations that require permissions to be used and those that do not need authorization. 
This is mainly used for `site` tasks or documentation. 

`dataMode` can be model, helper or table. Default is model. If you use "table" then `tableName` attribute must be set. 
If it is set to "helper" it will run operation on webservice helper class (that is shipped with webservice).

`authorization` is used to check permission if system is using Joomla ACL for authorization of operations.

`optionName` is used to add include path to model and table classes. Include paths will depend if you set `isAdminClass` to true or not.

`modelClassName` is the name used when creating object. By default it will be instanced using `optionName` and webservice `name` identifier. 
If your class name is different than Joomla default naming convention then you can set it directly to this attribute (Ex. `ContactModelContact`)

`modelClassPath` is the direct path to your model class. If you set this attribute to specific php file, it will be included before creating new instance of the object.

`isAdminClass` is set to False by default. If you set it to True it will load model from the `administrator` client and it will include paths to the admin models.

`functionName` is the method name that will be called on the instanced object.

`functionArgs` If this attribute is defined, only arguments defined there will be pulled from posted data and passed to the function. 
Default is to send all posted data in one array.

`validateData` can be set as: none, form, function. Default value is "none". If option "form" is selected then data will be validated against model form. 
If option "function" is selected then validateDataFunction attribute will be used to preform validation.

`validateDataFunction` To use this feature you must set your `validateData` attribute to "function". 
Defined function will be used to check data before passing it to the operation. Default function name if not set is "validate". 

`tableName` is used to define table in your database that you want to attach to. 
To use this feature you must set your operation `dataMode` attribute to "table". 
With this you can preform all CRUD functions without having model set. 

#### Operation child elements <a id="operation-child-elements"></a>
`fields` group is used to further process posted data.

`resources` group is used to define which resources will be rendered in the HAL document. 

`description` is used when redCORE API is generating documentation for this webservice. 

#### Operation fields <a id="operation-fields"></a>

Operation fields have a different attributes depending for `read` and different for every other operation. 
This is the list of attributes used in operation fields:

`name` is used to identify database column (or field name) when getting or setting an item from database.

`transform` is used to transform the data in specific format. Default value for transform is `string`. 
You can read more about transform [here](chapters/webservices/transform.md)

`isFilterField` is used only in `read list` operation. To use this feature you must set in `read list` operation `dataMode` attribute to "table". 
With this attribute you can set this field to be a filter field. 
Example of usage: `http://YOUR-SITE/index.php?option=com_contact&filter[catid]=4&api=Hal`

`isSearchableField` is used only in `read list` operation. To use this feature you must set in `read list` operation `dataMode` attribute to "table". 
With this attribute you can set this field to be a searchable field. 
It will gather all defined fields with this attribute and place it in the same _WHERE_ clause with a _OR_ condition.
Example of usage: `http://YOUR-SITE/index.php?option=com_contact&filter[search]=test&api=Hal`

`isHiddenField` is used only in `read list` operation. To use this feature you must set in `read list` operation `dataMode` attribute to "table". 
With this attribute you can set this field not to be loaded from the database. 
This is usefull if you have large database tables and you do not want to load all fields with every request. 

`isPrimaryField` is used only in `read item` operation. To use this feature you must set in `read list` operation `dataMode` attribute to "table". 
With this attribute you can set this field to be a primary field instead of default `id`. 
**If your table primary key is `id` then you do not have to define this attribute anywhere. **

`isRequiredField` is used in every operation. 
With this attribute you can set this field to be a required an it will be checked before preforming an operation. 

`defaultValue` if set it will ensure that your field have specified value defined if the field is not set at all.
