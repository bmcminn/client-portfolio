<?php

  // Connect to DB
  $db = new MongoLite\Client(DB_FOLDER);
  $db = $db->appDB;


  // init application database if it doesn't have user already
  initAdmin();
