var gulp = require('gulp');
var fs   = require('fs');

var config = require('../config.js');

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');

var baseTask  = 'components.redcore';

var subextensionPath = './redCORE/extensions/components/com_redcore';
var directPath       = '../extensions/components/com_redcore';

var extPath   = fs.existsSync(subextensionPath) ? subextensionPath : directPath;

// Clean
gulp.task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':backend'
	],
	function() {
		return true;
});

// Clean backend
gulp.task('clean:' + baseTask + ':backend', function() {
	return del(config.wwwDir + '/administrator/components/com_redcore', {force : true});
});

// Copy
gulp.task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':backend'
	],
	function() {
		return true;
});

// Copy backend
gulp.task('copy:' + baseTask + ':backend', ['clean:' + baseTask + ':backend'], function(cb) {
	return (
		gulp.src(extPath + '/admin/**')
		.pipe(gulp.dest(config.wwwDir + '/administrator/components/com_redcore')) &&
		gulp.src(extPath + '/../../redcore.xml')
		.pipe(gulp.dest(config.wwwDir + '/administrator/components/com_redcore')) &&
		gulp.src(extPath + '/../../install.php')
		.pipe(gulp.dest(config.wwwDir + '/administrator/components/com_redcore'))
	);
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':backend'
	],
	function() {
		return true;
});

// Watch backend
gulp.task('watch:' + baseTask + ':backend', function() {
	gulp.watch([
		extPath + '/admin/**/*',
		extPath + '/../redcore.xml',
		extPath + '/../install.php'
	],
	{ interval: config.watchInterval },
	['copy:' + baseTask + ':backend', browserSync.reload]);
});
