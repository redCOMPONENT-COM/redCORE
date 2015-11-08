var gulp = require('gulp');
var fs   = require('fs');

// Third part extension using redCORE
try {
	var config = require('../../../../../build/gulp-config.json');
}
catch(err) {
	var config = require('../../../../build/gulp-config.json');
}

// Dependencies
var browserSync = require('browser-sync');
var minifyCSS   = require('gulp-minify-css');
var rename      = require('gulp-rename');
var del         = require('del');

var baseTask  = 'modules.frontend.redcore_langswitcher';

var subextensionPath = './redCORE/extensions/modules/site/mod_redcore_language_switcher';
var directPath       = '../extensions/modules/site/mod_redcore_language_switcher';

var extPath   = fs.existsSync(subextensionPath) ? subextensionPath : directPath;

var mediaPath = extPath + '/media/mod_redcore_language_switcher';

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
    return gulp.src([
	        mediaPath + '/css/**'
    	])
		.pipe(gulp.dest(config.wwwDir + '/media/mod_redcore_language_switcher/css'));
});

// Styles
gulp.task('styles:' + baseTask, function () {
	return gulp.src([
			mediaPath + '/css/*.css',
			'!' + mediaPath + '/css/*.min.css'
		])
		.pipe(gulp.dest(config.wwwDir + '/media/mod_redcore_language_switcher/css'))
		.pipe(minifyCSS())
		.pipe(rename(function (path) {
				path.basename += '.min';
		}))
		.pipe(gulp.dest(mediaPath + '/css'))
		.pipe(gulp.dest(config.wwwDir + '/media/mod_redcore_language_switcher/css'));
});


// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':module',
		'watch:' + baseTask + ':styles'
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

// Watch: Styles
gulp.task('watch:' + baseTask + ':styles', function() {
    gulp.watch([
    	mediaPath + '/css/*.css',
    	'!' + mediaPath + '/css/*.min.css'
    	],
		{ interval: config.watchInterval },
		['styles:' + baseTask, browserSync.reload]);
});
