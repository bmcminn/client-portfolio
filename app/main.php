<?php

// PREVENT STATIC FILES FROM GOING BEING BLOCKED
// --------------------------------------------------

define('REQUEST_URI',   preg_replace('/\?*+/',  '', filter_var($_SERVER['REQUEST_URI'], FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE)));

if (preg_match('/\.(?:png|jpg|jpeg|gif|js|css|less|zip)$/', REQUEST_URI)) {
    return false;
}


// SET TIME/DATA PARAMETERS
// --------------------------------------------------

date_default_timezone_set('America/Chicago');

define('SECONDS',   1000);
define('MINUTES',   SECONDS * 60);
define('HOURS',     MINUTES * 60);
define('DAYS',      HOURS * 24);



// REQUIRE COMPOSER AUTOLOADER
// --------------------------------------------------

require __DIR__ . '/../vendor/autoload.php';



// DEFINE APP INSTANCES
// --------------------------------------------------

use Webmozart\PathUtil\Path;




// SETUP LOCAL FILEPATHS
define('DIR',    DIRECTORY_SEPARATOR);

define('ROOT_DIR',      Path::canonicalize(__DIR__.'/..'));
define('PROJECT_DIR',   Path::join(ROOT_DIR, '_projects'));
define('APP_DIR',       Path::join(ROOT_DIR, '/app'));
define('VIEWS_DIR',     Path::join(APP_DIR, '/views'));
define('DATA_DIR',      Path::join(APP_DIR, '/__data'));



// INIT THE USER SESSION
// --------------------------------------------------

define('SESSION_TIMEOUT', 60 * MINUTES);

session_save_path(realpath(ROOT_DIR.DIR.'.tmp'));
session_start();



// Prevent PHP garbage collection from deleting our session
// // TODO: what is this for?!
if (!isset($_SESSION['gc_last_access']) || (time() - $_SESSION['gc_last_access']) > SESSION_TIMEOUT) {
    $_SESSION['gc_last_access'] = time();
}



// Define routes list
// --------------------------------------------------
define('ROUTES', [
    'home'      => '/'
,   'login'     => '/login'
,   'logout'    => '/logout'
,   'register'  => '/register'
]);



// LOAD METHOD HELPERS
// --------------------------------------------------

require Path::join(APP_DIR, 'methods.php');


// SETUP DB CONNECTION
// --------------------------------------------------

require Path::join(APP_DIR, 'db.php');


// Create Twig instance
// --------------------------------------------------

require Path::join(APP_DIR, 'views.php');


// Define base model
// --------------------------------------------------

$model = [
    'routes' => ROUTES
];




$_POST['username'] = 'bob';
$_POST['password'] = 'Testing!';

userExists();

return;





// INIT ROUTER INSTANCE
// --------------------------------------------------

require Path::join(APP_DIR, 'routes.php');
