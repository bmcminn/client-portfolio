
module.exports = {

  check: [
    'gruntfile.js'
  , '_assets/js/**/*.js'
  ],

  options: {
    ignores: [
      '_assets/js/libs/**/*.js'
    ]

  , undef: true
  , laxcomma: true
  , laxbreak: true
  , unused: false
  , globals: {
      console: true
    , window: true
    , document: true
    , setTimeout: true
    , typeOf: true
    , clearTimeout: true
    , jQuery: true
    , module: true
    , define: true
    , require: true
    , Modernizr: true
    , process: true
    }
  }

};
