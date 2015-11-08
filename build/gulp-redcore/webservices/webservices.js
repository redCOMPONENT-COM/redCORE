var gulp = require('gulp');

// Third part extension using redCORE
try {
	var config = require('../../../../build/gulp-config.json');
}
// redCORE repo
catch(err) {
	var config = require('../../../build/gulp-config.json');
}

var extensions = require('../../../build/gulp-extensions.json');

/**
 * Get the list of the webservices from paths
 *
 * @return  array
 */
function getWebservices() {
	var results = [];

	if (extensions && extensions.hasOwnProperty('webservices')) {
		var sourceArray = extensions.webservices;

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
