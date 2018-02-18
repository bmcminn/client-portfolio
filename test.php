<?php

require 'vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;


// $salt = bin2hex(random_bytes(32));

// print_r($salt);


$hash = password_hash('rosesaredead', PASSWORD_ARGON2I);

echo $hash . PHP_EOL . PHP_EOL;


// if (password_verify('sfesfaefsd', $hash)) {
//     echo 'Password is valid!';
// } else {
//     echo 'Invalid password.';
// }


// if (password_verify('rosesaredead', $hash)) {
//     echo 'Password is valid!';
// } else {
//     echo 'Invalid password.';
// }


// function getUsers() {

//     $users = glob('users/*.yaml');

//     $userDB = [];

//     print_r($users);

//     foreach ($users as $userpath) {
//         $contents = file_get_contents($userpath);

//         $data = Yaml::parse($contents);

//         array_push($userDB, $data);

//         print_r($data);
//     }

//     print_r($userDB);

// }



// getUsers();
