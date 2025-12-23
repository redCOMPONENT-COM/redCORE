var gulp = require('gulp');
var fs   = require('fs');

var config = require('../config.js');
var task = require('../task-helper')(gulp);

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');

var baseTask  = 'webservices.redcore';

var subextensionPath = './redCORE/extensions/webservices';
var directPath       = '../extensions/webservices';

var extPath   = fs.existsSync(subextensionPath) ? subextensionPath : directPath;

// Clean
task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':webservices'
	],
	function() {
		return true;
});

// Clean webservices
task('clean:' + baseTask + ':webservices', ['copy:media.redcore'], function() {
	return del(config.wwwDir + '/media/redcore/webservices/joomla', {force : true});
});

// Copy
task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':webservices'
	],
	function() {
		return true;
});

// Copy webservices
task('copy:' + baseTask + ':webservices', ['copy:libraries.redcore', 'clean:' + baseTask + ':webservices'], function(cb) {
	return gulp.src(extPath + '/**')
		.pipe(gulp.dest(config.wwwDir + '/media/redcore/webservices'));
});

// Watch
task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':webservices'
	],
	function() {
		return true;
});

// Watch webservices
task('watch:' + baseTask + ':webservices', function() {
	gulp.watch(extPath + '/**/*',
	{ interval: config.watchInterval },
	gulp.series('copy:' + baseTask + ':webservices', browserSync.reload));
});
