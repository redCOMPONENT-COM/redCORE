## OAuth2 Clients

Before our client application can use OAuth2 Server functionality, it must be known to the service.
To do that we need to create a Client with redCORE OAuth Clients. You can access it through GUI in your administration in redCORE->OAuth Clients.

Once completed, this process gives us the information that must be coded in the application to identify your application to your site.

Note that all OAuth2 services require this step, and even if an application is designed to operate with more than one service,
this step must be performed by the developer once for each service and the resulting service IDs must all be stored for use with their matching service providers.

You provide a `Client ID` that is the name of your application that will be shown to users who requests authorization.
You need to provide `Redirect URL` for the application.

You can also choose `Grant types` you want to enable for this client. If one of the Grant types is not enabled then that Client will not have the option to authorize with it.

If you are using option `Authorize with scopes` which can be found in redCORE plugin options,
then you can select `Client scopes` which serves as a permission for specific task for that client.
You can select one or many Scopes for that client. You can also select scopes for all webservices if you want to provide full access to all installed webservices.

Once you have filled in the form, you will be issued your OAuth2 `Client Secret` identifier for the application.
Warning: Keep the `Client Secret` string secret. With that string, anyone can impersonate your application.

OAuth2 Server makes it possible to get an access token without needing to store the `Client Secret` in the client application at all. This is obviously the path we should take.

At any time you can view a list of your `OAuth Clients` where you can access each to manage its settings, recreate a lost secret key, or see some statistics about its use.
