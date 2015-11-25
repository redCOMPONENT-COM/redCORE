# Tools

Using tools is optional but can make developing much easier and faster. Some of the tools are used for automated packaging of builds or automated compressing the files.

## Gulp

We recommend using gulp in redCORE project because it will replace all your other tools and scripts that you have used before. It is used for creating release packages, watching of modified files and placing them in your test environment, compressing js/css/less files, publishing documentation to github pages, ...
To learn more about gulp project please see [here](http://gulpjs.com/).
To see in more detail how to use gulp in redCORE please see [here](chapters/tools/gulp.md).

## PHING

If you cannot use Gulp for some reason, we still support PHING build and deploy scripts which you can use to quickly create a release or to copy all new/updated files into the testing site.
To learn more about PHING project please see [here](https://www.phing.info/).
To see in more detail how to use PHING in redCORE please see [here](chapters/tools/phing.md).

## CSS minified files

If you cannot use Gulp for some reason, you can create minified CSS files by running standalone tool.
To see in more detail how to compress CSS files please see [here](chapters/tools/Css-minified-files.md).

## JS minified files

If you cannot use Gulp for some reason, you can create minified JS files by running standalone tool.
To see in more detail how to compress JS files please see [here](chapters/tools/JS-minified-files.md).
