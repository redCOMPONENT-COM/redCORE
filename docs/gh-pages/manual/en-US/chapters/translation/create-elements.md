## Create translation elements

redCORE translation consists of xml files that typically reside in the `media/redcore/translation` folder, or for some some extension in their own media folder (it depends on the extension).  To explore the basic Joomla translated elements go to the `media/redcore/translation` so you can create your elements from the existing ones.

Each XML file corresponds to a table of the component in the database.

The basic structure of an XML file is as follows (all the data enclosed by {} must be customized - explanation below):

```
<?xml version="1.0" encoding="UTF-8"?>
<contentelement type="contentelement">
    <name>{Translation element}</name>
    <author>{Author}</author>
    <copyright>{Copyright}</copyright>
    <version>{Version number}</version>
    <description>{Translation Description}</description>
    <reference>
        <table name="{table}">
            <field type="referenceid" name="id_field" translate="0">ID</field>
            <field type="{type}" name="{field_name}" translate="1" >{Field}</field>
        </table>
        <component>
            <form>com_{extension}#{table}#cid#task#!edit</form>
        </component>
    </reference>
</contentelement>
```

### Field explanation:

- {Translation element}: A descriptive name for the translation element
- {Author}: Author of this translation
- {Copyright}: Copyright of the translation
- {Version number}: Version of the file (ex: 1.0.0)
- {Translation description}: A descriptive translation for this translation element
- {table}: The Joomla table without the prefix (ex: `content` or `menus`)
- {type}: The field type, which has the following options:
	- `referenceid`: The reference or id field.  Typically the Primary Key to identify the table
	- `text`: A simple text field
	- `titletext`: A regular text field identifying the title of each row
	- `images`: A list of images
	- `hiddentext`: A hidden field that needs to be in the translation table but hidden from the end user
	- `readonlytext`: A text that needs to be translated but will not enabled editing
	- `textarea`: A long text (text area) field
	- `htmltext`: A long text with an HTML (Wysiwyg) editor
	- `params`: A json fields with parameters
- {field_name}: The field name as listed in the table
- {Field}: The descriptive name of the field (to be presented to the end users)
- {extension}: The extension name (ex. `com_content`)
