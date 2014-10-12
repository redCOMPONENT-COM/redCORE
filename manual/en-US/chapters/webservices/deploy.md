## Deploying webservice XMLs

Webservices in redCORE are defined using one XML mapping file and optionally one php Helper file.
There are couple of ways to set them in correct folder that redCORE uses when scaning for webservice XML files.

Folder from which redCORE scan webservice files is: `media/redcore/webservices`. Please note that webservices are grouped in different folders.
Although redCORE will scan files directly from root folder. It is good practice to separate them in folders for each extension or logic group. For example all Joomla core extensions are in folder `joomla`.


### Deploy your webservices through [redCORE installer](chapters/Installer.md)

If you are using redCORE installer on your extension you can deploy your webservices with a XML element within your extension installation manifest file.

```
<extension>
...

<webservices folder="media/webservices">
	<folder>com_my_extension</folder>
</webservices>

...
</extension>
```

This will copy all child elements you have defined (folder `com_my_extension` in above example) from your installation package folder: `media/webservices` (this is location of your webservice files) to redcore folder `media/redcore/webservices`


### Deploy your webservices through custom script

This option is entirely up to the developer.
In your installation procedure you must copy webservice files to folder `media/redcore/webservices` and put it into your extension (or logical group) folder name (ex. `media/redcore/webservices/com_my_extension`)


### Manually deploy webservices through redCORE user interface

You can deploy your webservice files directly through redCORE user interface. All XML files will be uploaded to `media/redcore/webservices/upload` location.
There is a limitation to only XML files through this feature so you will still have to deploy your helper files (if any) directly to `media/redcore/webservices/upload` where your XML files have been uploaded to.

