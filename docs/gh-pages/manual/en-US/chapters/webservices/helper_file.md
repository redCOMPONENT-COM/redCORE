## Webservice helper file

With every webservice you can add your own `helper file` that will be loaded at the same time your webservice is called.

### How it must be formatted?

Helper files must have exact same name as the webservice they belong.
So if the webservice file name is `administrator.contact.1.0.0.xml` then your helper file must be named the same `administrator.contact.1.0.0.php`.

Class name must begin with `RApiHalHelper` then if your webservice is for `administration` then you have to add `Administrator` to the Class name,
likewise if your webservice is for `site` then you have to add `Site` to the Class name.
Last name you have to add is your webservice name to the Class name (ex. Contact) and it have to start with Capital letter without any special characters.

Final look of the class name would be like this:

```
// This is helper file for Contact webservice in Administration
class RApiHalHelperAdministratorContact
{
}
```

```
// This is helper file for Contact webservice in Site
class RApiHalHelperSiteContact
{
}
```

### How it is used?

Helper file may contain various methods that is used in webservice `API` to parse data or to completely replace original method from the API.
The rule is simple, if the method exists in helper file, it will be called instead of original `API` method.

### How to access parent class?

Every method is triggered with parent object in the methods parameters as last parameter. You can use this to trigger parent functions. Ex:

```
public function isOperationAllowed(RApiHalHal $apiHal)
{
	$apiHal->isOperationAllowed();

	// Do my logic after the original isOperationAllowed method
}
```

### List of available methods

1. **isOperationAllowed** - Checks if operation is allowed from the configuration file
```
	/**
	 * Checks if operation is allowed from the configuration file
	 *
	 * @return object This method may be chained.
	 *
	 * @throws  RuntimeException
	 */
	public function isOperationAllowed(RApiHalHal $apiHal){}
```
2. **setResources** - Set resources from configuration if available
```
	/**
	 * Set resources from configuration if available
	 *
	 * @return  void
	 *
	 * @since   1.2
	 */
	public function setResources(RApiHalHal $apiHal){}
```
3. **apiDefaultPage** - Execute the Api Default Page operation.
```
	/**
	 * Execute the Api Default Page operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	public function apiDefaultPage(RApiHalHal $apiHal){}
```
4. **apiCreate** - Execute the Api Create operation.
```
	/**
	 * Execute the Api Create operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	public function apiCreate(RApiHalHal $apiHal){}
```
5. **apiDelete** - Execute the Api Delete operation.
```
	/**
	 * Execute the Api Delete operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	public function apiDelete(RApiHalHal $apiHal){}
```
6. **apiUpdate** - Execute the Api Update operation.
```
	/**
	 * Execute the Api Update operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	public function apiUpdate(RApiHalHal $apiHal){}
```
7. **apiTask** - Execute the Api Task operation.
```
	/**
	 * Execute the Api Task operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	public function apiTask(RApiHalHal $apiHal){}
```
8. **apiDocumentation** - Execute the Api Documentation operation.
```
	/**
	 * Execute the Api Documentation operation.
	 *
	 * @return  mixed  RApi object with information on success, boolean false on failure.
	 *
	 * @since   1.2
	 */
	public function apiDocumentation(RApiHalHal $apiHal){}
```
9. **processPostData** - Process posted data from json or object to array
```
	/**
	 * Process posted data from json or object to array
	 *
	 * @param   mixed             $data           Raw Posted data
	 * @param   SimpleXMLElement  $configuration  Configuration for displaying object
	 *
	 * @return  mixed  Array with posted data.
	 *
	 * @since   1.2
	 */
	public function processPostData($data, $configuration, RApiHalHal $apiHal){}
```
10. **setForRenderList** - Set document content for List view
```
	/**
	 * Set document content for List view
	 *
	 * @param   array             $items          List of items
	 * @param   SimpleXMLElement  $configuration  Configuration for displaying object
	 *
	 * @return void
	 */
	public function setForRenderList($items, $configuration, RApiHalHal $apiHal){}
```
11. **setForRenderItem** - Set document content for Item view
```
	/**
	 * Set document content for Item view
	 *
	 * @param   object            $item           List of items
	 * @param   SimpleXMLElement  $configuration  Configuration for displaying object
	 *
	 * @return void
	 */
	public function setForRenderItem($item, $configuration, RApiHalHal $apiHal){}
```
12. **prepareBody** - Prepares body for response
```
	/**
	 * Prepares body for response
	 *
	 * @param   string  $message  The return message
	 *
	 * @return  string	The message prepared
	 *
	 * @since   1.2
	 */
	public function prepareBody($message, RApiHalHal $apiHal){}
```
13. **loadModel** - Load model class for data manipulation
```
	/**
	 * Load model class for data manipulation
	 *
	 * @param   string            $elementName    Element name
	 * @param   SimpleXMLElement  $configuration  Configuration for current action
	 *
	 * @return  mixed  Model class for data manipulation
	 *
	 * @since   1.2
	 */
	public function loadModel($elementName, $configuration, RApiHalHal $apiHal){}
```
14. **setApiOperation** - Set Method for Api to be performed
```
	/**
	 * Set Method for Api to be performed
	 *
	 * @return  RApi
	 *
	 * @since   1.2
	 */
	public function setApiOperation(RApiHalHal $apiHal){}
```
