## Joomla user connection

Once the user has authenticated and redCORE issued an `access token` (such as with an `Authorize Controller`),
you will probably want to know which user an access token applies to when it is used. In webservices this is done automatically but you can write your own method.

You can do this by using the optional `user_id` parameter of `handleAuthorizeRequest`:

```
// A joomla user Id value that identifies the user
$userId = 1234;
$server->handleAuthorizeRequest($request, $response, $is_authorized, $userId);
```

That will save the `user Id` into the database with the `access token`. When the token is used by a client, you can retrieve the associated ID:

```
if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    $server->getResponse()->send();
    die;
}

$token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());
echo "Joomla User Id associated with this token is {$token['user_id']}";
```
