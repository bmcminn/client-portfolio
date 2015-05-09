<?php

  //
  // HOME ROUTE
  //
  map(BASE_URL.'/', function() {

    // if the an admin hasn't been registered...
    if (defined('FLAG_REGISTER_ADMIN')) {
      return redirect('/register');
    }

    checkUserSession();

    // TODO: Fix this problem with BASE_URL
    // return redirect('/login');
  });



  map(BASE_URL.'/register', function() {

    registerAdmin();

  });



  //
  // ADMIN STUFF
  //

  map(BASE_URL.'/admin', function($db) {

    global $appModel, $handlebars;

    setLastRoute(BASE_URL.'/admin');

    checkUserSession();

    getUserData();

    echo $handlebars->render('admin', $appModel);
  });



  map(BASE_URL.'/admin/register', function($db) {

    checkUserSession();

  // $user = [
  //   "fname"       =>  "Bob"
  // , "lname"       =>  "Belcher"
  // , "clientName"  =>  "Bob's Burgers"
  // , "phone"       =>  "135-555-1234"
  // , "email"       =>  "bob@bobsburgers.com"
  // , "username"    =>  "bobby"
  // , "password"    =>  "sdjfklsdjl"
  // , "address" => [
  //     "street"      => "4100 Lost Avenue"
  //   , "city"        => "Wharfington"
  //   , "state"       => "Pennsylvania"
  //   , "stateShort"  => "PA"
  //   , "zip"         => "83565"
  //   ]
  // ];

  });



  //
  // LOGIN STUFF
  //

  map(BASE_URL.'/logout', function() {
    logUserOut();
  });


  map(BASE_URL.'/login', function($db, $passHash) {
    global $appModel, $handlebars;

    checkUserSession();
    loginUser();

    echo $handlebars->render('login', $appModel);
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


    // Get .zip folders
    $zip = glob("_projects/{$projectPath}/*.zip");

    if (!count($zip) > 0) {
      $zip = null;
    } else {
      foreach ($zip as $file => $filePath) {
        $zip[$file] = [
          'name' => preg_replace('/-/', ' ', basename($filePath, '.zip'))
        , 'path' => $filePath
        , 'size' => round(filesize($filePath)/1024/1024, 1) . 'Mb'
        ];
      }
    }


    // Get our thumbnails
    $thumbs = glob("_projects/{$projectPath}/*.{jpg,jpeg,png,gif}", GLOB_BRACE);

    if (!count($thumbs) > 0) {
      $thumbs = null;
    } else {
      foreach ($thumbs as $thumb => $path) {

        // Get image size and orientation
        $sizes = getimagesize($path);
        $orientation = 'portrait';

        if ($sizes[0] > $sizes[1]) {
          $orientation = 'landscape';
        }

        // Get image nicename
        $name = preg_replace('/\.[\w\d]+/i', '', basename($path));

        $thumbs[$thumb] = [
          'path'        => BASE_URL . "/{$path}"
        , 'name'        => $name
        , 'nicename'    => ucwords(preg_replace('/[-_\s]/', ' ', $name))
        , 'orientation' => $orientation
        , 'width'       => $sizes[0]
        , 'height'      => $sizes[1]
        ];
      }
    }


    // recompile app model with project model data
    $appModel = array_replace_recursive(
      $appModel
    , requireJSON("_projects/{$projectPath}/project.json")
    , [
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
  // HTTP CODE HANDLEING PAGES
  //
  map(404, function ($code) {
    global $appModel, $handlebars;

    $appModel['code'] = $code;

    echo $handlebars->render('httpcode', $appModel);
  });


