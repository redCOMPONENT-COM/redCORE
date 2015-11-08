var gulp = require('gulp');
var fs   = require('fs');

// Third part extension using redCORE
try {
	var config = require('../../../../gulp-config.json');
}
// redCORE repo
catch(err) {
	var config = require('../../../gulp-config.json');
}

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');

var baseTask  = 'components.redcore';

var subextensionPath = './redCORE/component';
var directPath       = './component';

var extPath   = fs.existsSync(subextensionPath) ? subextensionPath : directPath;

// Clean
gulp.task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':cli',
		'clean:' + baseTask + ':backend'
	],
	function() {
		return true;
});

// Clean cli
gulp.task('clean:' + baseTask + ':cli', function() {
	return del(config.wwwDir + '/cli/com_redcore', {force : true});
});

// Clean backend
gulp.task('clean:' + baseTask + ':backend', function() {
	return del(config.wwwDir + '/administrator/components/com_redcore', {force : true});
});

// Copy
gulp.task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':cli',
		'copy:' + baseTask + ':backend'
	],
	function() {
		return true;
});

// Copy cli
gulp.task('copy:' + baseTask + ':cli', ['clean:' + baseTask + ':cli'], function() {
	return gulp.src(extPath + '/cli/**')
		.pipe(gulp.dest(config.wwwDir + '/cli'));
});

// Copy backend
gulp.task('copy:' + baseTask + ':backend', ['clean:' + baseTask + ':backend'], function(cb) {
	return (
		gulp.src(extPath + '/admin/**')
		.pipe(gulp.dest(config.wwwDir + '/administrator/components/com_redcore')) &&
		gulp.src(extPath + '/../redcore.xml')
		.pipe(gulp.dest(config.wwwDir + '/administrator/components/com_redcore')) &&
		gulp.src(extPath + '/../install.php')
		.pipe(gulp.dest(config.wwwDir + '/administrator/components/com_redcore'))
	);
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':cli',
		'watch:' + baseTask + ':backend'
	],
	function() {
		return true;
});

// Watch cli
gulp.task('watch:' + baseTask + ':cli', function() {
	gulp.watch(extPath + '/cli/**/*',
	['copy:' + baseTask + ':cli', browserSync.reload]);
});

// Watch backend
gulp.task('watch:' + baseTask + ':backend', function() {
	gulp.watch([
		extPath + '/admin/**/*',
		extPath + '/../redcore.xml',
		extPath + '/../install.php'
	],
	['copy:' + baseTask + ':backend', browserSync.reload]);
});