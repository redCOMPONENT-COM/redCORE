var gulp = require('gulp');
var fs   = require('fs');

var config = require('../config.js');
var task = require('../task-helper')(gulp);

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');
var less        = require('gulp-less');
var cleanCSS    = require('gulp-clean-css');
var rename      = require('gulp-rename');
var uglify      = require('gulp-uglify');

var baseTask  	= 'media.redcore';
var baseFolder  = fs.existsSync('./redCORE') ? './redCORE' : '..';
var extPath = baseFolder + '/extensions';
var buildPath = baseFolder + '/build';

var excludedMediaScriptFolders = [
	'!' + buildPath + '/media/**/*.min.js',
	'!' + buildPath + '/media/**/Gruntfile.js',
	'!' + buildPath + '/media/**/gulpfile.js',
	'!' + buildPath + '/media/**/lib/jquery-noconflict.js'
];

var excludedMediaStypeFolders = [
	'!' + buildPath + '/media/**/*.min.css'
];

// Clean
task('clean:' + baseTask, function() {
	return true;
});

// Copy
task('copy:' + baseTask, ['clean:' + baseTask],
	function() {
		return true;
});

// LESS
task('less:' + baseTask,
	[
		'less:' + baseTask + ':component',
		'less:' + baseTask + ':component.bs3'
	],
	function() {
});

// LESS: Component
task('less:' + baseTask + ':component', function () {
	return gulp.src(buildPath + '/media/libraries/redcore/media/redcore/less/component.less')
		.pipe(less({paths: [buildPath + '/media/libraries/redcore/media/redcore/less']}))
		.pipe(gulp.dest(buildPath + '/media/libraries/redcore/media/redcore/css'));
});

// LESS: Component Bootstrap3
task('less:' + baseTask + ':component.bs3', function () {
	return gulp.src(buildPath + '/media/libraries/redcore/media/redcore/less/component.bs3.less')
		.pipe(less({paths: [buildPath + '/media/libraries/redcore/media/redcore/less']}))
		.pipe(gulp.dest(buildPath + '/media/libraries/redcore/media/redcore/css'));
});

// Scripts
task('scripts:' + baseTask, function () {
	return gulp.src(excludedMediaScriptFolders.concat([
			buildPath + '/media/**/*.js'
		]))
		.pipe(uglify())
		.pipe(rename(function (path) {
				path.basename += '.min';
		}))
		.pipe(gulp.dest(extPath + ''));
});

// Styles
task('styles:' + baseTask, function () {
	return gulp.src(excludedMediaStypeFolders.concat([
			buildPath + '/media/**/*.css'
		]))
		.pipe(cleanCSS())
		.pipe(rename(function (path) {
				path.basename += '.min';
		}))
		.pipe(gulp.dest(extPath + ''));
});

// Library files (fonts, images, ...)
task('libraries:' + baseTask, function () {
	return gulp.src([buildPath + '/media/**/lib/**',
				'!' + buildPath + '/media/**/lib/**/*.css',
				'!' + buildPath + '/media/**/lib/**/*.js',
				'!' + buildPath + '/media/**/lib/**/*.md'
		])
		.pipe(gulp.dest(extPath + ''));
});

// Watch
task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':less',
		'watch:' + baseTask + ':scripts',
		'watch:' + baseTask + ':styles',
		'watch:' + baseTask + ':libraries'
	],
	function() {
});

// Watch: LESS
task('watch:' + baseTask + ':less',
	function() {
		gulp.watch(
			[buildPath + '/media/**/less/**/*.less'],
			{ interval: config.watchInterval },
			gulp.series('less:' + baseTask, browserSync.reload)
		);
});

// Watch: Scripts
task('watch:' + baseTask + ':scripts',
	function() {
		gulp.watch(excludedMediaScriptFolders.concat([
			buildPath + '/media/**/*.js'
		]),
		{ interval: config.watchInterval },
		gulp.series('scripts:' + baseTask, browserSync.reload));
});

// Watch: Styles
task('watch:' + baseTask + ':styles',
	function() {
		gulp.watch(excludedMediaStypeFolders.concat([
			buildPath + '/media/**/*.css'
		]),
		{ interval: config.watchInterval },
		gulp.series('styles:' + baseTask, browserSync.reload));
});

// Watch: Library files (fonts, images, ...)
task('watch:' + baseTask + ':libraries',
	function() {
		gulp.watch([
			buildPath + '/media/lib/**',
			'!' + buildPath + '/media/**/lib/**/*.css',
			'!' + buildPath + '/media/**/lib/**/*.js',
			'!' + buildPath + '/media/**/lib/**/*.md'
		],
		{ interval: config.watchInterval },
		gulp.series('libraries:' + baseTask, browserSync.reload));
});
