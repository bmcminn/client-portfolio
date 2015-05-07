<?php

  //
  // HOME ROUTE
  //
  map(BASE_URL.'/', function() {
    checkUserStatus();

    // TODO: Fix this problem with BASE_URL
    // return redirect('/login');
  });


  //
  // ADMIN STUFF
  //

  map(BASE_URL.'/admin', function($db) {

    setLastRoute(BASE_URL.'/admin');

    checkUserStatus();

    global $appModel, $handlebars;

    echo $handlebars->render('admin', $appModel);
  });


  //
  // LOGIN STUFF
  //

  map(BASE_URL.'/logout', function() {
    logUserOut();
  });


  map(BASE_URL.'/login', function($db, $passHash) {

    checkUserStatus();

    // TODO: move ALL this into a function...

    global $appModel, $handlebars;


    $eMessage = [];

    // If the form was submitted
    // AND we have POST data
    // TODO: AND the referrer wasn't a foreign (cURL|wget) request
    if (isset($_POST['username']) && isset($_POST['password'])) {

      // alias our login creds
      $username = strtolower($_POST['username']); // lower caseing to ensure we don't have duplicate usernames
      $password = $_POST['password'];

      // check if USERNAME is an empty string
      if (empty($username)) {
        $eMessage[] = 'Username is a required field';
      }

      // check if PASSWORD is an empty string
      if (empty($password)) {
        $eMessage[] = 'Password is a required field';
      }

      // cache the posted USERNAME in our viewmodel to hydrate the field
      $appModel['loginInfo'] = [
        'username' => $_POST['username']
      ];

      // lets test our creds
      if (!empty($username) && !empty($password)) {

        if (strlen($password) > 72) {
          $eMessage[] = "Password must be 72 characters or less";
        } else {

          // Get our user data from the DB
          $user = $db->users->find(function($document) use ($username, $password, $passHash) {
            return $document["username"] === $username
                && $passHash->CheckPassword($password, $document["password"])
              ;
          });

          // User is valid, we can log them in :)
          if ($user->valid()) {

            // alias the current User record
            $user = $user->current();

            // unset various user data so we don't want to update
            unset($user['_id']);
            unset($user['password']);

            // update the user timestamp value to now
            $user = array_replace_recursive(
              $user
            , [
                'timestamp'          => time()
              , 'lastLogin'          => date('Y-m-d')
              , 'lastLoginLocation'  => $_SERVER['REMOTE_ADDR']
              ]);


            // update the timestamp for our user record
            $db->users->update(
              function($document) use ($username) {
                return $document['username'] === $username;
              },
              $user
            );

            // remove any last bits we shouldn't expose to the user
            unset($user['lastLoginLocation']);

            // set our SESSION data
            $_SESSION['user'] = $user;

            // Go to /admin
            redirect('/admin');

          } else {
            $eMessage[] = 'The username or password did not match our records';
          }
        }
      }
    }

    // add error messaging to our view model
    if (!empty($eMessage)) {
      $appModel['loginError'] = [
        'message' => $eMessage
      ];
    }


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


