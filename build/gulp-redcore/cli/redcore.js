var gulp = require('gulp');
var fs   = require('fs');

var config = require('../config.js');
var task = require('../task-helper')(gulp);

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');

var baseTask  = 'cli.redcore';

var subextensionPath = './redCORE/extensions/cli';
var directPath       = '../extensions/cli';

var extPath   = fs.existsSync(subextensionPath) ? subextensionPath : directPath;

// Clean
task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':cli'
	],
	function() {
		return true;
});

// Clean cli
task('clean:' + baseTask + ':cli', function() {
	return del(config.wwwDir + '/cli/com_redcore', {force : true});
});

// Copy
task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':cli'
	],
	function() {
		return true;
});

// Copy cli
task('copy:' + baseTask + ':cli', ['clean:' + baseTask + ':cli'], function(cb) {
	return gulp.src(extPath + '/**')
		.pipe(gulp.dest(config.wwwDir + '/cli'));
});

// Watch
task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':cli'
	],
	function() {
		return true;
});

// Watch cli
task('watch:' + baseTask + ':cli', function() {
	gulp.watch(
		extPath + '/**/*',
		{ interval: config.watchInterval },
		gulp.series('copy:' + baseTask + ':cli', browserSync.reload)
	);
});
