## Implicit grant type

The `Implicit` grant type is similar to the `Authorization Code` grant type in that it is used to request access to protected resources on behalf of another user (i.e. a 3rd party).
It is optimized for public clients, such as those implemented in javascript or on mobile devices, where client credentials cannot be stored.

[Implicit grant type on tools.ietf.org](http://tools.ietf.org/html/rfc6749#section-4.2)

### Implicit Protocol

     +----------+
     | Resource |
     |  Owner   |
     |          |
     +----------+
          ^
          |
         (B)
     +----|-----+          Client Identifier     +---------------+
     |         -+----(A)-- & Redirection URI --->|               |
     |  User-   |                                | Authorization |
     |  Agent  -|----(B)-- User authenticates -->|     Server    |
     |          |                                |               |
     |          |<---(C)--- Redirection URI ----<|               |
     |          |          with Access Token     +---------------+
     |          |            in Fragment
     |          |                                +---------------+
     |          |----(D)--- Redirection URI ---->|   Web-Hosted  |
     |          |          without Fragment      |     Client    |
     |          |                                |    Resource   |
     |     (F)  |<---(E)------- Script ---------<|               |
     |          |                                +---------------+
     +-|--------+
       |    |
      (A)  (G) Access Token
       |    |
       ^    v
     +---------+
     |         |
     |  Client |
     |         |
     +---------+

The flow illustrated includes the following steps:

   **(A)** -  The client initiates the flow by directing the resource owner's user-agent to the authorization endpoint.  The client includes its client identifier,
   requested scope, local state, and a redirection URI to which the authorization server will send the user-agent back once access is granted (or denied).

   **(B)** -  The authorization server authenticates the resource owner (via the user-agent) and establishes whether the resource owner grants
   or denies the client's access request.

   **(C)** -  Assuming the resource owner grants access, the authorization server redirects the user-agent back to the client using the redirection URI provided earlier.
   The redirection URI includes the access token in the URI fragment.

   **(D)** -  The user-agent follows the redirection instructions by making a request to the web-hosted client resource (which does not include the fragment per [RFC2616]).
   The user-agent retains the fragment information locally.

   **(E)** -  The web-hosted client resource returns a web page (typically an HTML document with an embedded script) capable of accessing
   the full redirection URI including the fragment retained by the user-agent, and extracting the access token (and other parameters) contained in the fragment.

   **(F)** -  The user-agent executes the script provided by the web-hosted client resource locally, which extracts the access token.

   **(G)** -  The user-agent passes the access token to the client.

### Implementation

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
