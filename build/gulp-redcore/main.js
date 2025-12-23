var gulp = require('gulp');

var config = require('./config.js');
var task = require('./task-helper')(gulp);

// Check if config has defaultTasks defined
var defaultTasks = config.hasOwnProperty('defaultTasks') ? config.defaultTasks : ["copy", "watch", "copyRedcore", "watchRedcore", "browser-sync"];

// Clean redcore addons
task('cleanRedcore', [
	'clean:cli',
	'clean:webservices'
], function() {
	return true;
});

// Copy redcore addons
task('copyRedcore', [
	'copy:cli',
	'copy:webservices'
], function() {
	return true;
});

// Watch redcore addons
task('watchRedcore', [
	'watch:cli',
	'watch:webservices'
], function() {
	return true;
});

// Default task
task('default', defaultTasks, function() {
});
