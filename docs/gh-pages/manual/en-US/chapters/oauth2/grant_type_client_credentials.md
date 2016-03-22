## Client credentials grant type

The `Client Credentials` grant type is used when the client is requesting access to protected resources under its control,
such as their website URL or application icon, or they may wish to get statistics about the users of the app.
In this case, applications need a way to get an access token for their own account, outside the context of any specific user.
OAuth provides the client_credentials grant type for this purpose.

### Client Credentials Protocol

     +---------+                                  +---------------+
     |         |                                  |               |
     |         |>--(A)- Client Authentication --->| Authorization |
     | Client  |                                  |     Server    |
     |         |<--(B)---- Access Token ---------<|               |
     |         |                                  |               |
     +---------+                                  +---------------+

The flow illustrated includes the following steps:

   **(A)** -  The client authenticates with the authorization server and requests an access token from the token endpoint.

   **(B)** -  The authorization server authenticates the client, and if valid, issues an access token.

### Implementation

To use the client credentials grant type, make a POST request like the following:

```
http://YOUR-SITE/index.php?option=token&api=oauth2
    &grant_type=client_credentials
    &client_id=your_client_id
    &client_secret=your_client_secret
```

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
