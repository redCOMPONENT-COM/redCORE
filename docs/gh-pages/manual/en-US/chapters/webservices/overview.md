## Webservices in redCORE

Webservices in redCORE provide a RESTfull (Representational state transfer) interface for your site using `HAL` as a easy way to hyperlink between resources.
Generating mapping XML file which will expose your webservice is very easy and you can read about them in following chapters.

### What is HAL?

`HAL - Hypertext Application Language` is a simple format that gives a consistent and easy way to hyperlink between resources in redCORE webservices API. 
`HAL` is making your redCORE webservice API explorable, and its documentation easily discoverable from within the API itself. 
In short, it is making redCORE webservice API easier to work with and therefore more attractive to client developers.

You can read more about HAL [here](chapters/webservices/hal.md)

### Out of the box

We have provided many webservice XML files which you can use to expose Joomla core as a standalone API.

1. `Contact` (site and administrator) - this webservice provides full access to com_contact CRUD functionality and exposes number of specific tasks


### Getting started with redCORE webservices

redCORE provides a user interface for webservice management in your administration. It also provides the overview for your webservices, getting information about each.
After installing webservice you'll also have a feature that will build automatic documentation for your webservice, enabling help to developers who will interact with it.
To keep it short, redCORE is the easiest way to build robust, secure, and documented APIs.

### HTTP Status Codes

Each Status-Code is described below, including a description of which method(s) it can follow and any meta information required in the response. Status codes are given following this workflow:

![](assets/img/status_codes_workflow.png)

### Tips

We highly recommend using **OAuth2 Server** together with webservice API since it already brings token manipulation and ease of access to the data. 
redCORE already provide **OAuth2 Server** functionality and you can read more information [here](chapters/oauth2/overview.md)

For better readability of redCORE webservices we recommend using HAL browser 
(you can see it in action [here](http://haltalk.herokuapp.com) or download [here](https://github.com/mikekelly/hal-browser)). 
With HAL Browser you can waltz through complete webservice API, make use of templated links and read complete documentation of each webservice from within the HAL Browser.



