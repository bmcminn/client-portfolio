<?php

// define base constants for the app
define('EOL',           PHP_EOL);


// define local app folder constants
define('ROOT_DIR',      realpath(__DIR__ . '/..'));
define('APP_DIR',       ROOT_DIR . '/app');
define('VIEWS_DIR',     APP_DIR . '/views');
define('CACHE_DIR',     APP_DIR . '/cache');
define('LOGS_DIR',      APP_DIR . '/logs');
define('USERS_DIR',     ROOT_DIR . '/users');
define('CLIENTS_DIR',   ROOT_DIR . '/clients');


// define named route constants used throughout the system
define('ROUTE_GET_HOMEPAGE',            '/');

define('ROUTE_GET_LOGIN',               '/login');
define('ROUTE_POST_LOGIN',              '/auth/login');

define('ROUTE_GET_LOGOUT',              '/logout');
define('ROUTE_POST_LOGOUT',             '/auth/logout');

define('ROUTE_GET_RESET_PASSWORD',      '/reset/password');
define('ROUTE_POST_RESET_PASSWORD',     '/reset/password');

define('ROUTE_GET_DASHBOARD',           '/dashboard');


// init the user session
session_start();


// get the party started
require '../vendor/autoload.php';


// register env configs
$dotenv = new Dotenv\Dotenv(ROOT_DIR);
$dotenv->load();


define('APP_DEBUG', getenv('APP_DEBUG') === 'true');


// register error helper when in dev
if (APP_DEBUG) {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}


// set the default timezone for our app
$TIMEZONE = getenv('APP_TIMEZONE') ? getenv('APP_TIMEZONE') : 'America/Chicago';

date_default_timezone_get($TIMEZONE);


// Load helper functions
require('helpers.php');


// register a few env globals
define('PW_RESET_CACHE_EXPIRATION', hours(1));


// Ensure app directories exist
provisionDirs();


// define our route dispatcher
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {

    // Homepage route
    $r->addRoute('GET',     ROUTE_GET_HOMEPAGE,         require('routes/get-homepage.php'));

    // User login routes
    $r->addRoute('GET',     ROUTE_GET_LOGIN,            require('routes/get-login.php'));
    $r->addRoute('POST',    ROUTE_POST_LOGIN,           require('routes/post-login.php'));
    $r->addRoute('GET',     ROUTE_GET_LOGOUT,           require('routes/get-logout.php'));
    // $r->addRoute('POST',    ROUTE_POST_LOGOUT,          require('routes/post-logout.php'));

    // Reset password routes
    $r->addRoute('GET',     ROUTE_GET_RESET_PASSWORD . '[/{hash}]', require('routes/get-reset-password.php'));
    $r->addRoute('POST',    ROUTE_POST_RESET_PASSWORD,              require('routes/post-reset-password.php'));

    $r->addRoute('GET',     ROUTE_GET_DASHBOARD,                    require('routes/get-dashboard.php'));

});


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
        error_page_handler(404);
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        error_page_handler(405);
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        $handler($vars);

        break;
}
