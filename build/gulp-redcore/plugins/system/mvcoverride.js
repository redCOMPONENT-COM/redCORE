var gulp = require('gulp');
var fs   = require('fs');

// Third part extension using redCORE
try {
	var config = require('../../../../../gulp-config.json');
}
catch(err) {
	var config = require('../../../../gulp-config.json');
}

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');

var baseTask  = 'plugins.system.mvcoverride';

var subextensionPath = './redCORE/plugins/system/mvcoverride';
var directPath       = './plugins/system/mvcoverride';

var extPath   = fs.existsSync(subextensionPath) ? subextensionPath : directPath;

// Clean
gulp.task('clean:' + baseTask, function() {
	return del(config.wwwDir + '/plugins/system/mvcoverride', {force : true});
});

// Copy
gulp.task('copy:' + baseTask, ['clean:' + baseTask], function() {
	return gulp.src( extPath + '/**')
		.pipe(gulp.dest(config.wwwDir + '/plugins/system/mvcoverride'));
});

// Watch
gulp.task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':plugin'
	],
	function() {
});

// Watch: plugin
gulp.task('watch:' + baseTask + ':plugin', function() {
	gulp.watch(extPath + '/**/*', ['copy:' + baseTask, browserSync.reload]);
});
