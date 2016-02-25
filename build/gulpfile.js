var gulp       	= require('gulp');

var extension  	= require('./package.json');
var config      = require('./gulp-redcore/config.js');

var requireDir 	= require('require-dir');
var zip        	= require('gulp-zip');
var xml2js     	= require('xml2js');
var fs         	= require('fs');
var path       	= require('path');
var ghPages     = require('gulp-gh-pages');

var parser      = new xml2js.Parser();
var jgulp   	= requireDir('./node_modules/joomla-gulp', {recurse: true});

// We will use local redcore gulp repository instead of node_modules
var redcore     = requireDir('gulp-redcore', {recurse: true});

gulp.task('release',
	[
		'release:redcore'
	]
);

// Override of the release script
gulp.task('release:redcore', function (cb) {
	fs.readFile( '../extensions/redcore.xml', function(err, data) {
		parser.parseString(data, function (err, result) {
			var version = result.extension.version[0];
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

gulp.task('documentation', function() {
	// Needed because it requested a username and password for github on Windows
	process.chdir('../');

	return gulp.src('docs/gh-pages/**/*')
		.pipe(ghPages({
			remoteUrl: 'git@github.com:redCOMPONENT-COM/redCORE.git',
			branch: 'gh-pages',
			cacheDir: 'docs/.gh-pages/'
		}));
});
