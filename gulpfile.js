var gulp = require('gulp');

var extension = require('./package.json');

var requireDir = require('require-dir');
var zip        = require('gulp-zip');

var config    = require('./gulp-config.json');

var jgulp   = requireDir('./node_modules/joomla-gulp', {recurse: true});
var redcore = requireDir('./node_modules/gulp-redcore', {recurse: true});

// Override of the release script
gulp.task('release', function () {
	return gulp.src([
			'./**/*',
			'./**/.gitkeep',
			"!./**/bower.json",
			"!./**/scss/**",
			"!./**/less/**",
			"!./**/build.*",
			"!./**/build/**",
			"!./**/*.md",
			"!./**/docs/**",
			"!./**/joomla-gulp/**",
			"!./**/jgulp/**",
			"!./**/gulp**",
			"!./**/gulp**/**",
			"!./**/gulpfile.js",
			"!./**/node_modules/**",
			"!./**/node_modules/**/.*",
			"!./**/package.json",
			"!./**/releases/**",
			"!./**/releases/**/.*",
			"!./src/**",
			'!./**/sample/**',
			'!./**/sample/.*',
			'!./**/tests/**',
			'!./**/tests/.*',
			"!./**/*.sublime-*",
			"!./**/*.sh",
			"!./**/composer.json",
			"!./**/phpunit*.xml"
		])
		.pipe(zip(extension.name + '-' + extension.version + '.zip'))
		.pipe(gulp.dest('releases'));
});
