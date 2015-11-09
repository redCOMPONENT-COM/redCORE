var gulp = require('gulp');
var fs   = require('fs');

// Third part extension using redCORE
try {
	var config = require('../../../../build/gulp-config.json');
}
catch(err) {
	var config = require('../../../build/gulp-config.json');
}

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');
var less        = require('gulp-less');
var minifyCSS   = require('gulp-minify-css');
var rename      = require('gulp-rename');
var uglify      = require('gulp-uglify');

var baseTask  	= 'media.redcore';
var baseFolder  = fs.existsSync('./redCORE') ? './redCORE' : '..';
var extPath = baseFolder + '/extensions/media/redcore';
var buildPath = baseFolder + '/build';

// Clean
gulp.task('clean:' + baseTask, function() {
	return del(config.wwwDir + '/media/redcore', {force: true});
});

// Copy
gulp.task('copy:' + baseTask, ['clean:' + baseTask],
	function() {
		return gulp.src([
				extPath + '/**',
				extPath + '/**/.gitkeep'
			])
			.pipe(gulp.dest(config.wwwDir + '/media/redcore'))/* &&
			gulp.src(extPath + '/../redcore.xml')
					.pipe(gulp.dest(config.wwwDir + '/administrator/components/com_redcore'))*/;
});

// LESS
gulp.task('less:' + baseTask,
	[
		'less:' + baseTask + ':component',
		'less:' + baseTask + ':component.bs3'
	],
	function() {
});

// LESS: Component
gulp.task('less:' + baseTask + ':component', function () {
	return gulp.src(buildPath + '/less/component.less')
		.pipe(less({paths: [buildPath + '/less']}))
		.pipe(gulp.dest(extPath + '/css'))
		.pipe(gulp.dest(config.wwwDir + '/media/redcore/css'))
		.pipe(minifyCSS())
		.pipe(rename(function (path) {
				path.basename += '.min';
		}))
		.pipe(gulp.dest(extPath + '/css'))
		.pipe(gulp.dest(config.wwwDir + '/media/redcore/css'));
});

// LESS: Component Bootstrap3
gulp.task('less:' + baseTask + ':component.bs3', function () {
	return gulp.src(buildPath + '/less/component.bs3.less')
		.pipe(less({paths: [buildPath + '/less']}))
		.pipe(gulp.dest(extPath + '/css'))
		.pipe(gulp.dest(config.wwwDir + '/media/redcore/css'))
		.pipe(minifyCSS())
		.pipe(rename(function (path) {
				path.basename += '.min';
		}))
		.pipe(gulp.dest(extPath + '/css'))
		.pipe(gulp.dest(config.wwwDir + '/media/redcore/css'));
});

// Scripts
gulp.task('scripts:' + baseTask, function () {
	return gulp.src([
			extPath + '/js/**/*.js',
			'!' + extPath + '/js/lib/**',
			'!' + extPath + '/js/**/*.min.js'
		])
		.pipe(gulp.dest(config.wwwDir + '/media/redcore/js'))
		.pipe(uglify())
		.pipe(rename(function (path) {
				path.basename += '.min';
		}))
		.pipe(gulp.dest(extPath + '/js'))
		.pipe(gulp.dest(config.wwwDir + '/media/redcore/js'));
});

// Styles
gulp.task('styles:' + baseTask, function () {
	return gulp.src([
			extPath + '/css/*.css',
			'!' + extPath + '/css/lib/**',
			'!' + extPath + '/css/*.min.css'
		])
		.pipe(gulp.dest(config.wwwDir + '/media/redcore/css'))
		.pipe(minifyCSS())
		.pipe(rename(function (path) {
				path.basename += '.min';
		}))
		.pipe(gulp.dest(extPath + '/css'))
		.pipe(gulp.dest(config.wwwDir + '/media/redcore/css'));
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':less',
		'watch:' + baseTask + ':scripts',
		'watch:' + baseTask + ':styles'
	],
	function() {
});

// Watch: LESS
gulp.task('watch:' + baseTask + ':less',
	function() {
		gulp.watch(
			[buildPath + '/less/**/*.less'],
			{ interval: config.watchInterval },
			['less:' + baseTask, browserSync.reload]
		);
});

// Watch: Scripts
gulp.task('watch:' + baseTask + ':scripts',
	function() {
		gulp.watch([
			extPath + '/js/**/*.js',
			'!' + extPath + '/js/lib/**',
			'!' + extPath + '/js/**/*.min.js'
		],
		{ interval: config.watchInterval },
		['scripts:' + baseTask, browserSync.reload]);
});

// Watch: Styles
gulp.task('watch:' + baseTask + ':styles',
	function() {
		gulp.watch([
			extPath + '/css/*.css',
			'!' + extPath + '/css/lib/**',
			'!' + extPath + '/css/*.min.css'
		],
		{ interval: config.watchInterval },
		['styles:' + baseTask, browserSync.reload]);
});
