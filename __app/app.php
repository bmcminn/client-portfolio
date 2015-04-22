<?php

  // Define constants
  define('DS', DIRECTORY_SEPARATOR);
  define('VIEWS', __DIR__.DS.'..'.DS.'__views');
  define('BASE_DIR', __DIR__.DS.'..');
  define('HANDLEBARS_EXT', '.hbs');


  // load Composer modules
  require '../vendor/autoload.php';



  // hookup Whoops
  $whoops = new \Whoops\Run;
  $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
  $whoops->register();


  // Set default timezone so we don't depend on the system timezone being configured correctly
  date_default_timezone_set('America/Chicago');


  // get the project folder path
  $path = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING);
  $path = explode('/', $path);
  array_pop($path);
  $path = implode('/', $path);


  // Project Data Here
  $appModel = array_replace_recursive(
    requireJSON(__DIR__.DS.'__author.json'),
    requireJSON(BASE_DIR.DS.$path.DS.'__project.json'),
    [
      'path'        =>  $path
    , 'projectPath' =>  $path
    , 'resources'   =>  '/resources/'
    , 'download'    =>  glob('*.zip')[0]
    , 'thumbnails'  =>  glob('*.{jpg,jpeg,png,gif}', GLOB_BRACE)

    , 'date' => [
        'year'      => date('Y')
      , 'month'     => date('M')
      , 'day'       => date('j')
      , 'dayFull'   => date('l')
      , 'monthFull' => date('F')
      ]
    ]
  );


  // define our config as a constant
  $handlebarsConfig = [
    // define flags
    'flags' =>  LightnCandy::FLAG_PARENT
              | LightnCandy::FLAG_THIS

    // get partials from our VIEWS directory
  , 'partials' => getPartials(VIEWS)

    // load all helpers
  , 'helpers'  => require "helpers.php"
  ];



  $template = file_get_contents(VIEWS.DS.'photo.hbs');

  $template = LightnCandy::compile($template, $handlebarsConfig);
  $renderer = LightnCandy::prepare($template);
  echo $template = $renderer($appModel);

  console($appModel);
