var gulp       = require('gulp');

var extension  = require('./package.json');

var argv       = require('yargs').argv;
var requireDir = require('require-dir');
var zip        = require('gulp-zip');
var xml2js     = require('xml2js');
var fs         = require('fs');
var path       = require('path');

var parser = new xml2js.Parser();

var config      = require('./gulp-config.json');

var jgulp   = requireDir('./node_modules/joomla-gulp', {recurse: true});
var redcore = requireDir('./node_modules/gulp-redcore', {recurse: true});

/**
 * Function for read list folder
 *
 * @param  string dir Path of folder
 *
 * @return array      Subfolder list.
 */
function getFolders(dir){
	return fs.readdirSync(dir)
		.filter(function(file){
			return fs.statSync(path.join(dir, file)).isDirectory();
		}
	);
}

gulp.task('release',
	[
		'release:redcore',
		//'release:plugin',
		//'release:rsbmedia'
	]
);

// Override of the release script @todo dodati , ['composer:libraries.redcore']
gulp.task('release:redcore', function (cb) {
	fs.readFile( './redcore.xml', function(err, data) {
		parser.parseString(data, function (err, result) {
			var version = result.extension.version[0];

			var fileName = argv.skipVersion ? extension.name + '.zip' : extension.name + '-v' + version + '.zip';

			return gulp.src([
                './**/*',
                './**/.gitkeep',
                "!./**/bower.json",
                "!./**/scss/**",
                "!./**/less/**",
                "!./**/build.*",
                "!./**/build/**",
                "!./**/*.md",
                "!./**/docs/**",
                "!./**/joomla-gulp/**",
                "!./**/jgulp/**",
                "!./**/gulp**",
                "!./**/gulp**/**",
                "!./**/gulpfile.js",
                "!./**/node_modules/**",
                "!./**/node_modules/**/.*",
                "!./**/package.json",
                "!./**/releases/**",
                "!./**/releases/**/.*",
                "!./src/**",
                '!./**/sample/**',
                '!./**/sample/.*',
                '!./**/tests/**',
                '!./**/tests/.*',
                "!./**/*.sublime-*",
                "!./**/*.sh",
                "!./**/composer.json",
                "!./**/phpunit*.xml",
            ],{ base: './' })
			.pipe(zip(fileName))
			.pipe(gulp.dest(config.release_dir))
            .on('end', cb);
		});
	});
});
