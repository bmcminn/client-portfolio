
var path  = require('path')
  , opts  = require(path.resolve('grunt','ftp.options.js'))
  ;


// update options
opts.port   = 21;
opts.debug  = false;


module.exports = {
  build: {
    options: opts
  , files: [
      {
        expand: true
      , cwd: '.'
      , src: [
          // FOLDERS
            '_app/**'
          , '_config/**'
          , '_views/**'
          , 'content/**'

          // OMIT THESE
          , '!**/orig/*.*'
          , '!**/cache/**/*.php'

          // FILES
          , '\.htaccess'
          , 'index.php'
        ]
      }
    ]
  }

, init: {
    options: opts
  , files: [
      {
        expand: true
      , cwd: '.'
      , src: [
          // FOLDERS
            '_app/**'
          , '_config/**'
          , '_views/**'
          , 'content/**'
          , 'vendor/**'

          // OMIT THESE
          , '!**/orig/*.*'
          , '!**/cache/**/*.php'
          , '!vendor/**/*.test'
          , '!vendor/**/deprecated/**'
          , '!vendor/**/test*/**'
          , '!vendor/**/Test*/**'
          , '!vendor/**/doc/**'
          , '!vendor/**/ext/**'
          , '!vendor/**/phpunit*'
          , '!vendor/**/README*'
          , '!vendor/**/*LICENSE*'
          , '!vendor/**/CHANGELOG*'
          , '!vendor/**/*travis*'
          , '!vendor/**/composer*'
          , '!vendor/**/*.lock'

          // FILES
          , '\.htaccess'
          , 'index.php'
        ]
      }
    ]
  }

, projects: {
    options: opts
  , files: [
      {
        expand: true
      , cwd: '.'
      , src: [
          // FOLDERS
            '_config/**'
          , 'content/projects/**'

          // OMIT THESE
          , '!**/orig/*.*'
        ]
      }
    ]
  }

, site: {
    options: opts
  , files: [
      {
        expand: true
      , cwd: '.'
      , src: [
          // FOLDERS
            '_config/**'
          , '_views/**'
          , 'content/css/**'
          , 'content/images/**'
          , 'content/js/**'
          , 'content/pages/**'

          // OMIT THESE
          , '!**/cache/**/*.php'
        ]
      }
    ]
  }

, app: {
    options: opts
  , files: [
      {
        expand: true
      , cwd: '.'
      , src: [
          // FOLDERS
            '_app/**'
          , '_config/**'
          , '_views/**'
          , 'vendor/**'

          // OMIT THESE
          , '!**/cache/**/*.php'
          , '!vendor/**/*.test'
          , '!vendor/**/deprecated/**'
          , '!vendor/**/test*/**'
          , '!vendor/**/Test*/**'
          , '!vendor/**/doc/**'
          , '!vendor/**/ext/**'
          , '!vendor/**/phpunit*'
          , '!vendor/**/README*'
          , '!vendor/**/*LICENSE*'
          , '!vendor/**/CHANGELOG*'
          , '!vendor/**/*travis*'
          , '!vendor/**/composer*'
          , '!vendor/**/*.lock'

          // FILES
          , '\.htaccess'
          , 'index.php'
        ]
      }
    ]
  }


};
