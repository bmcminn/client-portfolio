<?php

// define base constants for the app
define('ROOT_DIR',          __DIR__ . '/..');
define('VIEWS_DIR',         ROOT_DIR . '/views');

define('ROUTE_LOGIN',       '/login');
define('ROUTE_LOGIN_USER',  '/login/user');


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

    $r->addRoute('GET', ROUTE_LOGIN, 'login_page');
    $r->addRoute('GET', ROUTE_LOGIN_USER, 'login_handler');

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
 * Middleware: checks if user session data is established and redirects to /login if not
 * @return boolean [description]
 */
function isLoggedIn() {
    if (!isset($_SESSION['user'])) {
        header('location:'.ROUTE_LOGIN);
    }

    return $_SESSION(['user']);
}


function login_page() {
    echo file_get_contents(VIEWS_DIR.'/login.twig');
}


function get_all_users_handler() {
    $user = isLoggedIn();
}






// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        // ... call $handler with $vars

        $handler($vars);

        break;
}
