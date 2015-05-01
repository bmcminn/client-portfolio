<?php

  //
  // HOME ROUTE
  //
  map(BASE_URL.'/', function() {
    // TODO: Add 404 page here
  });



  //
  // PROJECT ROUTES
  //
  map(BASE_URL.'/<projectname>', function($params) {
    global $appModel, $handlebars;

    $projectPath = $params['projectname'];

    if (!file_exists(PROJECT_DIR.DS.$projectPath)) {
      error(404);
      return;
    }

    // Do we have a zip folder?
    $zip = glob("_projects/{$projectPath}/*.zip");

    if (!count($zip) > 0) {
      $zip = null;
    } else {
      foreach ($zip as $file => $filePath) {
        $zip[$file] = [
          'name' => preg_replace('/-/', ' ', basename($filePath, '.zip'))
        , 'path' => $filePath
        ];
      }
    }


    // Get our thumbnails
    $thumbs = glob("_projects/{$projectPath}/*.{jpg,jpeg,png,gif}", GLOB_BRACE);

    foreach ($thumbs as $thumb => $path) {
      $thumbs[$thumb] = BASE_URL . "/$path";
    }


    // recompile app model with project model data
    $appModel = array_replace_recursive(
      $appModel,
      requireJSON("_projects/{$projectPath}/project.json"),
      [
        'projectPath' =>  BASE_URL . "/{$projectPath}/"
      , 'resources'   =>  BASE_URL.'/resources/'
      , 'download'    =>  $zip
      , 'thumbnails'  =>  $thumbs
      ]
    );


    // handle download limits on our project
    // if (isset($appModel['limit'])) {
    //   echo $appModel['limit'];

    //   if (!file_exists("_projects/{$projectPath}/counter.php")) {
    //     file_put_contents("_projects/{$projectPath}/counter.php", "<?php return 1;");
    //   }

    //   $counter = require "_projects/{$projectPath}/counter.php";
    //   $counter += 1;
    //   file_put_contents("_projects/{$projectPath}/counter.php", "<?php return {$counter};");

    //   $appModel['counter']

    //   if ($counter > $appModel['limit']) {
    //     echo $handlebars->render('limit', $appModel);
    //     return false;
    //   }
    // }


    // render template
    echo $handlebars->render('photo', $appModel);
  });



  //
  // 404 PAGE
  //
  map(404, function ($code) {
    global $appModel, $handlebars;

    $appModel['code'] = $code;

    echo $handlebars->render('httpcode', $appModel);
  });
