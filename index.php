<?php

  if (preg_match('/\.(?:png|jpg|jpeg|gif|css|less)$/', filter_input(INPUT_SERVER, 'REQUEST_URI'))) {
    return false;    // serve the requested resource as-is.
  }

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


  // Set default timezone so we don't depend on the system timezone being configured correctly
  date_default_timezone_set('America/Chicago');


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

  // // define our config as a constant
  // $handlebarsConfig = [
  //   // define flags
  //   // 'flags' =>

  //   // get partials from our VIEWS directory
  // , 'partials' => getPartials(VIEWS)

  //   // load all helpers
  // , 'helpers'  => require "helpers.php"
  // ];


  // // get project layout file
  // $template = file_get_contents(VIEWS.DS.'photo.hbs');

  // // compile and prep handlebars template
  // $template = LightnCandy::compile($template, $handlebarsConfig);
  // $renderer = LightnCandy::prepare($template);

  // // render our template to the page
  // echo $template = $renderer($appModel);


  // Load our default routes
  require APP_DIR . DS . "routes.php";

  // Dispatch Throwdown!
  dispatch();
