# SOAP Client class

This class enables easy connection and execution of the specific operation through SOAP protocol.

Standard method names are:
- create
- readList
- readItem
- update
- delete
- task_TASKNAME

Where `TASKNAME` is a name of the task which needs to be executed.

Please note that some webservices does not support all operations and it is best to check first which operations can be executed.

### Parameters

All parameters are passed in a array. Please check webservice documentation on which parameters are required and which are not required.

### Options

Options for the class are as follows:

- `soapversion` - which should have value of `SOAP_1_2` if it is not set as default in your SOAP Client
- `wsdlcache` - with this option you can set various settings for WDSL caching according to SOAP Client documentation. Default is `WSDL_CACHE_NONE` if it is not set.
- `authorization[username]` - login name for SOAP authorization
- `authorization[password]` - password value for SOAP authorization
- `compression` - if set to `SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | 1` it will compress request and response data for the given operation and making it much faster in the process.
- `debug` - if set to `true` it will output request and response headers for the given operation.

### Example

```

$options = array(
	'authorization' => array(
		'username' => 'super',
		'password' => 'admin'
	),
	'debug' => true,
);
$options = new JRegistry($options);
$data = array(
	'name' => 'test soap',
	'catid' => 4
);
$client = new RWebservicesSoap($options);
$response = $client->executeRemoteQuery(
	'http://YOUR-SITE/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&api=soap&wsdl',
	$data,
	$headers = array(),
	$method = 'create'
);

```

* Note: Contact Administrator Webservice must be installed for this example to work. Also Authorization parameters username and password must be set to their proper values.