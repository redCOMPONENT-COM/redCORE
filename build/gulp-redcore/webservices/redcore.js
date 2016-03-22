var gulp = require('gulp');
var fs   = require('fs');

var config = require('../config.js');

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');

var baseTask  = 'webservices.redcore';

var subextensionPath = './redCORE/extensions/webservices';
var directPath       = '../extensions/webservices';

var extPath   = fs.existsSync(subextensionPath) ? subextensionPath : directPath;

// Clean
gulp.task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':webservices'
	],
	function() {
		return true;
});

// Clean webservices
gulp.task('clean:' + baseTask + ':webservices', ['copy:media.redcore'], function() {
	return del(config.wwwDir + '/media/redcore/webservices/joomla', {force : true});
});

// Copy
gulp.task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':webservices'
	],
	function() {
		return true;
});

// Copy webservices
gulp.task('copy:' + baseTask + ':webservices', ['copy:libraries.redcore', 'clean:' + baseTask + ':webservices'], function(cb) {
	return gulp.src(extPath + '/**')
		.pipe(gulp.dest(config.wwwDir + '/media/redcore/webservices'));
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':webservices'
	],
	function() {
		return true;
});

// Watch webservices
gulp.task('watch:' + baseTask + ':webservices', function() {
	gulp.watch(extPath + '/**/*',
	{ interval: config.watchInterval },
	['copy:' + baseTask + ':webservices', browserSync.reload]);
});
