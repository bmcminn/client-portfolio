<?php

// Create a Router
// --------------------------------------------------

use Webmozart\PathUtil\Path;


$router = new \Bramus\Router\Router();


// Custom 404 Handler
$router->set404(function() use ($twig) {
    header('HTTP/1.1 404 Not Found');
    echo '404, route not found!';
});



// Define routes
// --------------------------------------------------

// ROUTE: Homepage
$router->get(ROUTES['home'], function() use ($twig) {

    validateUser();

    echo "homepage";

    return;
});


// ROUTE: register
$router->get(ROUTES['client-register'], function() use ($model, $twig) {

    $db = require Path::join(APP_DIR, 'db.php');

    $stmt = $db->prepare('
        SELECT *
        FROM clients
        ORDER BY name
    ');

    $stmt->execute();

    $clients = $stmt->fetchAll();

    $model['inputs'] = [
        [
            'label' => 'Email'
        ,   'name'  => 'username'
        ,   'type'  => 'email'
        ,   'required' => true
        ]
    ,   [
            'label' => 'Password'
        ,   'name'  => 'password'
        ,   'type'  => 'password'
        ,   'required' => true
        ]
    ,   [
            'label' => 'Confirm Password'
        ,   'name'  => 'password-confirm'
        ,   'type'  => 'password'
        ,   'required' => true
        ]
    ,   [
            'label' => 'Client'
        ,   'name'  => 'client'
        ,   'type'  => 'select'
        ,   'opts'  => $clients
        ,   'required' => true
        ]
    ];

    $stmt   = null;
    $db     = null;

    echo $twig->render('register.twig', $model);

});



// ROUTE: register
$router->post(ROUTES['register'], function() use ($model) {

    // // check if user doesn't exist
    // if (userExists()) {
    //     // if user exists, return 'bleh'
    //     return;
    // }

    $db = require Path::join(APP_DIR, 'db.php');

    $stmt = $db->prepare('
        SELECT * FROM users WHERE username=:username AND is_deleted=false
    ');

    $res = $stmt->execute([ ':username' => $_POST['username']]);

    $stmt   = null;

    if ($res) {
        echo message("user already exists", 500);
        return;
    }


    // move on to putting the user in the DB
    $user = [
        ':username'         => filter_var(trim($_POST['username']), FILTER_VALIDATE_STRING)
    ,   ':password'         => filter_var(trim($_POST['password']), FILTER_VALIDATE_STRING)
    ,   ':password-confirm' => filter_var(trim($_POST['password-confirm']), FILTER_VALIDATE_STRING)
    ];


    $stmt = $db->prepare('
        INSERT INTO users (username, password, client)
            VALUES (:username, :password, :client)
            ;
        ');

    $stmt->execute($user);

    $







    // close DB connection
    $db = null;

    return;



    // hash user password
    // $hash   = password_hash($pass, PASSWORD_BCRYPT);
    // $res    = password_verify($pass, $hash);

    // registerUser($user);

    //

});


// ROUTE: login
$router->get(ROUTES['login'], function() use ($twig, $model) {

    $pass   = "rasmuslerdorf";
    $hash   = password_hash($pass, PASSWORD_BCRYPT);
    $res    = password_verify($pass, $hash);

    echo $res;
    return;

    $model = [];

    echo $twig->render('login.twig', $model);
});


$router->post(ROUTES['login'], function() use ($model) {

    print_r($_POST);

    // look up user in users collection



    // get users hash

    // verify password against use rhash

    // if verified -> navigate to homepage and list all projects

    // else -> redirect to login page with error messsage
});


// ROUTE: logout
$router->get(ROUTES['logout'], function() use ($twig) {
    validateUser();
    logoutUser();
    return;
});






// //
// $router->get('[a-z0-9_-]+', function($clientRoute) {

//     echo $clientRoute;
//     return;

// });



// Run it!
// --------------------------------------------------

$router->run();
