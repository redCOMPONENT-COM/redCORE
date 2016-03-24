var gulp = require('gulp');

var config = require('../config.js');

/**
 * Get the list of the webservices from paths
 *
 * @return  array
 */
function getWebservices() {
	var results = [];

	if (config.extensions.hasOwnProperty('webservices')) {
		var sourceArray = config.extensions.webservices;

		for (index = 0; index < sourceArray.length; ++index) {
		    results.push(sourceArray[index]);
		}
	}

	return results;
}

/**
 * Function to ease the webservices
 *
 * @param   string  baseTask  Task to use as root. Example: 'clean:modules.frontend'
 *
 * @return  array
 */
function getWebservicesTasks(baseTask) {
	var webservices = getWebservices();
	var tasks = [];

	for (index = 0; index < webservices.length; ++index) {
	    tasks.push(baseTask + '.' + webservices[index]);
	}

	return tasks;
}

// Clean
gulp.task('clean:webservices',
		getWebservicesTasks('clean:webservices'),
	function() {
		return true
});

// Copy
gulp.task('copy:webservices',
		getWebservicesTasks('copy:webservices'),
	function() {
		return true;
});

// Watch
gulp.task('watch:webservices',
		getWebservicesTasks('watch:webservices'),
	function() {
		return true;
});
