var gulp = require('gulp');
var fs   = require('fs');

var config = require('../../config.js');
var task = require('../../task-helper')(gulp);

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');

var baseTask  = 'plugins.system.redcore';

var subextensionPath = './redCORE/extensions/plugins/system/redcore';
var directPath       = '../extensions/plugins/system/redcore';

var extPath   = fs.existsSync(subextensionPath) ? subextensionPath : directPath;

// Clean
task('clean:' + baseTask, function() {
	return del(config.wwwDir + '/plugins/system/redcore', {force : true});
});

// Copy
task('copy:' + baseTask, ['clean:' + baseTask], function() {
	return gulp.src( extPath + '/**')
		.pipe(gulp.dest(config.wwwDir + '/plugins/system/redcore'));
});

// Watch
task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':plugin'
	],
	function() {
});

// Watch: plugin
task('watch:' + baseTask + ':plugin', function() {
	gulp.watch(extPath + '/**/*',
		{ interval: config.watchInterval },
		gulp.series('copy:' + baseTask, browserSync.reload));
});
