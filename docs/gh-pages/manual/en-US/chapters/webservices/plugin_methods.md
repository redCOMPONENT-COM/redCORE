## Webservice plugin methods

redCORE webservice API provides access to many method calls which you can trigger with your plugin.

### Where to place the plugin?

redCORE webservice API loads up `redcore` plugin group at the webservice API constructor, so you have the benefit of using this plugin group for your plugin. 
To use it you have to set your group in your plugin manifest file to `redcore` like this:

```
<extension version="3.0" type="plugin" group="redcore" method="upgrade">
...
</extension>
```

### How it must be formatted?

You must follow default Joomla plugin creation rules which you can find [here](http://docs.joomla.org/J3.x:Creating_a_Plugin_for_Joomla).

### How it is used?

Plugin may contain various methods that is used in webservice `API` to parse data or to completely replace original method from the API.
The rule is simple, if the method exists in plugin, it will be triggered after the original `API` method.

### How to access parent class?

Every method is triggered with parent object in the methods parameters as last parameter. You can use this to trigger parent functions. Ex:

```
public function isOperationAllowed(RApiHalHal $apiHal)
{
	$apiHal->setResources();

	// Do my logic after the original setResources method
}
```

### List of available methods

List of available methods are identical to Helper file list of methods. You can see them [here](chapters/webservices/helper_file.md)