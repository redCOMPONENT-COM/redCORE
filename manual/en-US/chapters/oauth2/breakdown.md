## Breakdown on "How to use" of redCORE OAuth2 Server step by step

### redCORE plugin

In redCORE plugin you have a option group `OAuth2 Server options` where you can enable this service on your site.
You can also change lifetime of `code` and `token` and other usefull options if you need them.

### Create OAuth Client

You must first create a new client in redCORE administration OAuth Clients.
When creating a new Client, you must enter `Client ID` which is basically the name of your application.
`Client Secret` will be auto generated when you create new Client, please keep it confidential.
In addition, you must enter a redirect URI to be used for redirecting users to for web server, browser-based, or mobile apps.

### Authorization types

The first step of OAuth2 is to get authorization from the user so please determine type you want to use.
For browser-based or mobile apps, this is usually accomplished by displaying an interface provided by the service to the user.

OAuth 2 provides several "grant types" for different use cases:

1. Authorization code for apps running on a web server
2. Implicit for browser-based or mobile apps
3. User credentials (password) for logging in with a username and password
4. Client credentials for application access
5. Refresh token for refreshing existing token
6. JWT Bearer (JSON Web Token Bearer) for browser-based or mobile apps

#### Get Authorization code

Create a "Log In" link sending the user to: http://YOUR-SITE/index.php?option=authorize&api=oauth2&response_type=code&client_id=your_client_id&redirect_uri=your_redirect_uri

The user will see the authorization prompt. If access is allowed, the service redirects the user back to your site with an auth code: http://YOUR-SITE/?code=somecodehere12345

#### Use Authorization code

Your server exchanges the auth code for an access token.

```
http://YOUR-SITE/index.php?option=token&api=oauth2
    &grant_type=authorization_code
    &code=your_authorization_code
    &redirect_uri=your_redirect_uri
    &client_id=your_client_id
    &client_secret=your_client_secret
```

`your_authorization_code` is the code you have fetched with previous step.

The server replies in json format with an access token

```
{
    "access_token": "78dc760116da3a3e0b28c326d4ec0d9d4340896f",
    "expires_in": "145600",
    "token_type": "Bearer",
    "scope": "site.read site.documentation",
    "refresh_token": "550f2884363736c0850555860604e3d85e10eec9"
}
```

#### Use Token to access protected webservices

redCORE OAuth2 supports a `Bearer` token that is passed along in an Authorization header and a URI query `access_token`.

```
<?php
$access_token = "YOUR_API_TOKEN";
$curl = curl_init("http://YOUR-SITE/index.php?api=hal");
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token));
curl_exec($curl);
?>
```

The above example would return all available webservices currently installed on the site which user can access.
