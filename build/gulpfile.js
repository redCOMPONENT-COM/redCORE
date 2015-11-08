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

// We will use local gulp repository instead of node_modules since it might be obsolete if we are making changes in this PR
var redcore = requireDir('gulp-redcore', {recurse: true});

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
		//'release:plugin'
	]
);

// Override of the release script @todo dodati , ['composer:libraries.redcore']
gulp.task('release:redcore', function (cb) {
	fs.readFile( '../extensions/redcore.xml', function(err, data) {
		parser.parseString(data, function (err, result) {
			var version = result.extension.version[0];
			var fileName = argv.skipVersion ? extension.name + '.zip' : extension.name + '-v' + version + '.zip';

			return gulp.src([
                '../extensions/**/*',
                '../extensions/**/.gitkeep',
				'../docs/LICENSE'
            ])
			.pipe(zip(fileName))
			.pipe(gulp.dest(config.release_dir))
            .on('end', cb);
		});
	});
});
