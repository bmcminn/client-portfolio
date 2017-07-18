<?php

define('VIEWS_EXT', '.twig');

$loader = new Twig_Loader_Filesystem('./app/views');
$twig = new Twig_Environment($loader, array(
    'cache' => './views/cache'
,   'auto_reload' => true
));
