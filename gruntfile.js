
module.exports = function(grunt) {

	'use strict';

	var path  = require('path')
		;


	//-----------------------------------------------------------------


	require('time-grunt')(grunt);


	//-----------------------------------------------------------------


	require('load-grunt-config')(grunt, {
		configPath: path.join(process.cwd(), 'grunt')
	, data: {

		}
	});

	//-----------------------------------------------------------------

	if (grunt.file.exists('./tasks')) {
		grunt.loadTasks('./tasks/');
	}

};
