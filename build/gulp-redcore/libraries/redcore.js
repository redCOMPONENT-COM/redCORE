var gulp = require('gulp');
var fs   = require('fs');

var config = require('../config.js');

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');
var merge       = require('merge-stream');

var baseTask  = 'libraries.redcore';

var baseFolder  = fs.existsSync('./redCORE') ? './redCORE' : '..';
var extSubPath = 'libraries/redcore';
var mediaSubPath = 'media/redcore';
var extPath = baseFolder + '/extensions/' + extSubPath;
var mediaPath = extPath + '/' + mediaSubPath;
var buildPathMedia = baseFolder + '/build/media/' + extSubPath + '/' + mediaSubPath;

// Clean
gulp.task('clean:' + baseTask,
	[
		'clean:' + baseTask + ':library',
		'clean:' + baseTask + ':manifest'
	],
	function() {
		return true;
});

// Clean: library
gulp.task('clean:' + baseTask + ':library', function() {
	return del(config.wwwDir + '/libraries/redcore', {force : true});
});

// Clean: manifest
gulp.task('clean:' + baseTask + ':manifest', function() {
	return del(config.wwwDir + '/administrator/manifests/libraries/redcore.xml', {force : true});
});

// Copy
gulp.task('copy:' + baseTask,
	[
		'copy:' + baseTask + ':library',
		'copy:' + baseTask + ':manifest',
		'copy:' + baseTask + ':media'
	],
	function() {
		return true;
});

// Copy: library
gulp.task('copy:' + baseTask + ':library',
	['clean:' + baseTask + ':library', 'copy:' + baseTask + ':manifest'], function() {
	return gulp.src([
		extPath + '/**',
		'!' + extPath + '/**/*.md',
		'!' + extPath + '/media',
		'!' + extPath + '/media/**'
	])
	.pipe(gulp.dest(config.wwwDir + '/libraries/redcore'));
});

// Copy: manifest
gulp.task('copy:' + baseTask + ':manifest', ['clean:' + baseTask + ':manifest'], function() {
	return gulp.src(extPath + '/redcore.xml')
		.pipe(gulp.dest(config.wwwDir + '/administrator/manifests/libraries'));
});

// Copy: media
gulp.task('copy:' + baseTask + ':media', function() {
	// Delete all except for webservices folder
	del.sync([
		config.wwwDir + '/media/redcore/css',
		config.wwwDir + '/media/redcore/fonts',
		config.wwwDir + '/media/redcore/images',
		config.wwwDir + '/media/redcore/js',
		config.wwwDir + '/media/redcore/lib',
		config.wwwDir + '/media/redcore/translations',
		config.wwwDir + '/media/redcore/webservices/joomla'
	], {force: true});

	var media = gulp.src([mediaPath + '/**'])
		.pipe(gulp.dest(config.wwwDir + '/media/redcore'));

	var uncompressed = gulp.src([
				buildPathMedia + '/**',
				'!' + buildPathMedia + '/less',
				'!' + buildPathMedia + '/less/**',
				'!' + buildPathMedia + '/sass',
				'!' + buildPathMedia + '/sass/**'
			])
			.pipe(gulp.dest(config.wwwDir + '/media/redcore'));

	return merge(media, uncompressed);
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':library',
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
			'!' + extPath + '/media',
			'!' + extPath + '/media/**'
		],
		{ interval: config.watchInterval },
		['copy:' + baseTask + ':library', browserSync.reload]);
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
