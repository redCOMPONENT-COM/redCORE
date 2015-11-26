var gulp = require('gulp');
var fs   = require('fs');

// Third part extension using redCORE
try {
	var config = require('../../../../build/gulp-config.json');
}
// Called directly from redCORE
catch(err) {
	var config = require('../../../build/gulp-config.json');
}

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');

var baseTask  = 'libraries.redcore';

var subextensionPath = './redCORE/extensions/libraries/redcore';
var directPath       = '../extensions/libraries/redcore';

var extPath   = fs.existsSync(subextensionPath) ? subextensionPath : directPath;

// Clean
gulp.task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':library',
		'clean:' + baseTask + ':languages',
		'clean:' + baseTask + ':manifest'
	],
	function() {
		return true;
});

// Clean: languages
gulp.task('clean:' + baseTask + ':library', function() {
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
		'copy:' + baseTask + ':manifest',
		'copy:' + baseTask + ':media'
	],
	function() {
		return true;
});

// Copy: library
gulp.task('copy:' + baseTask + ':library',
	['clean:' + baseTask + ':library', 'copy:' + baseTask + ':languages', 'copy:' + baseTask + ':manifest'], function() {
	return gulp.src([
		extPath + '/**',
		'!' + extPath + '/**/*.md',
		'!' + extPath + '/language',
		'!' + extPath + '/language/**',
		'!' + extPath + '/media',
		'!' + extPath + '/media/**'
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

// Copy: media
gulp.task('copy:' + baseTask + ':media', function() {
	del.sync([config.wwwDir + '/media/redcore'], {force: true});

	return gulp.src([extPath + '/media/redcore/**'])
		.pipe(gulp.dest(config.wwwDir + '/media/redcore'));
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':library',
		'watch:' + baseTask + ':languages',
		'watch:' + baseTask + ':manifest',
		'watch:' + baseTask + ':media'
	],
	function() {
});

// Watch: library
gulp.task('watch:' +  baseTask + ':library', function() {
	gulp.watch([
			extPath + '/**/*',
			'!' + extPath + '/redcore.xml',
			'!' + extPath + '/language',
			'!' + extPath + '/language/**',
			'!' + extPath + '/media',
			'!' + extPath + '/media/**'
		],
		{ interval: config.watchInterval },
		['copy:' + baseTask + ':library', browserSync.reload]);
});

// Watch: languages
gulp.task('watch:' +  baseTask + ':languages', function() {
	gulp.watch(extPath + '/language/**/*',
		{ interval: config.watchInterval },
		['copy:' + baseTask + ':languages', browserSync.reload]);
});

// Watch: manifest
gulp.task('watch:' +  baseTask + ':manifest', function() {
	gulp.watch(extPath + '/redcore.xml',
		{ interval: config.watchInterval },
		['copy:' + baseTask + ':manifest',browserSync.reload]);
});

// Watch: media
gulp.task('watch:' +  baseTask + ':media', function() {
	gulp.watch([
		extPath + '/media/redcore/**'
	],
	{ interval: config.watchInterval },
	['copy:' + baseTask + ':media', browserSync.reload]);
});
