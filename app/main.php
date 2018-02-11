<?php

// define base constants for the app
define('ROOT_DIR',          __DIR__ . '/..');
define('APP_DIR',           ROOT_DIR . '/app');
define('VIEWS_DIR',         APP_DIR . '/views');

define('GET_LOGIN',     '/login');
define('POST_LOGIN',    '/auth/login');


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

    $r->addRoute('GET', '/', 'home_page_handler');

    $r->addRoute('GET',  GET_LOGIN, 'login_page_handler');
    $r->addRoute('POST', POST_LOGIN, 'auth_login_handler');

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
 * parses request body content into JSON
 * @param [string]  $type   Data type to be parsed (defaults to json)
 * @return [string|array]   Returns the request body in the desired $type format
 */
function req($type='json') {
    $type = strToLower($type);

    // @sauce: https://stackoverflow.com/a/7084677/3708807
    $body = file_get_contents('php://input');

    $req = '';

    switch ($type) {
        case 'json':
            $req = json_decode($body, true);
            break;

        case 'raw':
        default:
            $req = $body;
            break;
    }

    return $req;
}


/**
 * [now description]
 * @return [type] [description]
 */
function now() {
    return floor(microtime(true));
}


/**
 * Issue a redirect to the login page
 * @return [type] [description]
 */
function redirect($route) {
    header('location:'.$route);
}


/**
 * Middleware: checks if user session data is established and redirects to /login if not
 * @return boolean [description]
 */
function isLoggedIn() {
    // check if user session has been initialized
    if (!isset($_SESSION['user'])) {
        redirect(GET_LOGIN);
    }

    $user = $_SESSION['user'];

    // check if user session is expired
    if ($user['cache'] + minutes(60) < now()) {
        redirect(GET_LOGIN);
    }

    // update user session cache timer
    $user['cache'] = now();

    // return updated user session data
    $_SESSION['user'] = $user;
}



function auth_login_handler() {

    $req = req();
    print_r($req);
    // print_r($_SERVER);

}


function home_page_handler() {
    header('location:'.GET_LOGIN);
}


function login_page_handler() {
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
