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
 * Get the list of the cli from paths
 *
 * @return  array
 */
function getCli() {
	var results = [];

	if (extensions && extensions.hasOwnProperty('cli')) {
		var sourceArray = extensions.cli;

		for (index = 0; index < sourceArray.length; ++index) {
		    results.push(sourceArray[index]);
		}
	}

	return results;
}

/**
 * Function to ease the cli
 *
 * @param   string  baseTask  Task to use as root. Example: 'clean:modules.frontend'
 *
 * @return  array
 */
function getCliTasks(baseTask) {
	var cli = getCli();
	var tasks = [];

	for (index = 0; index < cli.length; ++index) {
	    tasks.push(baseTask + '.' + cli[index]);
	}

	return tasks;
}

// Clean
gulp.task('clean:cli',
		getCliTasks('clean:cli'),
	function() {
		return true
});

// Copy
gulp.task('copy:cli',
		getCliTasks('copy:cli'),
	function() {
		return true;
});

// Watch
gulp.task('watch:cli',
		getCliTasks('watch:cli'),
	function() {
		return true;
});
