<?php

  // Disable CORS for now
  // TODO: determine if we can submit logins via command prompt utilities
  header("Access-Control-Allow-Origin: null");


  // Init the user session
  session_start();


  // Prevent PHP garbage collection from deleting our session
  if(!isset($_SESSION['gc_last_access']) || (time() - $_SESSION['gc_last_access']) > 60) {
    $_SESSION['gc_last_access'] = time();
  }


  // Get base url for project (allows us to nest the app within a subdir of our host)
  $filterPort = filter_var($_SERVER['SERVER_PORT'], FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
  define('SERVER_PORT',     $filterPort===80?'':":{$filterPort}");
  define('SERVER_NAME',     filter_var($_SERVER['SERVER_NAME'], FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE).SERVER_PORT);
  define('REQUEST_URI',     preg_replace('/\?*+/',  '', filter_var($_SERVER['REQUEST_URI'], FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE)));
  define('BASE_URL',        preg_replace('/\/[\d\w?&=%\-_]+$/i', '', REQUEST_URI));
  define('SITE_URL',        '//'.SERVER_NAME.BASE_URL);
  define('SESSION_TIMEOUT', 600); // 10 minutes


  // TODO: set route handler for project assets (/projects/PROJECT_NAME/*.jpg|jpeg|gif|png|zip) and validate if the user has access to them

  // serve the requested resource as-is.
  if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css|less|zip)$/', REQUEST_URI)) {
    return false;
  }


  // Setup debugging
  if (preg_match('/localhost/', SERVER_NAME)) {
    ini_set('display_errors',1);
    ini_set('display_startup_errors',1);
    error_reporting(-1);
  }


  // Set default timezone
  date_default_timezone_set('America/Chicago');


  // Define constants
  define('DS',              DIRECTORY_SEPARATOR);
  define('BASE_DIR',        __DIR__);
  define('VIEWS_DIR',       __DIR__.DS.'_views'.DS);
  define('APP_DIR',         __DIR__.DS.'_app');
  define('PROJECT_DIR',     __DIR__.DS.'_projects');
  define('DB_FOLDER',       __DIR__.DS.'_data');
  define('HANDLEBARS_EXT',  '.handlebars');


  // load Composer modules
  require 'vendor/autoload.php';

  // setup Database stuff
  require APP_DIR.DS.'database.php';




  console($_POST, '$_POST', 'info');
  console($_GET, '$_GET', 'info');
  console($_SESSION, '$_SESSION', 'info');



  //
  // setup PHPass
  //
  use Hautelook\Phpass\PasswordHash;

  $passHash = new PasswordHash(8,false);


  //
  // setup Handlebars
  //
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


  // Dispatch!
  dispatch($db, $passHash);
