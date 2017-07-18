<?php

// Create a Router
// --------------------------------------------------

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

    return;
});



// ROUTE: register
$router->post(ROUTES['register'], function() use ($model) {

    // check if user doesn't exist
    if (userExists()) {
        // if user exists, return 'bleh'
        return;
    }


    $data = validateData($data, $contract);


    // hash user password
    $pass   = $_POST['password'];
    $hash   = password_hash($pass, PASSWORD_BCRYPT);
    $res    = password_verify($pass, $hash);


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






//
$router->get('[a-z0-9_-]+', function($clientRoute) {

    echo $client;
    return;

});



// Run it!
// --------------------------------------------------

$router->run();
