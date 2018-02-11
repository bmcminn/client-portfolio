<?php

// define base constants for the app
define('ROOT_DIR',          __DIR__ . '/..');
define('APP_DIR',           ROOT_DIR . '/app');
define('VIEWS_DIR',         APP_DIR . '/views');

define('ROUTE_LOGIN',       '/login');
define('ROUTE_LOGIN_USER',  '/auth/login');


// get the party started
require '../vendor/autoload.php';


// register env configs
$dotenv = new Dotenv\Dotenv(ROOT_DIR);
$dotenv->load();


// register error helper when in dev
if (getenv('APP_DEBUG')) {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}


// init the user session
session_start();


// define our route dispatcher
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {

    $r->addRoute('GET', '/', 'login_redirect');

    $r->addRoute('GET',  ROUTE_LOGIN, 'login_page');
    $r->addRoute('POST', ROUTE_LOGIN_USER, 'login_handler');

    $r->addRoute('GET', '/users', 'get_all_users_handler');

    // {id} must be a number (\d+)
    $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');

    // The /{title} suffix is optional
    $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
});


// define our render method
function render($templateName='default', $model = []) {

}


/**
 * Takes num minutes and turns it into seconds
 * @param  [int]    $min    Number of minutes to be converted
 * @return [int]            Number of minutes in seconds
 */
function minutes($min) {
    return 60 * $min;
}


/**
 * [now description]
 * @return [type] [description]
 */
function now() {
    return floor(microtime(true));
}


function loginRedirect() {
    header('location:'.ROUTE_LOGIN);
}


/**
 * Middleware: checks if user session data is established and redirects to /login if not
 * @return boolean [description]
 */
function isLoggedIn() {
    // check if user session has been initialized
    if (!isset($_SESSION['user'])) {
        loginRedirect();
    }

    $user = $_SESSION['user'];

    // check if user session is expired
    if ($user['cache'] + minutes(60) < now()) {
        loginRedirect();
    }

    // update user session cache timer
    $user['cache'] = now();

    // return updated user session data
    $_SESSION['user'] = $user;
}



function login_handler() {

    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, TRUE);

    print_r($input);
    // print_r($_SERVER);

}


function login_redirect() {
    header('location:'.ROUTE_LOGIN);
}


function login_page() {
    require(VIEWS_DIR . '/login.twig');
}


function get_all_users_handler() {
    $user = isLoggedIn();
}


function error_handler($errCode) {
    // TODO: setup logger to capture error information
    require(VIEWS_DIR . "/${errCode}.twig");
}




// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri        = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== ($pos = strpos($uri, '?'))) {
    $uri = substr($uri, 0, $pos);
}

$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        error_handler(404);
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        error_handler(405);
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        // ... call $handler with $vars

        $handler($vars);

        break;
}
