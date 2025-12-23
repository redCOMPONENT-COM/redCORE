var gulp = require('gulp');

var config = require('../config.js');
var task = require('../task-helper')(gulp);

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
task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':backend'
	],
	function() {
		return true;
});

// Clean backend
task('clean:' + baseTask + ':backend', function() {
	return del(config.wwwDir + '/administrator/components/com_redcore', {force : true});
});

// Clean frontend
task('clean:' + baseTask + ':frontend', function() {
	return del(config.wwwDir + '/components/com_redcore', {force : true});
});

// Copy
task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':backend'
	],
	function() {
		return true;
});

// Copy backend
task('copy:' + baseTask + ':backend', ['clean:' + baseTask + ':backend'], function(cb) {
	var admin = gulp.src(extPath + '/admin/**')
		.pipe(gulp.dest(config.wwwDir + '/administrator/components/com_redcore'));

	var install = gulp.src([extPath + '/../../redcore.xml', extPath + '/../../install.php'])
		.pipe(gulp.dest(config.wwwDir + '/administrator/components/com_redcore'));

	return merge(admin, install);
});

// Copy frontend
task('copy:' + baseTask + ':frontend', ['clean:' + baseTask + ':frontend'], function(cb) {
	return (
		gulp.src(extPath + '/site/**')
		.pipe(gulp.dest(config.wwwDir + '/components/com_redcore'))
	);
});

// Watch
task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':backend',
		'watch:' + baseTask + ':frontend'
	],
	function() {
		return true;
});

// Watch backend
task('watch:' + baseTask + ':backend', function() {
	gulp.watch([
		extPath + '/admin/**/*',
		extPath + '/../redcore.xml',
		extPath + '/../install.php'
	],
	{ interval: config.watchInterval },
	gulp.series('copy:' + baseTask + ':backend', browserSync.reload));
});

// Watch frontend
task('watch:' + baseTask + ':frontend', function() {
	gulp.watch([
		extPath + '/site/**/*'
	],
	{ interval: config.watchInterval },
	gulp.series('copy:' + baseTask + ':frontend', browserSync.reload));
});
