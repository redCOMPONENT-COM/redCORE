## Access token

To act on a users behalf and make calls from our API you will need an access token.
To get an access token you need to go through the access token flow and prompt the user (optional) to authorize your application to act on his or her behalf.
To begin, you will need to send the user to the authorization endpoint.

`http://YOUR-SITE/index.php?option=token&api=oauth2&client_id=your_client_id&client_secret=your_client_secret&redirect_uri=your_url&response_type={code or token}`

`your_client_id` should be set to your applications client id.

`your_client_secret` should be set to your applications client secret.

`redirect_uri` should be set to the URL that the user will be redirected back to after the request is authorized. `redirect_uri` should be set in the applications manager.

redCORE supports both `code` and `token` response types.

`Code` should be used for server side applications where you can guarantee that secrets will be stored securely. These Tokens do not have an expiration time.

`Token` should be used for client side applications.
These Tokens have an expiration time which can be set in redCORE plugin options and users will need to authenticate with your app once the token expires.

Tokens are used with the hash/fragment of the URL or set in Headers.
If the OAuth2 Server owner has denied access to your application, then response will include `access_denied`.

```
<?php
// The code from the previous authorization request
$code = $_GET['code'];
$curl = curl_init("http://YOUR-SITE/index.php?option=token&api=oauth2");
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, array(
    'client_id' => your_client_id,
    'client_secret' => your_client_secret,
    'redirect_uri' => your_redirect_url,
    'code' => $code,
    'grant_type' => 'authorization_code'
));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$myAuth = curl_exec($curl);
$accessToken = json_decode($myAuth);
$accessTokenKey = $accessToken->access_token;
?>
```

You are required to pass `client_id`, `client_secret`, and `redirect_uri`.
These parameters have to match the details for your OAuth2 Client credentials.

`redirect_uri` must match the `redirect_uri` used during the Authorize step.

`grant_type` has to be set to `authorization_code` since we are using Authorization code flow.

`code` must match the code you received in the redirect from Authorization code flow.


### Return value of Access token flow

If everything works correctly and the OAuth2 Server grants you authorization,
you will get back a JSON-encoded string containing the token and some basic information about the access token.

```
{
    "access_token": "78dc760116da3a3e0b28c326d4ec0d9d4340896f",
    "expires_in": "145600",
    "token_type": "Bearer",
    "scope": "site.read site.documentation",
    "refresh_token": "550f2884363736c0850555860604e3d85e10eec9"
}
```

You now have an access token which should be stored securely within your application.
This access token allows your application to act on the behalf of the user.

