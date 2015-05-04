<?php

  // Connect to DB
  $client   = new MongoLite\Client(DB_FOLDER);

  $database   = $client->testdb;
  $collection = $database->products;

  $entry = ["name"=>"Super cool Product", "price"=>20];

  $collection->insert($entry);

  $db = [
    'client'  => $client->appDB
  ];

  // // $db['users']    = $db['client']->users;
  // // $db['projects'] = $db['client']->projects;

  // $entry = [
  //   'username'  => 'bobsburgers101'
  // , 'fname'     => 'Bob'
  // , 'lname'     => 'Belcher'
  // , 'email'     => 'bob@bobsburgers.com'
  // , 'password'  => 'badpassword'
  // ];

  // $db['users'] = $db['client']->users;

  // $db['users']->insert($entry);

  // print_r($db);


  // bootstrap the `users` table if it doesn't exist
  // $db->query(implode([
  //   'CREATE TABLE IF NOT EXISTS users ('
  // ,   'userId INTEGER PRIMARY KEY,'   // primary key the system uses for everything users
  // ,   'type TEXT,'                    // type of user for permissions and such
  // ,   'fname TEXT,'                   // first name of user
  // ,   'lname TEXT,'                   // last name of user
  // ,   'username TEXT,'                // helper for logins
  // ,   'password TEXT,'                // used for logins
  // ,   'email TEXT,'                   // used for communications
  // ,   'phone TEXT,'                   // used for communications
  // ,   'timestamp TEXT'                // used for validating user sessions
  // , ')'
  // ]));
