## Creating new webservice

redCORE webservices API is build with a clear goal in mind and that is to provide easy way of creating new webservices. 
Creating webservice consist of several parts but only `XML mapping file` is required.

#### XML mapping file

XML mapping file is a heart and soul of the webservice. This is required step for any webservice. 
You can read on how to create your own XML mapping file [here](chapters/webservices/xml_file.md).

#### Helper file

Helper file can be used together with our `XML mapping file` to expand or replace methods and to provide your own custom logic in the way webservice works. 
Helper file is optional and you do not have to have it for your webservice to work. 
You can read on how to create your own Helper file [here](chapters/webservices/helper_file.md).

#### Plugins

With plugins you can trigger events after the original methods. 
There are many events available which you can use [here](chapters/webservices/plugin_methods.md).








