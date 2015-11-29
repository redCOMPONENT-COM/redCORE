# redCORE Repository structure

Structure is based on maximum simplicity for developers and for end users.

## build/ folder

In this folder we keep all our tools that we use in this repository. You can read more about specific tools [here](chapters/Tools.md).

- PHING files: `build.properties.dist`, `build.xml`, `extension_packager.xml` and `redcore_copy_mandatory.xml` Used for PHING builds and older extensions compatibility
- Gulp files: `gulp-config.json.dist`, `gulp-extensions.js`, `gulpfile.js`, `package.json` and folder `gulp-redcore/` are used for Gulp builds. Read more on [Tools](chapters/Tools.md)
- `media/` folder is is where we keep uncompressed media files which we can compress (minify) and move to the extensions folder. Gulp watcher is watching media folder for any changes on those files, if you make a change it will run appropriate style, script or libraries task that will copy compressed files to the testing site and in the `extensions/` folder. Any new added media file in the `media/` folder will be processed and copied to that locations.

## docs/ folder

This is the folder where we keep all information for support and is a place where we keep Github documentation pages

## extensions/ folder

In this folder we keep all the files that will be installed with the package. All media files are already minified and package is ready to be installed. If pointed to that folder one could install extension directly from that folder.

# tests/ folder

This folder is used for automated testing of the redCORE extension. Find detailed documentation at [Testing redCORE](https://github.com/redCOMPONENT-COM/redCORE/blob/develop/tests/README.md#testing-redcore).

PHP checkers and Code Style Sniffer are both placed in `tests/checkers` folder
