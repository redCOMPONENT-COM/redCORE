## User credentials (password)

The `User Credentials` grant type (Resource Owner Password Credentials) is used when the user has a trusted relationship with the client,
and so can supply credentials directly.

[User credentials grant type on tools.ietf.org](http://tools.ietf.org/html/rfc6749#section-4.3)

### User Credentials Protocol

     +----------+
     | Resource |
     |  Owner   |
     |          |
     +----------+
          v
          |    Resource Owner
         (A) Password Credentials
          |
          v
     +---------+                                  +---------------+
     |         |>--(B)---- Resource Owner ------->|               |
     |         |         Password Credentials     | Authorization |
     | Client  |                                  |     Server    |
     |         |<--(C)---- Access Token ---------<|               |
     |         |    (w/ Optional Refresh Token)   |               |
     +---------+                                  +---------------+

The flow illustrated includes the following steps:

   **(A)** -  The resource owner provides the client with its username and password.

   **(B)** -  The client requests an access token from the authorization server's token endpoint by including the credentials received from the resource owner.
   When making the request, the client authenticates with the authorization server.

   **(C)** -  The authorization server authenticates the client and validates the resource owner credentials, and if valid, issues an access token.

### Implementation

If you are the client owner, you can authenticate with the `User Credentials` Grant type.
This will allow you to skip the authorization step of authenticating the user, and logging in directly to your site with username and password.

**We highly recommend using HTTPS with OAuth2 Server with Password Grant Type for additional security.**

This is an example of how you can get started with using both these features:

```
$curl = curl_init("http://YOUR-SITE/index.php?option=token&api=oauth2");
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, array(
    'client_id' => your_client_id,
    'client_secret' => your_client_secret_key,
    'username' => your_joomla_username,
    'password' => your_joomla_password,
    'grant_type' => 'password'
));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$myAuth = curl_exec($curl);
$accessToken = json_decode($myAuth);
$accessTokenKey = $accessToken->access_token;
```

These are only available to you as the owner of the application, and not to any other user and you should use this method with great care.

You are required to pass `client_id`, `client_secret`, `username` and `password`.
These parameters have to match the details for your OAuth2 Client credentials and Joomla user credentials.

`grant_type` has to be set to `password` since we are using User credentials flow.


### Return value of User credentials (password) flow

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
