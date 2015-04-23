<?php

  //
  // HOME ROUTE
  //
  map('/', function() {
    // TODO: Add 404 page here
  });



  //
  // PROJECT ROUTES
  //
  map('/<projectname>', function($params) {
    global $appModel, $handlebars;

    $path = $params['projectname'];

    if (!file_exists(PROJECT_DIR.DS.$path)) {
      error(404);
      return;
    }

    $appModel = array_replace_recursive(
      $appModel,
      requireJSON("_projects/$path/project.json"),
      [
        'projectPath' =>  "/$path/"
      , 'resources'   =>  '/resources/'
      , 'download'    =>  glob("_projects/$path/*.zip")
      , 'thumbnails'  =>  glob("_projects/$path/*.{jpg,jpeg,png,gif}", GLOB_BRACE)
      ]
    );

    // render template
    echo $handlebars->render('photo', $appModel);
  });



  //
  // 404 PAGE
  //
  map(404, function ($code) {
    global $appModel, $handlebars;

    echo $handlebars->render('httpcode', $appModel);
  });
