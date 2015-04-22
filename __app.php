<?php

  require 'vendor/autoload.php';
  require '__funcs.php';

  define('DS', DIRECTORY_SEPARATOR);

  date_default_timezone_set('America/Chicago');

  $path = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_STRING);

  $path = explode('/', $path);
  array_pop($path);
  $path = implode('/', $path);

  // Project Data Here
  $project = array_replace_recursive(
    requireJSON(__DIR__.$path.DS.'__project.json'),
    requireJSON(__DIR__.DS.'__author.json'),
    [
      'path'        =>  $path
    , 'projectPath' =>  $path
    , 'download'    =>  glob('*.zip')[0]
    , 'files'       =>  glob('*.{jpg,jpeg,png,gif}', GLOB_BRACE)
    ]
  );

  // print_r($project);

  $template = file_get_contents(__DIR__.DS.'__views'.DS.'photo.hbs');

  $handlebarConfig = [
    'flags' =>  LightnCandy::FLAG_PARENT
              | LightnCandy::FLAG_THIS

  , 'partials' => getPartials(__DIR__.DS.'__views')
  , 'helpers'  => getHelpers()
  ];

  $template = LightnCandy::compile($template, $handlebarConfig);
  $renderer = LightnCandy::prepare($template);

  // TODO: add in markdown parsing

  echo $renderer($project);

  console($project);