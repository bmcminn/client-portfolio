<?php

  if (preg_match('/\.(?:png|jpg|jpeg|gif|css|less)$/', filter_input(INPUT_SERVER, 'REQUEST_URI'))) {
    return false;    // serve the requested resource as-is.
  }


  // Set default timezone so we don't depend on the system timezone being configured correctly
  date_default_timezone_set('America/Chicago');


  // Define constants
  define('DS', DIRECTORY_SEPARATOR);
  define('VIEWS_DIR', __DIR__.DS.'_views'.DS);
  define('APP_DIR', __DIR__.DS.'_app');
  define('BASE_DIR', __DIR__);
  define('PROJECT_DIR', __DIR__.DS.'_projects');
  define('HANDLEBARS_EXT', '.handlebars');


  // load Composer modules
  require 'vendor/autoload.php';


  // setup Handlebars
  use Handlebars\Handlebars;

  $handlebars = new Handlebars([
    'loader'          => new \Handlebars\Loader\FilesystemLoader(VIEWS_DIR)
  , 'partials_loader' => new \Handlebars\Loader\FilesystemLoader(VIEWS_DIR, ['prefix' => '_'])
  ]);

  require '_app/helpers.php';


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
