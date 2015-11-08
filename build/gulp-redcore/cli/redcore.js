var gulp = require('gulp');
var fs   = require('fs');

// Third part extension using redCORE
try {
	var config = require('../../../../build/gulp-config.json');
}
// redCORE repo
catch(err) {
	var config = require('../../../build/gulp-config.json');
}

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');

var baseTask  = 'cli.redcore';

var subextensionPath = './redCORE/extensions/cli';
var directPath       = '../extensions/cli';

var extPath   = fs.existsSync(subextensionPath) ? subextensionPath : directPath;

// Clean
gulp.task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':cli'
	],
	function() {
		return true;
});

// Clean cli
gulp.task('clean:' + baseTask + ':cli', function() {
	return del(config.wwwDir + '/cli/com_redcore', {force : true});
});

// Copy
gulp.task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':cli'
	],
	function() {
		return true;
});

// Copy cli
gulp.task('copy:' + baseTask + ':cli', ['clean:' + baseTask + ':cli'], function() {
	return gulp.src(extPath + '/**')
		.pipe(gulp.dest(config.wwwDir + '/cli'));
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':cli'
	],
	function() {
		return true;
});

// Watch cli
gulp.task('watch:' + baseTask + ':cli', function() {
	gulp.watch(extPath + '/**/*',
	{ interval: config.watchInterval },
	['copy:' + baseTask + ':cli', browserSync.reload]);
});
