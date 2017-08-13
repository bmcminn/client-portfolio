<?php

define('VIEWS_EXT', '.twig');

$loader = new Twig_Loader_Filesystem('./app/views');
$twig = new Twig_Environment($loader, array(
    'cache' => './app/views/__cache'
,   'auto_reload' => true
));


$filter = new Twig_SimpleFilter('asset', function($str) {
    return "/public/{$str}";
});

$twig->addFilter($filter);


return $twig;
