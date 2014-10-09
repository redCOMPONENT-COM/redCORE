## Refresh token flow

The `Refresh Token` grant type is used to obtain additional access tokens in order to prolong the client's authorization of a user's resources.

[Refresh token grant type on tools.ietf.org](http://tools.ietf.org/html/rfc6749#section-1.5)

Refresh tokens are only provided when retrieving a token using the `Authorization Code` or `User Credentials` grant types.
You can further set up Refresh Token grant type by using option `always_issue_new_refresh_token` and `refresh_token_lifetime` in redCORE plugin options.

`always_issue_new_refresh_token` - This option will check whether to issue a new refresh token upon each successful token request.

`refresh_token_lifetime` - This is time before refresh token expires

### How to use it

First, a refresh token must be retrieved using the `Authorization Code` or `User Credentials` grant types:

```
$ curl -u your_client_id:your_client_secret http://YOUR-SITE/index.php?option=token&api=oauth2 -d 'grant_type=password&username=your_user_name&password=your_password'
```

The access token will then contain a refresh token:

```
{
    "access_token":"6YotnF2FEjr1zCsicMWpAA",
    "expires_in":3600,
    "token_type": "bearer",
    "scope":null,
    "refresh_token":"1Gzv3JOkF0XG2Qx2TlKWIA",
}
```

This refresh token can then be used to generate a new access token of equal or lesser scope:

```
$ curl -u your_client_id:your_client_secret http://YOUR-SITE/index.php?option=token&api=oauth2 -d 'grant_type=refresh_token&refresh_token=1Gzv3JOkF0XG2Qx2TlKWIA'
```

A successful token request will return a standard access token in JSON format:

```
{"access_token":"03807cbsdf319329bdf6c777d4dfae9c0d3b3c35","expires_in":3600,"token_type":"bearer","scope":null}
```

If the server is configured to always issue a new refresh token, then a refresh token will be returned with this response as well:

```
{"access_token":"03807cb390319329b273c777d4dfae9c0d3b3c35","expires_in":3600,"token_type":"bearer","scope":null,"refresh_token":"s6BhdRkqt3038295df6c78"}
```
