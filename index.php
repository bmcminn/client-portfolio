<?php

// handle serving binary assets in local dev
if (php_sapi_name() === 'cli-server') {
    if (preg_match('/\.(?:js|css|ico|webp|png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
        return false;    // serve the requested resource as-is.
    }
}


// get the party started
require __DIR__ . '/vendor/autoload.php';


// init .env
$dotenv = new Dotenv\Dotenv(__DIR__ . '/environments');
$dotenv->load();


// init global root variables
define('ROOT_DIR',  __DIR__);
define('DS',        DIRECTORY_SEPARATOR);


// init global directory maps
define('APP_DIR',       __DIR__ . '/app');
define('CACHE_DIR',     __DIR__ . '/.cache');
define('LOGS_DIR',      __DIR__ . '/.cache/logs');
define('DATA_DIR',      __DIR__ . '/data');
define('PROJECTS_DIR',  __DIR__ . '/data/projects');
define('VIEWS_DIR',     __DIR__ . '/views');


// load provision script(s)
require __DIR__ . '/app/provision.php';


// load helper methods
require "./app/helpers.php";


// init ENV boolean flags
define('IS_PRODUCTION', (bool)env('ENV') === 'production');
define('IS_STAGING',    (bool)env('ENV') === 'staging');
define('IS_DEV',        (bool)(!IS_STAGING && !IS_PRODUCTION));


// Define App instance config
$appConfig = require './app/config.php';


// Create Slim app
$app = new \Slim\App(['settings' => $appConfig]);


// Fetch DI Container
$container = $app->getContainer();


// Hookup DB instance
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO($db['type'] . ':' . $db['path']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};


// Setup Logger service
$container['loggerService'] = function ($c) {
    return new \Gbox\Minilog('logName', [
        'dir' => LOGS_DIR,
    ]);
};


// // Register Twig View helper
// $container['view'] = function ($c) {
//     $view = new \Slim\Views\Twig(VIEWS_DIR, [
//         'cache' => IS_PRODUCTION ? CACHE_DIR : false,
//     ]);

//     // Instantiate and add Slim specific extension
//     $router = $c->get('router');
//     $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
//     $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

//     $view->addExtension(new Twig_Extension_StringLoader());

//     // Register global view model data
//     $globals = require  DATA_DIR . '/view-model.php';

//     foreach ($globals as $key => $value) {
//         $view->getEnvironment()->addGlobal($key, $value);
//     }

//     $view->getEnvironment()->registerUndefinedFunctionCallback(function ($name) {
//         if (function_exists($name)) {
//             return new Twig_Function($name, $name);
//         }
//         return false;
//     });

//     $view->getEnvironment()->registerUndefinedFilterCallback(function ($name) {
//         if (function_exists($name)) {
//             return new Twig_Filter($name, $name);
//         }
//         return false;
//     });

//     return $view;
// };



// load API routes
require APP_DIR . '/auth.router.php';
require APP_DIR . '/api.router.php';

// load server side rendered routes
require APP_DIR . '/client.router.php';



// Run app
$app->run();
