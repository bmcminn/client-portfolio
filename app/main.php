<?php

// define base constants for the app
define('ROOT_DIR',      realpath(__DIR__ . '/..'));
define('APP_DIR',       ROOT_DIR . '/app');
define('VIEWS_DIR',     APP_DIR . '/views');


// define named route constants used throughout the system
define('ROUTE_GET_HOMEPAGE',            '/');

define('ROUTE_GET_LOGIN',               '/login');
define('ROUTE_POST_LOGIN',              '/auth/login');

define('ROUTE_GET_LOGOUT',              '/logout');
define('ROUTE_POST_LOGOUT',             '/auth/logout');

define('ROUTE_GET_RESET_PASSWORD',      '/reset/password');
define('ROUTE_POST_RESET_PASSWORD',     '/reset/password');

define('ROUTE_GET_DASHBOARD',           '/dashboard');


// get the party started
require '../vendor/autoload.php';


// register env configs
$dotenv = new Dotenv\Dotenv(ROOT_DIR);
$dotenv->load();


// set the default timezone for our app
$TIMEZONE = getenv('APP_TIMEZONE') ? getenv('APP_TIMEZONE') : 'America/Chicago';

date_default_timezone_get($TIMEZONE);


// register error helper when in dev
if (getenv('APP_DEBUG')) {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}


// init the user session
session_start();


require('helpers.php');


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
    $r->addRoute('GET',     ROUTE_GET_RESET_PASSWORD,   require('routes/get-reset-password.php'));
    $r->addRoute('POST',    ROUTE_POST_RESET_PASSWORD,  require('routes/post-reset-password.php'));

    $r->addRoute('GET',     ROUTE_GET_DASHBOARD,        require('routes/get-dashboard.php'));

    // // {id} must be a number (\d+)
    // $r->addRoute('GET',     '/user/{id:\d+}', 'get_user');

    // // The /{title} suffix is optional
    // $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article');
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
