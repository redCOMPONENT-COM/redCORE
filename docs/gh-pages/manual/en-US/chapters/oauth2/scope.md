## Scope

When creating or editing a OAuth Client in redCORE administration you can see available scopes you can assign to the Client.
You can change the way Joomla checks for user permissions in webservices in redCORE plugin options.

### Configure your Scope

The use of Scope in an OAuth2 application is often key to proper permissioning.
Scope is used to limit the authorization granted to the client by the resource owner.
The most popular use of this is Facebook's ability for users to authorize a variety of different functions to the client ("access basic information", "post on wall", etc).

In this library, scope is handled by implementing `OAuth2\Storage\ScopeInterface`. This can be done using your own implementation, or by taking advantage of the existing OAuth2\Storage\Memory class:

```
// Configure your available scopes
$defaultScope = 'basic';
$supportedScopes = array(
  'basic',
  'postonwall',
  'accessphonenumber'
);
$memory = new OAuth2\Storage\Memory(array(
  'default_scope' => $defaultScope,
  'supported_scopes' => $supportedScopes
));
$scopeUtil = new OAuth2\Scope($memory);

$server->setScopeUtil($scopeUtil);
```

### Validate your scope

Configuring your scope in the server class will ensure requested scopes by the client are valid.
However, there are two steps required to ensure the proper validation of your scope.
- First, the requested scope must be exposed to the resource owner upon authorization.
In this library, this is left 100% to the implementation.
The UI must make clear the scope of the authorization being granted.
- Second, the resource request itself must specify what scope is required to access it:

```
// http://YOUR-SITE/administrator/index.php?option=contact&api=Hal
$request = OAuth2\Request::createFromGlobals();
$response = new OAuth2\Response();

// This resource requires "read" scope for webservice Contact from administrator
$scopeRequired = 'administrator.contact.read';

if (!$server->verifyResourceRequest($request, $response, $scopeRequired)) {
  // if the scope required is different from what the token allows, this will send a "401 insufficient_scope" error
  $response->send();
}
```

### State

The `state` parameter is required by default for authorize redirects.
This is the equivalent of a CSRF token, and provides session validation for your Authorize request.
See the OAuth2.0 Spec for more information on state.

This is enabled by default for security purposes, but you can remove this requirement when you configure your server in redCORE plugin options by disabling `enforce_state` option

### Using Multiple Scopes

You can request multiple scopes by supplying a space-delimited (but url-safe) list of scopes in your authorize request. It will look like this:

```
http://YOUR-SITE/index.php?option=authorize&api=oauth2
   &client_id=your_client_id
   &response_type=code
   &scope=administrator.contact.read%20administrator.contact.update%20administrator.contact.delete

Note: Extra line breaks are for readability only
```

This will create an authorization code with the following four scopes: "administrator.contact.read", "administrator.contact.update" and "administrator.contact.delete"

These three scopes will then be validated against the available scopes using the OAuth2\ScopeUtil class to ensure they exist.
If you receive the error invalid_scope: An unsupported scope was requested, this is because you need to set your available scopes on your server object, like so:

```
$scope = new OAuth2\Scope(array(
  'supported_scopes' => array('administrator.contact.read', 'administrator.contact.update', 'administrator.contact.delete')
));
$server->setScopeUtil($scope);
```
