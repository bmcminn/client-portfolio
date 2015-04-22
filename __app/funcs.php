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
   * Dumps data into a JS console.log command
   * @param  [type] $data [description]
   * @return [type]       [description]
   */
  function console($data, $type="log") {
    echo "<script>console.$type(JSON.parse('", json_encode($data), "'));</script>";
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
