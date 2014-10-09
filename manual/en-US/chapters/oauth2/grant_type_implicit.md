## Implicit grant type

The `Implicit` grant type is similar to the `Authorization Code` grant type in that it is used to request access to protected resources on behalf of another user (i.e. a 3rd party).
It is optimized for public clients, such as those implemented in javascript or on mobile devices, where client credentials cannot be stored.

[Implicit grant type on tools.ietf.org](http://tools.ietf.org/html/rfc6749#section-4.2)

Use the `Implicit` Grant Type by setting the `allow_implicit` option to true for the `authorize` endpoint in redCORE plugin options.

### Using Implicit grant type

First, redirect the user to the following URL:

`http://YOUR-SITE/index.php?option=authorize&api=oauth2&response_type=code&client_id=your_client_id&redirect_uri=your_redirect_uri`

Once the user authenticates on their site, they will be redirect back to your application.
The token and user information will be included in the URL fragment.

`http://YOUR-SITE/#access_token=78dc760116da3a3e0b28c326d4ec0d9d4340896f&expires_in=145600&token_type=Bearer&refresh_token=550f2884363736c0850555860604e3d85e10eec9`

This token will allow you to make authenticated client side calls using Ajax requests.

These Tokens have an expiration time which can be set in redCORE plugin options and users will need to authenticate with your app once the token expires.
Use the `expires_in` fragment to detect when you should prompt for a refresh.
