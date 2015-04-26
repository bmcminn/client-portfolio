<?php

  // ini_set('display_errors',1);
  // ini_set('display_startup_errors',1);
  // error_reporting(-1);

  // Set default timezone so we don't depend on the system timezone being configured correctly
  date_default_timezone_set('America/Chicago');


  // Define constants
  define('DS',              DIRECTORY_SEPARATOR);
  define('VIEWS_DIR',       __DIR__.DS.'_views'.DS);
  define('APP_DIR',         __DIR__.DS.'_app');
  define('BASE_DIR',        __DIR__);
  define('PROJECT_DIR',     __DIR__.DS.'_projects');
  define('HANDLEBARS_EXT',  '.handlebars');


  // Get base url for project (allows us to nest the app within a subdir of our host)
  define('REQUEST_URI',     filter_var($_SERVER['REQUEST_URI'], FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE));
  define('BASE_URL',        preg_replace('/\/[A-Z\d-_]+$/i', '', REQUEST_URI));


  // load Composer modules
  require 'vendor/autoload.php';


  // Run preflight
  // TODO: Get preflight counters for zip files working. Requires more thought on integration with project configs
  // require '_app/preflight.php';


  // serve the requested resource as-is.
  if (preg_match('/\.(?:png|jpg|jpeg|gif|css|less|zip)$/', REQUEST_URI)) {
    return false;
  }


  // setup Handlebars
  use Handlebars\Handlebars;

  $handlebars = new Handlebars([
    'loader'          => new \Handlebars\Loader\FilesystemLoader(VIEWS_DIR)
  , 'partials_loader' => new \Handlebars\Loader\FilesystemLoader(VIEWS_DIR, ['prefix' => '_'])
  ]);

  require APP_DIR . DS . 'helpers.php';


  // hookup Whoops
  $whoops = new \Whoops\Run;
  $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
  $whoops->register();


  // Project Data Here
  $appModel = array_replace_recursive(
    requireJSON('_app/author.json'),
    [
      'resources' => '/resources/'
    , 'license'   => '_byas'
    , 'date' => [
        'year'      => date('Y')
      , 'month'     => date('M')
      , 'day'       => date('j')
      , 'dayFull'   => date('l')
      , 'monthFull' => date('F')
      ]
    ]
  );


  // Load our default routes
  require APP_DIR . DS . "routes.php";

  // Dispatch Throwdown!
  dispatch();
