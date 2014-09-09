## Postflight tasks in Manifest

You can specify postflight tasks in the manifest to be executed.

### Simple Example

```xml
<postflight>
   <task name="hey" />
   <task name="hello" />
</postflight>
```

will execute the functions

```php
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

```xml
    <postflight>
        <task name="coolTask">
            <parameter>1</parameter>
            <parameter>value</parameter>
        </task>
    </postflight>
```

will execute the function :

```php
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

```xml
    <postflight>
        <task name="deleteMenu">
            <parameter>1</parameter>
        </task>
    </postflight>
```
The parameter corresponds to the client : `1` for the backend, `0` for the frontend.

If no parameter is specified, it will delete both backend and frontend menu items.