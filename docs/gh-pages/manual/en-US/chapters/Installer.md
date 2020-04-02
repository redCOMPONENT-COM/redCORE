## Postflight tasks in Manifest

You can specify postflight tasks in the manifest to be executed.

### Simple Example

```
<postflight>
   <task name="hey" />
   <task name="hello" />
</postflight>
```

will execute the functions

```
<?php

protected function hey($type, $parent)
{
}

protected function hello($type, $parent)
{
}
```

defined in the component installer class.

### Example with parameters

You can also specify parameters value to be passed to the task. The parameters will be passed in the order they are defined in the manifest file.

```
    <postflight>
        <task name="coolTask">
            <parameter>1</parameter>
            <parameter>value</parameter>
        </task>
    </postflight>
```

will execute the function :

```
<?php

protected function coolTask($type, $parent, $integer, $string)
{
}
```

defined in the component installer class.

Note : the `$type` and `$parent` parameters are ALWAYS passed first to the task.

### Predefined tasks

List of the predefined tasks in the redCORE installer available to all components.

#### deleteMenu

This is a predefined task that deletes the menu item associated with the current component.

```
    <postflight>
        <task name="deleteMenu">
            <parameter>1</parameter>
        </task>
    </postflight>
```
The parameter corresponds to the client : `1` for the backend, `0` for the frontend.

If no parameter is specified, it will delete both backend and frontend menu items.

## Requirements check in Manifest

You can specify minimum requirement checks that will be preformed before installing the extension.
There are few predefined requirements check and there are PHP extensions check.
ex:

```
    <requirements>
        <php>5.3.0</php>
        <mysql>5.5.0</mysql>
        <extensions>
            <extension>gd</extension>
            <extension>cURL</extension>
        </extensions>
    </requirements>
```

### PHP version check

You can define minimum PHP version that your extension require or this extension will not be installable.
ex:
```
    <requirements>
        <php>5.3.0</php>
    </requirements>
```

### MySQL version check

You can define minimum MySQL version that your extension require or this extension will not be installable.
ex:
```
    <requirements>
        <mysql>5.5.0</mysql>
    </requirements>
```

### Joomla version check

You can define minimum Joomla version that your extension require or this extension will not be installable.
ex:
```
    <requirements>
        <joomla>3.4.0</joomla>
    </requirements>
```

### PHP Extensions check

You can define PHP extensions check that your extension require or this extension will not be installable.
ex:
```
    <requirements>
        <php>5.3.0</php>
        <mysql>5.5.0</mysql>
        <extensions>
            <extension>gd</extension>
            <extension>SimpleXML</extension>
            <extension>cURL</extension>
        </extensions>
    </requirements>
```
