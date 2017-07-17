<?php

// serve the requested resource as-is.
define('DIR',    DIRECTORY_SEPARATOR);

define('ROOT_DIR',      __DIR__);
define('APP_DIR',       ROOT_DIR.DIR.'app'.DIR);
define('VIEWS_DIR',     APP_DIR.DIR.'views');
define('PROJECT_DIR',   ROOT_DIR.DIR.'_projects');
// define('DB_FOLDER',     ROOT_DIR.DIR.'_data');
define('VIEWS_EXT',     '.twig');

define('SECONDS',   1000);
define('MINUTES',   SECONDS * 60);
define('HOURS',     MINUTES * 60);
define('DAYS',      HOURS * 24);



// PREVENT STATIC FILES FROM GOING BEING BLOCKED
// --------------------------------------------------

define('REQUEST_URI',   preg_replace('/\?*+/',  '', filter_var($_SERVER['REQUEST_URI'], FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE)));

if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css|less|zip)$/', REQUEST_URI)) {
    return false;
}


// Set default timezone
// --------------------------------------------------

date_default_timezone_set('America/Chicago');



// INIT THE USER SESSION
// --------------------------------------------------

define('SESSION_TIMEOUT', 10 * MINUTES);

session_save_path(realpath(ROOT_DIR.DIR.'..'.DIR.'.tmp'));
session_start();


// Prevent PHP garbage collection from deleting our session
if(!isset($_SESSION['gc_last_access']) || (time() - $_SESSION['gc_last_access']) > 60) {
    $_SESSION['gc_last_access'] = time();
}





// Define routes list
// --------------------------------------------------
define('ROUTES', [
    'home'      => '/'
,   'login'     => '/login'
,   'logout'    => '/logout'
]);



// Require composer autoloader
// --------------------------------------------------

require __DIR__ . '/../vendor/autoload.php';
require './app/methods.php';



// Create Twig instance
// --------------------------------------------------

$loader = new Twig_Loader_Filesystem('./app/views');
$twig = new Twig_Environment($loader, array(
    'cache' => './views/cache'
,   'auto_reload' => true
));



// Define base model
// --------------------------------------------------

$model = [
    'routes' => ROUTES
];

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


// ROUTE: login
$router->get(ROUTES['login'], function() use ($twig, $model) {

    $pass = "rasmuslerdorf";
    $hash = password_hash($pass, PASSWORD_BCRYPT);

    $res = password_verify($pass, $hash);

    echo $res;
    return;

    $model = [];

    echo $twig->render('login.twig', $model);
});


$router->post(ROUTES['login]', function() use ($model) {

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




