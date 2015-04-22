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
    $json = file_get_contents($path);
    $json = preg_replace('/[^:]\/\/[\s\S]+?(\r|\n)/', '', $json); // remove comments
    return json_decode($json, true);
  }


  /**
   * [console description]
   * @param  [type] $data [description]
   * @return [type]       [description]
   */
  function console($data) {
    echo "<script>console.log(JSON.parse('", json_encode($data), "'));</script>";
  }



  function getPartials($viewsPath) {
    $files = glob($viewsPath.DS.'_*');
    $partials = [];

    foreach ($files as $index => $filepath) {
      $name = array_pop(explode(DS, $filepath));
      $name = preg_replace('/\.[^.]+$/','',$name);
      $partials[$name] = file_get_contents($filepath);
    }

    return $partials;
  }


  function getHelpers() {
    // TODO: make a helpers.php with an array of all Handlebars helpers
    return [];
  }


