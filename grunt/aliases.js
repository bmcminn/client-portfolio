// grunt/tasks/default.js

module.exports = function(grunt) {

  //
  // DEPLOYMENT CONFIG
  //

  grunt.registerTask('default', [
    'jsonlint'
  , 'jshint'
  , 'stylus:dev'
  , 'uglify:dev'
  ]);


  //
  // BUILD TASK CONFIG
  //

  grunt.registerTask('build', [
    'jsonlint'
  , 'jshint'
  , 'stylus:build'
  , 'uglify:build'
  // TODO: add svgo to grunt tasks
  ]);


  //
  // DEPLOYMENT TASK CONFIG
  //

  grunt.registerTask('deploy', [
    'build'
  , 'ftp_push:init'
  ]);


  grunt.registerTask('update-projects', [
    'build'
  , 'ftp_push:projects'
  ]);


  grunt.registerTask('update-site', [
    'build'
  , 'ftp_push:site'
  ]);


  grunt.registerTask('update-app', [
    'build'
  , 'ftp_push:app'
  ]);

};
