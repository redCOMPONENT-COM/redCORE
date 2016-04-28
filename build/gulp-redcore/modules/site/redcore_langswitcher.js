var gulp = require('gulp');
var fs   = require('fs');

var config = require('../../config.js');

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');
var merge       = require('merge-stream');

var baseTask  = 'modules.frontend.redcore_langswitcher';

var baseFolder  = fs.existsSync('./redCORE') ? './redCORE' : '..';

var extSubPath = 'modules/site/mod_redcore_language_switcher';
var mediaSubPath = 'media/mod_redcore_language_switcher';
var extPath = baseFolder + '/extensions/' + extSubPath;
var mediaPath = extPath + '/' + mediaSubPath;
var buildPathMedia = baseFolder + '/build/media/' + extSubPath + '/' + mediaSubPath;

// Clean
gulp.task('clean:' + baseTask, ['clean:' + baseTask + ':media'], function() {
    return del(config.wwwDir + '/modules/mod_redcore_language_switcher', {force: true});
});

// Clean: Media
gulp.task('clean:' + baseTask + ':media', function() {
    return del(config.wwwDir + '/media/mod_redcore_language_switcher', {force: true});
});

// Copy
gulp.task('copy:' + baseTask, ['clean:' + baseTask, 'copy:' + baseTask + ':media'], function() {
    return gulp.src([
	        extPath + '/**',
	        '!' + extPath + '/media',
	        '!' + extPath + '/media/**'
    	])
		.pipe(gulp.dest(config.wwwDir + '/modules/mod_redcore_language_switcher'));
});

// Copy: media
gulp.task('copy:' + baseTask + ':media', ['clean:' + baseTask + ':media'], function() {
	var media = gulp.src([
	        mediaPath + '/**'
    	])
		.pipe(gulp.dest(config.wwwDir + '/media/mod_redcore_language_switcher'));

		// Copy original uncompressed files to the testing site too
	var buildMedia = gulp.src([
					buildPathMedia + '/**'
		])
		.pipe(gulp.dest(config.wwwDir + '/media/mod_redcore_language_switcher'));

	return merge(media, buildMedia);
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':module',
		'watch:' + baseTask + ':media'
	],
	function() {
});

// Watch: Module
gulp.task('watch:' + baseTask + ':module', function() {
    gulp.watch([
    	extPath + '/**/*',
    	'!' + mediaPath + '/css',
    	'!' + mediaPath + '/css/**'
		],
		{ interval: config.watchInterval },
		['copy:' + baseTask, browserSync.reload]);
});

// Watch: media
gulp.task('watch:' +  baseTask + ':media', function() {
	gulp.watch([
		mediaPath + '/**'
	],
	{ interval: config.watchInterval },
	['copy:' + baseTask + ':media', browserSync.reload]);
});
