<?php

// Create a Router
// --------------------------------------------------

use Webmozart\PathUtil\Path;

$db     = require('db.php');
$router = new \Bramus\Router\Router();


// Custom 404 Handler
$router->set404(function() use ($twig) {
    header('HTTP/1.1 404 Not Found');
    echo '404, route not found!';
});



// Define routes
// --------------------------------------------------

$router->get("{ROUTES['static']}/(.*)", function($path) {

    $path = Path::canonicalize("${ROUTES['static']}/${path}");

    echo $path;
    // return;
    echo file_get_contents($path);
});



// ROUTE: Homepage
$router->get(ROUTES['home'], function() use ($db, $twig, $model) {

    if (!isset($model['page'])) {
        $model['page'] = [];
    }

    // $model['page']['title'] = 'New Client Registration';

    echo $twig->render('homepage.twig', $model);

    return;
});



// Glob all the route files

$routesGlob = Path::join(__DIR__, 'routes/**');

foreach (glob($routesGlob) as $filepath) {
    require($filepath);
}



// Run it!
// --------------------------------------------------

$router->run();
