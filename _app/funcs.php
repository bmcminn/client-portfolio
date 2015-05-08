<?php

  /**
   * Returns an associative array from a JSON file
   * @param  string $path Path to JSON file
   * @return array        decoded JSON data as associative array
   */
  function requireJSON($path=null) {
    if (!$path) {
      throw new Exception("\"requireJSON()\" requires a path argument", 1);
    }

    $path = pathify($path);

    // get file relative to BASE_DIR
    $json = file_get_contents(BASE_DIR.DS.$path);

    // remove all comments
    $json = preg_replace('/[^:]\/\/[\s\S]+?(\r|\n)/', '', $json); // remove comments

    return json_decode($json, true);
  }



  /**
   * Runs the user login routine
   * @return null
   */
  function loginUser() {

    global $appModel, $handlebars, $db, $passHash;

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

            // unset various user data we don't want to update
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

            // set our SESSION user data
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

    return null;
  }



  /**
   * Checks if the 'user' session data has been established
   * @return [type] [description]
   */
  function checkUserSession() {

    // if the user object is created and the session is still valid
    if (isset($_SESSION['user']) && (time() - $_SESSION['user']['timestamp'] < SESSION_TIMEOUT)) {

      // if we go to the login route, take us back to the admin
      if (onRoute('login')) {
        return redirect('/admin');
      }

    // the user is either not logged in or their session timed out
    } else {

      // if they aren't on the login view, they should be
      if (!onRoute('login')) {
        return redirect('/login');
      }
    }

    return null;
  }



  /**
   * [getUserData description]
   * @return [type] [description]
   */
  function getUserData() {
    global $db, $appModel;

    // get user data
    $user = $db->users->find(function($document) {
      return $document["username"] === $_SESSION['user']['username'];
    });

    if ($user->valid()) {
      $user = $user->current();

      $_SESSION['user'] = $user;

      // unset data that shouldn't be in here
      unset($user['_id']);
      unset($user['lastLoginLocation']);
      unset($user['password']);

      // merge user data into $appModel
      $appModel = array_replace_recursive(
        $appModel,
        ['user' => $user]
        );

      // console($user, '$user', 'debug');
      console($appModel, '$appModel', 'debug');
    }

    return null;
  }



  /**
   * Checks the curring route and
   * @param  string $route The route partial we should check for
   * @return bool
   */
  function onRoute($route) {
    return strpos(REQUEST_URI, $route) ? true : false;
  }



  /**
   * [setLastRoute description]
   * @param [type] $route [description]
   */
  function setLastRoute($route) {
    $_SESSION['lastRoute'] = $route;
    return null;
  }



  /**
   * Logs out the user by destroying the session
   * @return [type] [description]
   */
  function logUserOut() {
    session_unset();
    session_destroy();
    return redirect('/login');
  }



  /**
   * [filterVar description]
   * @param  [type] $var [description]
   * @return [type]      [description]
   */
  function filterVar($var) {
    return filter_var($var, FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
  }



  /**
   * Dumps data into a JS console.log command
   * @param  [type] $data [description]
   * @return [type]       [description]
   */
  function console($data, $alias=null, $type="log") {

    if ($alias) {
      $alias = "\"{$alias}\",";
    } else {
      $alias = '';
    }

    echo "<script>console.{$type}({$alias}JSON.parse('", preg_replace('/\'/', "\\'", json_encode($data)), "'));</script>";
  }



  /**
   * Gets all handlebar partial files from the globally defined VIEWS directory
   * @param  string $viewsPath path to the views directory where partials are kept
   * @return array             key->val array of partials file contents
   */
  function getPartials() {
    $files = glob(VIEWS.DS.'_*'.HANDLEBARS_EXT);
    $partials = [];

    foreach ($files as $index => $filepath) {
      $name = array_pop(explode(DS, $filepath));
      $name = preg_replace('/\.[^.]+$/','',$name);

      $markdown = new ParsedownExtra();
      $file = file_get_contents($filepath);

      $partials[$name] = $markdown->text($file);
    }

    return $partials;
  }



  /**
   * [pathify description]
   * @param  [type] $path [description]
   * @return [type]       [description]
   */
  function pathify($path) {
    // remove leading directory separator
    $path = preg_replace('/^[\\\\\/]/', '', $path);

    // sub all directory separators with proper separator
    $path = preg_replace('/[\\\\\/]/', DS, $path);

    return $path;
  }



  /**
   * [urlify description]
   * @param  [type] $path [description]
   * @return [type]       [description]
   */
  function urlify($path) {
    // remove leading directory separator
    $path = preg_replace('/^[\\\\\/]/', '', $path);

    // sub all directory separators with proper separator
    $path = preg_replace('/[\\\\\/]/', '/', $path);

    return $path;
  }