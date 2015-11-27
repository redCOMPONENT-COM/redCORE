var gulp = require('gulp');

// Third part extension using redCORE
try {
	var config = require('../../../build/gulp-config.json');
}
// redCORE repo
catch(err) {
	var config = require('../../build/gulp-config.json');
}

// Check if config has defaultTasks defined
var defaultTasks = config.hasOwnProperty('defaultTasks') ? config.defaultTasks : ["copy", "watch", "copyRedcore", "watchRedcore", "browser-sync"];

// Clean redcore addons
gulp.task('cleanRedcore', [
	'clean:cli',
	'clean:webservices'
], function() {
	return true;
});

// Copy redcore addons
gulp.task('copyRedcore', [
	'copy:cli',
	'copy:webservices'
], function() {
	return true;
});

// Watch redcore addons
gulp.task('watchRedcore', [
	'watch:cli',
	'watch:webservices'
], function() {
	return true;
});

// Default task
gulp.task('default', defaultTasks, function() {
});
