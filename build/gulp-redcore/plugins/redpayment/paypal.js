var gulp = require('gulp');
var fs   = require('fs');

var config = require('../../config.js');
var task = require('../../task-helper')(gulp);

// Dependencies
var browserSync = require('browser-sync');
var del         = require('del');

var baseTask  = 'plugins.redpayment.paypal';

var subextensionPath = './redCORE/extensions/plugins/redpayment/paypal';
var directPath       = '../extensions/plugins/redpayment/paypal';

var extPath   = fs.existsSync(subextensionPath) ? subextensionPath : directPath;

// Clean
task('clean:' + baseTask, function() {
	return del(config.wwwDir + '/plugins/redpayment/paypal', {force : true});
});

// Copy
task('copy:' + baseTask, ['clean:' + baseTask], function() {
	return gulp.src( extPath + '/**')
		.pipe(gulp.dest(config.wwwDir + '/plugins/redpayment/paypal'));
});

// Watch
task('watch:' + baseTask,
	[
		'watch:' + baseTask + ':plugin'
	],
	function() {
});

// Watch: plugin
task('watch:' + baseTask + ':plugin', function() {
	gulp.watch(extPath + '/**/*',
		{ interval: config.watchInterval },
		gulp.series('copy:' + baseTask, browserSync.reload));
});
