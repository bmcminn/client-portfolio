<?php

  // Connect to DB
  $db = new MongoLite\Client(DB_FOLDER);
  $db = $db->appDB;
