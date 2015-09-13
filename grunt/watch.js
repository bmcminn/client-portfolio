
module.exports = {
  js: {
    options: {
      spawn: false
    },
    files: [
      "js/**.js"
    ],
    tasks: [ "jshint" ]
  }

, styl: {
    options: {
      spawn: false
    },
    files: [
      "_assets/stylus/**/*.styl"
    ],
    tasks: [ "stylus:dev" ]
  }

, json: {
    options: {
      spawn: false
    },
    files: [
      "**.json"
    ],
    tasks: [ "jsonlint" ]
  }
};
