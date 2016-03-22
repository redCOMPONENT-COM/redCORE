## JWT Bearer (JSON Web Token Bearer) flow

The JWT Bearer grant type is used when the client wants to receive access tokens without transmitting sensitive information such as the client secret.
This can also be used with trusted clients to gain access to user resources without user authorization.
JWT Bearer Token has same benefits as the `Client Credentials` grant type,
allows for secure calls to be made without transmitting credentials and
for trusted clients, allows access of user resources without authorization.

The OAuth 2.0 JWT bearer token flow defines how a JWT can be used to request an OAuth access token from redCORE OAuth Server site when a client utilizes a previous authorization.
Authentication of the authorized application is provided by a digital signature applied to the JWT.

More detailed explanations of a JWT and the JWT bearer token flow for OAuth can be found at:

[Draft ietf oauth jwt bearer on tools.ietf.org](http://tools.ietf.org/html/draft-ietf-oauth-jwt-bearer)

[Draft jones json web token on tools.ietf.org](http://tools.ietf.org/html/draft-jones-json-web-token)

### How to use it

JWT requests require the signing of the JWT assertion using public key cryptography. The code snippet below provides an example of how this might be done.

```
$private_key = file_get_contents('id_rsa');
$client_id   = 'your_client_id';
$user_id     = 'your_joomla_user_id';
$grant_type  = 'urn:ietf:params:oauth:grant-type:jwt-bearer';

$jwt = generateJWT($private_key, $client_id, $user_id, 'http://YOUR-SITE');

passthru("curl http://YOUR-SITE/index.php?option=token&api=oauth2 -d 'grant_type=$grant_type&assertion=$jwt'");
```

A successful token request will return a standard access token in JSON format:

```
{"access_token":"23807cb390319329bdf6c777d4ddae9c0d3b3c35","expires_in":3600,"token_type":"bearer","scope":null}
```


The `JWT` is POST-ed to the OAuth token endpoint, which in turn processes the JWT, and issues an `access_token` based upon prior approval of the application.
However, the client doesn't need to have or store a `refresh_token`, nor is a `client_secret` required to be passed to the token endpoint.

JWT bearer flow supports the `RSA SHA256 algorithm`, which uses an uploaded certificate as the signing secret.

The OAuth 2.0 JWT bearer token flow involves the following general steps:

1. The developer creates a new client. `Client Secret` is generated and assigned to the client.
2. The developer writes an application that generates a JWT, and signs it with their certificate.
3. The JWT is POST-ed to the `token` endpoint http://YOUR-SITE/index.php?option=token&api=oauth2
4. The token endpoint validates the signature using the certificate registered by the developer.
5. The token endpoint validates the audience (aud), issuer (iss), validity (exp), and principal (prn) of the JWT.
6. Assuming the JWT is valid and the application has been previously authorized by the user or administrator, redCORE OAuth issues an `access_token`.
