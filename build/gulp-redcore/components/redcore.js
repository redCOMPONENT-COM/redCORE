var gulp = require('gulp');

var config = require('../config.js');

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');
var fs          = require('fs');
var merge       = require('merge-stream');

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

// Clean frontend
gulp.task('clean:' + baseTask + ':frontend', function() {
	return del(config.wwwDir + '/components/com_redcore', {force : true});
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
	var admin = gulp.src(extPath + '/admin/**')
		.pipe(gulp.dest(config.wwwDir + '/administrator/components/com_redcore'));

	var install = gulp.src([extPath + '/../../redcore.xml', extPath + '/../../install.php'])
		.pipe(gulp.dest(config.wwwDir + '/administrator/components/com_redcore'));

	return merge(admin, install);
});

// Copy frontend
gulp.task('copy:' + baseTask + ':frontend', ['clean:' + baseTask + ':frontend'], function(cb) {
	return (
		gulp.src(extPath + '/site/**')
		.pipe(gulp.dest(config.wwwDir + '/components/com_redcore'))
	);
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':backend',
		'watch:' + baseTask + ':frontend'
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

// Watch frontend
gulp.task('watch:' + baseTask + ':frontend', function() {
	gulp.watch([
		extPath + '/site/**/*'
	],
	{ interval: config.watchInterval },
	['copy:' + baseTask + ':frontend', browserSync.reload]);
});
