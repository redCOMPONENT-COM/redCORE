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

var baseTask  = 'components.redcore';

var subextensionPath = './redCORE/extensions/component';
var directPath       = '../extensions/component';

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
		gulp.src(extPath + '/../redcore.xml')
		.pipe(gulp.dest(config.wwwDir + '/administrator/components/com_redcore')) &&
		gulp.src(extPath + '/../install.php')
		.pipe(gulp.dest(config.wwwDir + '/administrator/components/com_redcore')) &&
		gulp.src(extPath + '/../LICENSE')
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
