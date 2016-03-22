## Authorization code

The `Authorization Code` grant type is used when the client wants to request access to protected resources on behalf of another user (i.e. a 3rd party).
This is the grant type most often associated with OAuth.

[Authorization Code Grant on tools.ietf.org](http://tools.ietf.org/html/rfc6749#section-4.1)

### Authorization code protocol


     +----------+
     | Resource |
     |   Owner  |
     |          |
     +----------+
          ^
          |
         (B)
     +----|-----+          Client Identifier      +---------------+
     |         -+----(A)-- & Redirection URI ---->|               |
     |  User-   |                                 | Authorization |
     |  Agent  -+----(B)-- User authenticates --->|     Server    |
     |          |                                 |               |
     |         -+----(C)-- Authorization Code ---<|               |
     +-|----|---+                                 +---------------+
       |    |                                         ^      v
      (A)  (C)                                        |      |
       |    |                                         |      |
       ^    v                                         |      |
     +---------+                                      |      |
     |         |>---(D)-- Authorization Code ---------'      |
     |  Client |          & Redirection URI                  |
     |         |                                             |
     |         |<---(E)----- Access Token -------------------'
     +---------+       (w/ Optional Refresh Token)

The flow illustrated includes the following steps:

   **(A)** - The client initiates the flow by directing the resource owner's user-agent to the authorization endpoint.
   The client includes its client identifier, requested scope, local state, and a redirection URI to which the authorization server
   will send the user-agent back once access is granted (or denied).

   **(B)** - The authorization server authenticates the resource owner (via the user-agent) and establishes whether the resource owner grants
   or denies the client's access request.

   **(C)** - Assuming the resource owner grants access, the authorization server redirects the user-agent back to the client using the
	redirection URI provided earlier (in the request or during client registration).  The redirection URI includes an
	authorization code and any local state provided by the client earlier.

   **(D)** - The client requests an access token from the authorization server's token endpoint by including the authorization code
	received in the previous step.  When making the request, the client authenticates with the authorization server.  The client
	includes the redirection URI used to obtain the authorization code for verification.

   **(E)** - The authorization server authenticates the client, validates the authorization code, and ensures that the redirection URI
	received matches the URI used to redirect the client in step **(C)**.  If valid, the authorization server responds back with
	an access token and, optionally, a refresh token.

### Implementation

To make this Authorization code api call use this URL:

http://YOUR-SITE/index.php?option=authorize&api=oauth2&response_type=code&client_id=your_client_id&redirect_uri=your_redirect_uri&state=xxy

The `client_id` and `redirect_uri` must match information in your OAuth Client description as registered with your site.

The `response_type` is `code` or `token`, with `token` you can do Implicit OAuth and directly issue an access token.
The `code` response is an `authorization token` that must be exchanged with the Client Secret to get the `access token`.
The `state` provides any state that might be useful to your application upon receipt of the response. 
The redCORE OAuth2 Server roundtrips this parameter, so your application receives the same value it sent. 
To mitigate against cross-site request forgery (CSRF), it is strongly recommended to include an anti-forgery token in the state, 
and confirm it in the response.

Page the user sees describes the application (client) that is requesting access, using fields you filled out when you created your client.

### Second step

redCORE OAuth2 Server handles user authentication, session, and user consent if the user is not logged in yet. 

`Not logged in` users will be redirected to the login screen where they will have an username / password box from 
Joomla where they can login to the site. If user has successfully logged in he will be redirected to the `user consent form`.

`user consent form` is shown when user is already logged in to the site. 
There user will be able to decide if he wants to let api client to use his account to preform specific tasks on his behalf by authorizing the request.

### Third step

Once the user has authorized the request, he will be redirected to the `redirect_url`.
The request will look like the following: http://YOUR-SITE/?code=somecodehere12345&state=xxy

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
