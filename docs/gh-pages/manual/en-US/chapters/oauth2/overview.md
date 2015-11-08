## OAuth2 Server in redCORE

OAuth2 Server in redCORE is based on Brent Shaffer project "OAuth2 Server library for PHP". More information about it can be found [here](https://github.com/bshaffer/oauth2-server-php).

OAuth 2.0 focuses on client developer simplicity while providing specific authorization flows for web applications, desktop applications, mobile phones, and living room devices.
The primary goal of OAuth2 is to allow developers to interact with your site without requiring them to store sensitive credentials.
OAuth2 replaces a username and password that must be entered manually or stored locally so that it is available every time you run the script,
and which are transmitted to the server on every request.

### Protocol flow

     +--------+                               +---------------+
     |        |--(A)- Authorization Request ->|   Resource    |
     |        |                               |     Owner     |
     |        |<-(B)-- Authorization Grant ---|               |
     |        |                               +---------------+
     |        |
     |        |                               +---------------+
     |        |--(C)-- Authorization Grant -->| Authorization |
     | Client |                               |     Server    |
     |        |<-(D)----- Access Token -------|               |
     |        |                               +---------------+
     |        |
     |        |                               +---------------+
     |        |--(E)----- Access Token ------>|    Resource   |
     |        |                               |     Server    |
     |        |<-(F)--- Protected Resource ---|               |
     +--------+                               +---------------+

The abstract OAuth 2.0 flow illustrated describes the interaction between the four roles and includes the following steps:

   **(A)**  - The client requests authorization from the resource owner. The authorization request can be made directly to the resource owner
        (as shown), or preferably indirectly via the authorization server as an intermediary.

   **(B)**  - The client receives an authorization grant, which is a credential representing the resource owner's authorization,
        expressed using one of four grant types defined in this specification or using an extension grant type. The
        authorization grant type depends on the method used by the client to request authorization and the types supported by the authorization server.

   **(C)**  - The client requests an access token by authenticating with the authorization server and presenting the authorization grant.

   **(D)**  - The authorization server authenticates the client and validates the authorization grant, and if valid, issues an access token.

   **(E)**  - The client requests the protected resource from the resource server and authenticates by presenting the access token.

   **(F)**  - The resource server validates the access token, and if valid, serves the request.


### Roles
1. `The Third-Party Application: "Client"` -
The client is the application that is attempting to get access to the user's account. It needs to get permission from the user before it can do so.
redCORE OAuth Clients can be managed from Administration GUI.

2. `The API: "Resource Server"` -
The resource server is the redCORE API server used to access the user's information.

3. `The User: "Resource Owner"` -
The resource owner is the person who is giving access to some portion of their account.


### Access grant types
redCORE provides a OAuth2 Server functionality with several grant type accesses:

1. Authorization code
2. Implicit
3. User credentials
4. Client credentials
5. Refresh token
6. JWT Bearer (JSON Web Token Bearer)


### How to access it?

Before you begin to develop an application, you will need a Client id, Redirect URI, and a Client secret key.
These details will be used to authenticate your application and verify that the API calls being are valid and are coming from your application.
If OAuth2 Server is enabled in redCORE plugin you can access it from these endpoints:

1. Authorize: `http://YOUR-SITE/index.php?option=authorize&api=oauth2&response_type=code` - The user is redirected here by the client to authorize the request
2. Get Token: `http://YOUR-SITE/index.php?option=token&api=oauth2` - The client makes a request to this endpoint in order to obtain an Access Token
3. Get protected resource: `http://YOUR-SITE/index.php?option=resource&api=oauth2` - The client requests resources, providing an Access Token for authentication token. This library supports many different grant types, including all of those defined by the official OAuth Specification.

All parameters can be added in query or the URL, as a POST action, or in Headers. We recommend using headers for passing parameters.
**We highly recommend using HTTPS with OAuth2 Server for additional security.**

Returned value is in JSON format and status code for your OAuth2 Server is in Headers.

### Making an webservice call with Access Token

Our webservice API is JSON/XML based (HAL).
You can view all of the available endpoints if you access site root URL:

`http://YOUR-SITE/index.php?api=hal`

In order to make an authenticated call to your APIs, you need to include your access token with the call.
redCORE OAuth2 supports a `Bearer` token that is passed along in an Authorization header and a URI query `access_token`.

```
<?php
$access_token = "YOUR_API_TOKEN";
$curl = curl_init("http://YOUR-SITE/index.php?api=hal");
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token));
curl_exec($curl);
?>
```

The above example would return all available webservices currently installed on the site which user can access.


Example for adding new contact using webservice through ajax call:

```
jQuery.ajax({
    url: 'http://YOUR-SITE/administrator/index.php?option=com_contact&api=hal&task=save',
    type: 'POST',
     data: '{"name" : "test new contact", "catid" : "4"}',
    beforeSend : function(xhr) {
        xhr.setRequestHeader("Authorization", "Bearer " + access_token);
    },
    success: function(response) {
        //console.log(response);
    }
});
```

Note. Contact webservice must be installed for this to work and Contact category id must be equal to your contact default category (4 by default).

### Tips

For clients running in typical `mobile environments`, it is often easy to register a temporary URI schema,
and set the redirect URI to something like `myclientauth://mycustomapp/` and let the phone browser restart the application when the user is done by fetching that URL.

For clients running a `desktop application` (on PC), the smart option is to make the redirect URI be something like
http://localhost:12345/mycustomapp/ and start a very limited web server before sending the user off to give authorization.
When his local browser redirects to that URL, the apps internal web server can capture the token and display a page letting the user know that the authorization is complete.


