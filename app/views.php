<?php

define('VIEWS_EXT', '.twig');

use Webmozart\PathUtil\Path;



$loader = new Twig_Loader_Filesystem('./app/views');
$twig = new Twig_Environment($loader, array(
    'cache' => './app/views/__cache'
,   'auto_reload' => true
));



$twig->addFilter(new Twig_SimpleFilter('asset', function($str) {
    return ROUTES['static'] . "/${str}";
}));


// TODO: hookup parsedown library here
$twig->addFilter(new Twig_SimpleFilter('md', function($str) {
    return $str;
}));


// TODO: hookup parsedown library here
$twig->addFilter(new Twig_SimpleFilter('embed', function($str) {
    $filepath = Path::join(__DIR__, '../static', $str);

    if (file_exists($filepath)) {
        return file_get_contents($filepath);
    }

    return $str;
}));






return $twig;
