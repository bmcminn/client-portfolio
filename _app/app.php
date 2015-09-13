<?php

  // configure whoops! instance
  $whoops = new \Whoops\Run;
  $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
  $whoops->register();


  // ---------------------------------------------------------------------------


  // load helper functions
  require APP_DIR.DS."funcs.php";

  // load config/model definitions
  require APP_DIR.DS."model.php";

  // load template engine instance
  require APP_DIR.DS."twig.php";
