## Authorization code

The `Authorization Code` grant type is used when the client wants to request access to protected resources on behalf of another user (i.e. a 3rd party).
This is the grant type most often associated with OAuth.

[Authorization Code Grant on tools.ietf.org](http://tools.ietf.org/html/rfc6749#section-4.1)

To make this Authorization code api call use this URL:

http://YOUR-SITE/index.php?option=authorize&api=oauth2&response_type=code&client_id=your_client_id&redirect_uri=your_redirect_uri

The `client_id` and `redirect_uri` must match information in your OAuth Client description as registered with your site.

The `response_type` is `code` or `token`, with `token` you can do Implicit OAuth and directly issue an access token.
The `code` response is an `authorization token` that must be exchanged with the Client Secret to get the `access token`.

Page the user sees describes the application (client) that is requesting access, using fields you filled out when you created your client.


### How to use it

Once the user has authorized the request, he will be redirected to the `redirect_url`.
The request will look like the following: http://YOUR-SITE/?code=somecodehere12345

This is a time-limited code that your application can exchange for a full authorization token.
To do this you will need to pass the code to the token endpoint by making a POST request to the token endpoint:

Once this is done, a token can be requested using the authorization code.

```
$ curl -u your_client_id:your_client_secret http://YOUR-SITE/index.php?option=token&api=oauth2 -d 'grant_type=authorization_code&code=xyz'
```

A successful token request will return a standard access token in JSON format:

```
{"access_token":"03807cc390319329bdf6c777d4dfae5c0d3b3c35","expires_in":3600,"token_type":"bearer","scope":null}
```
