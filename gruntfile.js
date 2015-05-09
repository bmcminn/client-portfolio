module.exports = function(grunt) {

  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

    date: {
      year: new Date().getFullYear()
    },

    banner: [
      "/**"
    , " * © <%= date.year %> <%= pkg.author %>"
    , " */"
    , ""
    ].join('\n'),

    jsonlint: {
      json: {
        src: [
          '_projects/**/*.json'
        ]
      }
    },

    jshint: {
      options: {
        ignores: [

        ],
        undef: true,
        laxcomma: true,
        laxbreak: true,
        unused: false,
        globals: {
          module: true,
          require: true,
          console: true,
          jQuery: true,
          $: true,
          Modernizr: true
        }
      },

      all: [
        'gruntfile.js'
      , '_resources/**/*.js'
      ]
    },


    uglify: {
      dev: {
        options: {
          mangle: false,
          screwIE8: true,
          banner: "<%= banner %>",
          preserveComments: true,
          beautify: true,
          compress: false
        },
        files: {
          'resources/main.js': ['_resources/js/main.js']
        }
      },
      build: {
        options: {
          compress: true,
          mangle: true,
          report: 'gzip'
        },
        files: {
          'resources/main.js': ['_resources/js/main.js']
        }
      }
    },


    stylus: {
      dev: {
        options: {
          banner: "<%= banner %>"
        , compress: false
        , linenos: true
        },
        files: {
          './resources/main.css': './_resources/stylus/main.styl'
        , './resources/login.css': './_resources/stylus/login.styl'
        }
      },

      build: {
        options: {
          banner: "<%= banner %>"
        },
        files: {
          './resources/main.css': './_resources/stylus/main.styl'
        }
      }
    },


    watch: {
      styles: {
        files: [
          '_resources/**/*.styl'
        ],
        tasks: ['stylus:dev']
      },
      json: {
        files: [
          '**/*.json'
        ],
        tasks: ['jsonlint']
      },
      javascript: {
        files: [
          '_resources/**/*.js'
        ],
        tasks: ['jshint', 'uglify:dev']
      }
    },


    php: {
      dist: {
        options: {
          port: 3005,
          keepalive: true,
          router: "index.php",
          directives: {
            'error_log': require('path').resolve('_logs/error.log')
          }
        }
      }
    }

  });


  require('load-grunt-tasks')(grunt);


  grunt.loadTasks('./tasks/');


  grunt.registerTask('default', [
    'jsonlint'
  , 'jshint'
  , 'stylus:dev'
  , 'uglify:dev'
  ]);



  grunt.registerTask('build', [
    'jsonlint'
  , 'jshint'
  , 'stylus:build'
  , 'uglify:build'
  ]);


};
