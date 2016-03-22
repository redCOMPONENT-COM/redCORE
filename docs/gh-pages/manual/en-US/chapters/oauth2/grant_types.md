## Authorization Grant types

Grant Types allow you to expose multiple ways for a client to receive an Access Token.

Once the user has authorized the request, he will be redirected to the `redirect_url`.
The request will look like the following: http://YOUR-SITE/?code=somecodehere12345

This is a time-limited code that your application can exchange for a full authorization token.
To do this you will need to pass the code to the token endpoint by making a POST request to the token endpoint depending on the Authorization Grant type you are going to use.

redCORE provides a OAuth2 Server functionality with several grant type accesses:

### Authorization Code

The Authorization Code grant type is the most common OAuth2.0 flow.
It implements 3-Legged OAuth and involves the user granting the client an authorization code, which can be exchanged for an `Access Token`.

### User credentials - Resource Owner Password Credentials

A Resource Owner's username and password are submitted as part of the request, and a token is issued upon successful authentication.

```
$ curl -u your_client_id:your_client_secret "http://YOUR-SITE/index.php?option=token&api=oauth2" -d 'grant_type=password&username=your_username&password=your_somepassword'
```
Response for this call is:
```
{"access_token":"206c80413b9a96c4312cc346b7d2517b84463edd","expires_in":3600,"token_type":"bearer","scope":null}
```

### Client Credentials

The client uses their credentials to retrieve an access token directly, which allows access to resources under the client's control

```
$ curl -u your_client_id:your_client_secret "http://YOUR-SITE/index.php?option=token&api=oauth2" -d 'grant_type=client_credentials'
```
Response for this call is:
```
{"access_token":"6f05ad622a3d32a5a81aee3d73a5826adb8cbf63","expires_in":3600,"token_type":"bearer","scope":null}
```

### Refresh Token

The client can submit a refresh token and receive a new `access token` if the access token had expired.

```
$ curl -u your_client_id:your_client_secret "http://YOUR-SITE/index.php?option=token&api=oauth2" -d 'grant_type=refresh_token&refresh_token=c54adcfdb1d99d10be3be3b77ec32a2e402ef7e3'
```
Response for this call is:
```
{"access_token":"0e9d02499fe06762eca2fb9cfbb506676631dcfd","expires_in":3600,"token_type":"bearer","scope":null}
```

### Implicit

This is similar to the `Authorization Code` Grant Type above, but rather than an Authorization Code being returned from the authorization request, a token is returned to the client.
This is most common for client-side devices (i.e. mobile) where the Client Credentials cannot be stored securely.

Use the `Implicit` Grant Type by setting the `allow_implicit` option to true for the `authorize` endpoint in redCORE plugin options.

It is important to note this is not added as a Grant Type class because the implicit grant type is requested using the authorize endpoint rather than the token endpoint.

### JWT Bearer

The client can submit a JWT (JSON Web Token) in a request to the token endpoint. An access token (without a refresh token) is then returned directly.

### Extension Grant

You can create your own grant type by implementing the OAuth2\GrantType\GrantTypeInterface and adding it to the OAuth2 Server object.
The JWT Bearer Grant Type above is an example of this.
