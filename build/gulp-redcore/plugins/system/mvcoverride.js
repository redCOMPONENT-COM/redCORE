var gulp = require('gulp');
var fs   = require('fs');

var config = require('../../config.js');

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');

var baseTask  = 'plugins.system.mvcoverride';

var subextensionPath = './redCORE/extensions/plugins/system/mvcoverride';
var directPath       = '../extensions/plugins/system/mvcoverride';

var extPath   = fs.existsSync(subextensionPath) ? subextensionPath : directPath;

// Clean
gulp.task('clean:' + baseTask, function() {
	return del(config.wwwDir + '/plugins/system/mvcoverride', {force : true});
});

// Copy
gulp.task('copy:' + baseTask, ['clean:' + baseTask], function() {
	return gulp.src( extPath + '/**')
		.pipe(gulp.dest(config.wwwDir + '/plugins/system/mvcoverride'));
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':plugin'
	],
	function() {
});

// Watch: plugin
gulp.task('watch:' + baseTask + ':plugin', function() {
	gulp.watch(extPath + '/**/*',
		{ interval: config.watchInterval },
		['copy:' + baseTask, browserSync.reload]);
});
