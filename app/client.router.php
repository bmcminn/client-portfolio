<?php


// Define named route
$app->get('/[{path:.*}]', function($request, $response, $path = null) {
    // return get_template($this->view, '/index.twig');
    return file_get_contents(VIEWS_DIR . '/index.html');
})->setName('page.terms');


// $app->get('/privacy-policy', function($req, $res) {
//     return get_template($this->view, '/pages/privacy.twig');
// })->setName('page.privacy');

