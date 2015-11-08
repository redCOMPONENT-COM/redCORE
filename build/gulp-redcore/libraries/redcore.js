var gulp = require('gulp');
var fs   = require('fs');

// Third part extension using redCORE
try {
	var config = require('../../../../gulp-config.json');
}
// Called directly from redCORE
catch(err) {
	var config = require('../../../gulp-config.json');
}

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');

var baseTask  = 'libraries.redcore';

var subextensionPath = './redCORE/libraries/redcore';
var directPath       = './libraries/redcore';

var extPath   = fs.existsSync(subextensionPath) ? subextensionPath : directPath;

// Clean
gulp.task('clean:' + baseTask, ['clean:' + baseTask + ':languages', 'clean:' + baseTask + ':manifest'], function() {
	return del(config.wwwDir + '/libraries/redcore', {force : true});
});

// Clean: languages
gulp.task('clean:' + baseTask + ':languages', function() {
	return del(config.wwwDir + '/language/**/*.lib_redcore.ini', {force : true});
});

// Clean: manifest
gulp.task('clean:' + baseTask + ':manifest', function() {
	return del(config.wwwDir + '/administrator/manifests/libraries/redcore.xml', {force : true});
});

// Copy
gulp.task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':library',
		'copy:' + baseTask + ':languages',
		'copy:' + baseTask + ':manifest'
	],
	function() {
});

// Copy: library
gulp.task('copy:' + baseTask + ':library',
	['clean:' + baseTask, 'copy:' + baseTask + ':languages', 'copy:' + baseTask + ':manifest'], function() {
	return gulp.src([
		extPath + '/**',
		'!' + extPath + '/**/*.md',
		'!' + extPath + '/language',
		'!' + extPath + '/language/**'
	])
	.pipe(gulp.dest(config.wwwDir + '/libraries/redcore'));
});

// Copy: languages
gulp.task('copy:' + baseTask + ':languages', ['clean:' + baseTask + ':languages'], function() {
	return gulp.src(extPath + '/language/**')
		.pipe(gulp.dest(config.wwwDir + '/language'));
});

// Copy: manifest
gulp.task('copy:' + baseTask + ':manifest', ['clean:' + baseTask + ':manifest'], function() {
	return gulp.src(extPath + '/redcore.xml')
		.pipe(gulp.dest(config.wwwDir + '/administrator/manifests/libraries'));
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':library',
		'watch:' + baseTask + ':languages',
		'watch:' + baseTask + ':manifest'
	],
	function() {
});

// Watch: library
gulp.task('watch:' +  baseTask + ':library', function() {
	gulp.watch([
			extPath + '/**/*',
			'!' + extPath + '/redcore.xml',
			'!' + extPath + '/language',
			'!' + extPath + '/language/**'
		], ['copy:' + baseTask + ':library', browserSync.reload]);
});

// Watch: languages
gulp.task('watch:' +  baseTask + ':languages', function() {
	gulp.watch(extPath + '/language/**/*', ['copy:' + baseTask + ':languages', browserSync.reload]);
});

// Watch: manifest
gulp.task('watch:' +  baseTask + ':manifest', function() {
	gulp.watch(extPath + '/redcore.xml', ['copy:' + baseTask + ':manifest', browserSync.reload]);
});
