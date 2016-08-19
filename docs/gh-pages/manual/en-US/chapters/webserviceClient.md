# Webservice Client class

This class enables easy connection and execution of the specific operation through Hal Webservices.
Object will upon execution generate token and watch for the expiration time before it refreshes it or create a new one.

Standard method names are:
- `post` - Used for new items or for task operations
- `get` - Used for read List and read Item operations
- `put` - Used for item update
- `delete` - Used for deletion of the item

Please note that some webservices does not support all operations and it is best to check first which operations can be executed.

### Parameters

All parameters are passed in a array. Please check webservice documentation on which parameters are required and which are not required.

### Options

Options for the class are as follows:

- `authorization[granttype]` - grant type for oAuth2 Authorization, it can also be several other types like password or refresh_token. Depending on this type, different options must be set.
- `authorization[clientid]` - client ID for oAuth2 Authorization. Required for any grant type.
- `authorization[clientsecret]` - client secret which is unique and auto generated for client ID. Required for any grant type.
- `authorization[username]` - username for oAuth2 Authorization if using grant type `password`. Also it is required if `basic` authentication type is used
- `authorization[password]` - password for oAuth2 Authorization if using grant type `password`. Also it is required if `basic` authentication type is used
- `authorization[authurl]` - authentication URL. This must be full url for the authentication server ex: `http://YOUR-SITE/index.php?option=token&api=oauth2`
- `authorization[tokenurl]` - authentication URL for token only. Set this only if server path is different for token generation. If not set then `authurl` is used. This must be full url for the token ex: `http://YOUR-SITE/index.php?option=token&api=oauth2`
- `authorization[authmethod]` - some servers allow only specific type of server method. If not set it will use `post` as default method action
- `authorization[redirecturi]` - for security reasons some servers require this option to be sent with the request
- `authorization[scope]` - if you want token to be restricted for only specific scope, then scope must be sent with the request
- `authorization[state]` - state is optional parameter which can be used for additional security layer as the server will return state parameter in the response
- `authorization[requestparams]` - other request parameters that can be sent together with the rest of the parameters. This must be set as an array.
- `authorization[userefresh]` - this option can turn off using refresh token feature. By default it is turned on.
- `authorization[basic]` - if authorization type is `basic` then it can use this option and set it as Authorization header. If not set then it will use `username` and `password` options
- `authorizationtype` - authorization type can be `token` (default), `basic`, `none`. Depending on this option it will provide authorization for the request.
- `authmethod` - authorization method can be set to `bearer` (default) or `get`. If set to `get` it will insert access token in to the URL
- `tokenparam` - for security reasons access token can be changed in some servers, and it can be changed here. If not set it will default to `access_token`
- `responseconverttoarray` - this option can turn off casting response from the json format to array. By default system will try to convert it unless it is set off in this option.
	
### Response

Response from the request is an object with these values:

- `code` - this is response code (integer) from the Webservice which can be used for further diagnose of the response request
- `headers` - an array of response headers with key => value chain information
- `body` - this is the response from the webservice

### Example

```

$options = array(
	'authorization' => array(
		'clientid' => 'testclientid',
		'clientsecret' => '9152ac0dfdcff7a3acde03e289104e3e05288d1a0278496c10319d388d199e49ef8be25b5d01bcd8',
		'authurl' => 'http://YOUR-SITE/index.php?option=token&api=oauth2'
	),
);
$options = new JRegistry($options);
$data = array(
	'name' => 'test ws',
	'catid' => 4
);
$client = new RWebservicesWebservice($options);
$response = $client->executeRemoteQuery(
	'http://YOUR-SITE/index.php?option=com_contact&webserviceVersion=1.0.0&webserviceClient=administrator&api=Hal',
	$data,
	$headers = array(),
	$method = 'post'
);

```

* Note: Contact Administrator Webservice must be installed for this example to work. Also Authorization parameters clientid and clientsecret must be set to their proper values.