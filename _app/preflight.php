 <?php

  $initialRequest = filter_input(INPUT_SERVER, 'REQUEST_URI');

  // Check zips to see if they need to be counted
  if (preg_match('/\.(?:zip)$/', $initialRequest)) {

    // Does our project exist?
    if (file_exists(BASE_DIR.$initialRequest)) {

      // get project path
      $projectPath    = preg_replace('/\/[\w\d\-\_]+\.zip$/', '/', $initialRequest);
      $zipName        = basename($initialRequest, '.zip');
      $projectConfig  = file_get_contents(BASE_DIR.$projectPath.'project.json');
      $zipList        = glob(BASE_DIR.$projectPath.'*.zip');
      $zipFiles       = [];
      $counters       = '';

      // do we have limits?
      if (isset($projectConfig['limit'])) {

        // Get all project zip files
        foreach ($zipList as $zip => $filePath) {
          $zipFiles[basename($filePath, '.zip')] = 0;
        }

        // check for counters.php
        if (file_exists(BASE_DIR.$projectPath.'counters.php')) {
          $counters = json_decode(file_get_contents(BASE_DIR.$projectPath.'counters.php'));

          // update the counters index for this zip file
          if (isset($zipfiles[$zipName])) {
            $counters[$zipName] += 1;
          }

          // write the updates to the project directory
          file_put_contents(BASE_DIR.$projectPath.'counters.json', json_encode($counters));

        // initialize counters.php
        } else {
          file_put_contents(BASE_DIR.$projectPath.'counters.json', json_encode($zipFiles));

        }
      }

      // DUMP CONTENTS WHILE TESTING
      file_put_contents('__temp.json', json_encode([
        'initialRequest' => $initialRequest
      , 'projectPath' => $projectPath
      , 'zipName' => $zipName
      , 'zipFiles' => $zipFiles
      , 'projectConfig' => $projectConfig
      , 'counters' => $counters
      ], JSON_PRETTY_PRINT));

    }

    return false;
  }
