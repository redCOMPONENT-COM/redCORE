## HAL - Hypertext Application Language

By using HAL redCORE webservice API can be easily served and consumed using open source libraries available for most major programming languages. 
It's also simple enough that you can just deal with it as you would any other `JSON`. Supported document formats are `JSON` and `XML`.

You can see demo of HAL working [here](http://haltalk.herokuapp.com) and you can read more 
information [here](http://stateless.co/hal_specification.html) or you can read [draft specification on tools.ietf.org](https://tools.ietf.org/html/draft-kelly-json-hal-06)

### General Description

HAL provides a set of conventions for expressing hyperlinks in either `JSON` or `XML`. The rest of a HAL document is just plain old JSON or XML.

Instead of using ad-hoc structures, or spending valuable time designing our own format; 
we are using HAL's conventions and focusing on building and documenting the data and transitions that make up redCORE webservice API.

HAL is a little bit like HTML for machines, in that it is generic and designed to drive many different types of application via hyperlinks. 
The difference is that HTML has features for helping `human actors` move through a web application to achieve their goals, 
whereas HAL is intended for helping `automated actors` move through a web API to achieve their goals.

Having said that, HAL is actually very human-friendly too. 
Its conventions make the documentation for an API discoverable from the API messages themselves. 
This makes it possible for developers to jump straight into a HAL-based API and explore its capabilities.

### Examples

The example below is how Default page in redCORE webservice API looks like with hal+json.


```
{
  "_links": {
    "curies": [
      {
        "href": "http://YOUR-SITE/index.php?option={rel}&format=doc&api=Hal",
        "title": "Documentation",
        "name": "documentation",
        "templated": true
      }
    ],
    "base": {
      "href": "http://YOUR-SITE/index.php?api=Hal",
      "title": "Default page"
    },
    "documentation:contact": [
      {
        "href": "http://YOUR-SITE/index.php?option=contact&api=Hal",
        "title": "Contact Webservice Site"
      },
      {
        "href": "http://YOUR-SITE/administrator/index.php?option=contact&api=Hal",
        "title": "Contact Webservice Administrator"
      }
    ]
  }
}
```


The compact URI (curie) named `documentation` is used for expanding the name of the links to their documentation URL.

A templated link called `documentation:contact` is used for getting documentation about specific webservice. `{rel}` in curries will be replaced with `contact`.

The URI of the Default page resource being represented `http://YOUR-SITE/index.php?api=Hal` expressed with a `base` link

### The HAL Model

The HAL conventions revolve around representing two simple concepts: Resources and Links.

#### Resources

Resources have:

- Links (to URIs)
- Embedded Resources (i.e. other resources contained within them)
- State (your bog standard JSON or XML data)

#### Links

Links have:

- A target (a URI)
- A relation aka. 'rel' (the name of the link)
- A few other optional properties to help with deprecation, content negotiation, etc.

Below is an image that roughly illustrates how a HAL representation is structured:

![](assets/img/hal_info_model.png)

