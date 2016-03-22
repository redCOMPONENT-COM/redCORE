## Installing webservice

After you have deployed your webservice XML mapping file and optionally Helper file 
(you can read how to deploy your webservice files [here](chapters/webservices/deploy.md)) you can install your webservice file.

redCORE provide a GUI for webservice management from Administration of your site. Go to `Adminitrator -> redCORE -> Webservices`. 
In that view you will see all deployed webservice files displayed together with some of the description they offer.

### Global options

Global options are always at the top of the listed webservices.

![](assets/img/redcore_webservices_global_options.png)

In above picture you can see if the option in redCORE plugin for webservice is enabled or not. 
Even if it is disabled you will be able to manage webservices on this page.

In here you can upload your XML mapping files with `Uploader`. 
You can read more information about how to deploy Webservice using uploader [here](chapters/webservices/deploy.md).

Below uploader you can find Buttons that will perform certain action on all listed webservices:

1. **Install / Update** button will try to `install` **all** listed webservices. 
Even if one of the webservice is already installed, it will perform an update on that webservice. 

2. **Uninstall** button is used to `uninstall` **all** webservices from your site.

3. **Delete** button will preform `uninstall` of **all** webservices from the site 
and then it will `delete` **all** webservice XML and Helper files from your webservices folder.

### List of webservices

Webservices by default are not installed and there for not accessible through the API. To make it accessible you must install it. 

Webservice list is divided between two client groups `administrator` and `site`.

#### Not installed webservice

![](assets/img/redcore_webservices_not_installed_webservice.png)

This is a representation of webservice which is available but not installed yet. 
You can see basic information about the webservice and the `status` which states if the webservice is installed or not. 
If webservice is not installed it will not be available through the redCORE webservice API.

You have two available Button options for not installed webservice:

1. `Install` - which installs webservice

2. `Delete` - which deletes webservice XML and Helper files

#### Installed webservice

![](assets/img/redcore_webservices_installed_webservice.png)

This is a representation of webservice which is installed. 
You can see basic information about the webservice and several useful information about it:
 
`status` which states if the webservice is installed or not. 

`methods` which states which operations are available within this webservice

`available scopes` which list scopes that are available with this webservice.

You have several available Button options for installed webservice:

1. `Documentation` - displays documentation for the webservice if authorization is not needed for display.

2. `GET JSON` - displays `read` operation in JSON format of the webservice if authorization is not needed for display.

3. `GET XML` - displays `read` operation in XML format of the webservice if authorization is not needed for display.

4. `Update` - which updates existing webservice from the XML file

5. `Uninstall` - which uninstalls webservice

6. `Delete` - which deletes webservice XML and Helper files

### How to check if the webservice is working

You can test it by clicking on one of the offered buttons `Documentation`, `GET JSON` or `GET XML`.
If all of the buttons require authorization you can try to check it by opening it directly with `HAL Browser` by supplying token for client authorization. 

You can read more about authorization in redCORE OAuth2 Server documentation [here](chapters/oauth2/overview.md).
