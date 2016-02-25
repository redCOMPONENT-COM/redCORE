# Gulp

We recommend using gulp in redCORE project because it will replace all your other tools and scripts that you have used before. It is used for creating release packages, watching of modified files and placing them in your test environment, compressing js/css/less files, publishing documentation to github pages.

Gulp scripts for redCORE
==========

1. [Description](#description)
2. [Setup](#setup)
    2.1. [Standalone redCORE](#standalone-setup)
        2.1.1. [Use Gulp as file watcher](#use-gulp-as-watcher)
        2.1.2. [Use Gulp for creating release package](#use-gulp-for-release)
        2.1.3. [Use Gulp for pushing new documentation](#use-gulp-for-pushing-documentation)
    2.2. [Integration with third part extensions](#third-part-setup)
        2.2.1. [Add redCORE extensions to your extensions file](#add-required-extensions)
        2.2.2. [Install required npm packages](#install-required-packages)

## <a name="description"></a> 1. Description

These are a set of [Gulp](http://gulpjs.com/) scripts for [redCORE](https://github.com/redCOMPONENT-COM/redCORE) to integrate it with the [joomla-gulp](https://github.com/phproberto/joomla-gulp) system.
This article assumes that you have already installed npm and Gulp into your Operating system and that you can use them globally.

## <a name="setup"></a>2. Setup

These scripts can be used for standalone redCORE development or in projects that integrate redCORE as a dependency.

### <a name="standalone-setup"></a>2.1. Standalone redCORE setup

See [redCORE contributing guidelines](../Contributing.md) first to get started.

We are keeping gulp scripts and all gulp related files in the `build` folder of the redCORE repository, so to set it up you have to be in the `build` folder first

```cd build```

The easiest way to install dependencies is by running the command:

```npm install --save-dev```

This will ensure that all dependencies required will be installed locally in your `build/node_modules` folder.

After all dependencies have been installed, you may use the gulp in redCORE but first you need to create your Gulp config file that is specific for your environment.
You need to copy file `build/gulp-config.json.dist` to `build/gulp-config.json` and edit your new file `build/gulp-config.json` to suit your environment.

Note. You only have to install dependencies once. After that you can simply use gulp as described in the next chapter

#### <a name="use-gulp-as-watcher"></a>2.1.1. Use Gulp as file watcher

There are several ways that Gulp can be used but most common is the file watcher feature.
Running gulp watcher for redCORE can be done in two ways

You can go into the build folder and run it from there:
```
cd build
gulp
```

or from your root redCORE repository folder you can type:
```gulp --gulpfile build/gulpfile.js```

Either of those commands till trigger several things:
- it will clean your test site folders and copy newest files from the redCORE repository in a specific locations
- it will create a watcher for those same files, so if you change something it will copy that file to your test site
- it will create a browser sync so if you change something in redCORE it will refresh the browser page of the test site after the new file has been copied

#### <a name="use-gulp-for-release"></a>2.1.1. Use Gulp for creating release package

When creating new release package you can have a flag to omit the version number, otherwise it will create a file with the redCORE version number in the file name (ex: redCORE-v1.8.0.zip). To omit the version number you can set argument `--skip-version` when calling gulp release build.
Running gulp release for redCORE can be done in two ways

You can go into the build folder and run it from there:
```
cd build
gulp release
```

or from your root redCORE repository folder you can type:
```gulp release --gulpfile build/gulpfile.js```

Either of those commands till create a new release package in the folder you have specified in the gulp-config file.

#### <a name="use-gulp-for-pushing-documentation"></a>2.1.3. Use Gulp for pushing new documentation

If you are making changes to the documentation files there is a process to simplify your work using Gulp.
redCORE uses Github pages to show its documentation, and Github pages works in a specific way where user mush create a separate branch in the repository called gh-pages and keep documentation there.
Using gulp this can be a simple process.

- Add new chapters or text in the `docs/gh-pages`
- run gulp documentation

To run gulp documentation you can do it in two ways:

You can go into the build folder and run it from there:
```
cd build
gulp documentation
```

or from your root redCORE repository folder you can type:
```gulp documentation --gulpfile build/gulpfile.js```

This process will do following things:
- Clone gh-pages branch from redCORE repository into a temporary folder
- Copy all files from the `docs/gh-pages`
- Check to see if there are any changes
- If there are new changes it will push those changes to the gh-pages branch

### <a name="third-part-setup"></a>2.2. Integration with third part extensions

If your extension uses redCORE + joomla-gulp you can use these scripts to ensure that your Gulp system control tracks everything.

#### <a name="add-required-extensions"></a>2.2.1. Add redCORE extensions to your extensions file

You have to add redCORE extensions to your extension `gulp-extensions.json` file. Remember to always check the [latest extensions file](https://github.com/redCOMPONENT-COM/redCORE/blob/develop/build/gulp-extensions.json) to see the list of extensions with Gulp scripts in redCORE.

This is a sample redCORE `gulp-extensions.json` file:

```json
{
	"cli" 		 : ["redcore"],
	"components" : ["redcore"],
	"libraries"  : ["redcore"],
	"media"      : ["redcore"],
	"modules"    : {
		"frontend"       : ["redcore_langswitcher"]
	},
	"plugins"    : {
		"redpayment"     : ["paypal"],
		"system"         : ["redcore", "mvcoverride"]
	},
	"webservices"      : ["redcore"]
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
	"cli" 		 : ["redcore"],
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
	},
	"webservices"      : ["redcore"]
}
```

The final file is the result of mixing redCORE + your extension `gulp-extensions.json` files.

#### <a name="install-required-packages"></a>2.2.2 Install required npm packages

These Gulp scripts relay on some npm packages. So if your project is not already using them you have to install them.

The easiest to install dependencies is by running the command:

```npm install --save-dev```
