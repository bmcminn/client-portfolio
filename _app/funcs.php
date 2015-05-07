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
   * Checks if the 'user' session data has been established
   * @return [type] [description]
   */
  function checkUserStatus() {
    // TODO: remap this function to handle use session validation
    // if (isset($_SESSION['user'])) {
    //   return redirect('/admin');
    // }
  }


  function checkUserTimeout() {
    // if (isset($_SESSION['timestamp'])) {

    //   return redirect('/login');
    // }
  }


  /**
   * Logs out the user by destroying the session
   * @return [type] [description]
   */
  function logUserOut() {
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