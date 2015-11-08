Gulp scripts for redCORE
==========

1. [Description](#description)
2. [Setup](#setup)  
    2.1. [Standalone redCORE](#standalone-setup)  
    2.2. [Integration with third part extensions](#third-part-setup)  
        2.2.1. [Add redCORE extensions to your extensions file](#add-required-extensions)  
        2.2.2. [Install required npm packages](#install-required-packages)  
3. [Usage](#usage)  
4. [License](#license)  

## <a name="description"></a> 1. Description

These are a set of [Gulp](http://gulpjs.com/) scripts for [redCORE](https://github.com/redCOMPONENT-COM/redCORE) to integrate it with the [joomla-gulp](https://github.com/phproberto/joomla-gulp) system.

## <a name="setup"></a>2. Setup

These scripts can be used for standalone redCORE development or in projects that integrate redCORE as a dependency.

### <a name="standalone-setup"></a>2.1. Standalone redCORE setup

See [redCORE contributing guidelines](https://github.com/redCOMPONENT-COM/redCORE/blob/develop/CONTRIBUTING.md).

### <a name="third-part-setup"></a>2.2. Integration with third part extensions

If your extension uses redCORE + joomla-gulp you can use these scripts to ensure that your Gulp system control tracks everything.

#### <a name="add-required-extensions"></a>2.2.1. Add redCORE extensions to your extensions file

You have to add redCORE extensions to your extension `gulp-extensions.json` file. Remember to always check the [latest extensions file](https://github.com/redCOMPONENT-COM/redCORE/blob/develop/gulp-extensions.json) to see the list of extensions with Gulp scripts in redCORE.

This is a sample redCORE `gulp-extensions.json` file:

```json
{
	"components" : ["redcore"],
	"libraries"  : ["redcore"],
	"media"      : ["redcore"],
	"modules"    : {
		"frontend"       : ["redcore_langswitcher"]
	},
	"plugins"    : {
		"redpayment"     : ["paypal"],
		"system"         : ["redcore", "mvcoverride"]
	}
}
```

If your extension has a `gulp-extensions.json` file like:

```json
{
	"components" : ["mipayway", "package"],
	"libraries"  : ["mipayway"],
	"modules"    : {
		"frontend"       : ["member", "mybeacons", "shop", "transactions", "statistics", "cart"],
		"backend"        : ["count", "members_chart"]
	},
	"plugins"    : {
		"authentication" : ["mipayway", "mipayway_pin"],
		"mipayway"       : ["autoshop", "email", "usertypes", "push"],
		"redpayment"     : ["paypal"],
		"system"         : ["mipayway"],
		"user"           : ["mipayway"]
	}
}
```

The end result will be something like:

```json
{
	"components" : ["redcore", "mipayway", "package"],
	"libraries"  : ["redcore", "mipayway"],
	"media"      : ["redcore"],
	"modules"    : {
		"frontend"       : ["member", "mybeacons", "redcore_langswitcher", "shop", "transactions", "statistics", "cart"],
		"backend"        : ["count", "members_chart"]
	},
	"plugins"    : {
		"authentication" : ["mipayway", "mipayway_pin"],
		"mipayway"       : ["autoshop", "email", "usertypes", "push"],
		"redpayment"     : ["paypal"],
		"system"         : ["redcore", "mvcoverride", "mipayway"],
		"user"           : ["mipayway"]
	}
}
```

The final file is the result of mixing redCORE + your extension `gulp-extensions.json` files.

#### <a name="install-required-packages"></a>2.2.2 Install required npm packages

These Gulp scripts relay on some npm packages. So if your project is not already using them you have to install them.  

The easiest to install dependencies is by running the command:  

```npm install gulp joomla-gulp gulp-redcore fs del browser-sync gulp-less gulp-minify-css gulp-rename gulp-uglify --save-dev```

## <a name="usage"></a>3. Usage

Usage is described in the [joomla-gulp documentation](https://github.com/phproberto/joomla-gulp/blob/master/docs/README.md)  

## <a name="license"></a>4. License  

This scripts are licensed under the [GPL v2.0 license](https://github.com/redCOMPONENT-COM/gulp-redcore/blob/master/LICENSE)  

Copyright (C) 2015 [redCOMPONENT.com](http://www.redcomponent.com) - All rights reserved.  
