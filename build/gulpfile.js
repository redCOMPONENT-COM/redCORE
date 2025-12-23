var gulp       = require('gulp');

var extension  = require('./package.json');
var config     = require('./gulp-redcore/config.js');

var requireDir = require('require-dir');
var zip        = require('gulp-zip');
var xml2js     = require('xml2js');
var fs         = require('fs');
var path       = require('path');
var ghPages    = require('gh-pages');
var taskHelper = require('./gulp-redcore/task-helper');
var task       = taskHelper(gulp);

var parser     = new xml2js.Parser();
var jgulp      = requireDir('./node_modules/joomla-gulp', {recurse: true});

// We will use local redcore gulp repository instead of node_modules
var redcore    = requireDir('gulp-redcore', {recurse: true});

task('release',
	[
		'release:redcore'
	]
);

// Override of the release script
task('release:redcore', function (cb) {
	fs.readFile('../extensions/redcore.xml', function(err, data) {
		parser.parseString(data, function (err, result) {
			var version = result.extension.version[0];

			if (result.extension.releaseName[0])
			{
				version = version + '-' + result.extension.releaseName[0].toLowerCase();
			}

			var fileName = config.skipVersion ? extension.name + '.zip' : extension.name + '-v' + version + '.zip';

			// We will output where release package is going so it is easier to find
			console.log('Creating new release file in: ' + path.join(config.release_dir, fileName));
			return gulp.src([
                '../extensions/**/*',
                '../extensions/**/.gitkeep'
            ])
			.pipe(zip(fileName))
			.pipe(gulp.dest(config.release_dir))
            .on('end', cb);
		});
	});
});

task('documentation', function(cb) {
	// Needed because it requested a username and password for github on Windows
	process.chdir('../');

	ghPages.publish('docs/gh-pages', {
		repo: 'git@github.com:redCOMPONENT-COM/redCORE.git',
		branch: 'gh-pages',
		cache: 'docs/.gh-pages'
	}, cb);
});
