<?php


// Define named route
$app->get('/[{path:.*}]', function($request, $response, $path = null) {
    // return get_template($this->view, '/index.twig');
    $cacheIndex     = CACHE_DIR . '/index.html';
    $viewIndex      = VIEWS_DIR . '/view-index.php';

    // Generate the cached index file
    if (IS_DEV || !is_file($cacheIndex)) {
        exec("php {$viewIndex} > {$cacheIndex}");

        // if prod, optimize the cached file
        if (IS_PROD) {
        }

        $content = file_get_contents($cacheIndex);

        $content = preg_replace('/\s{2,}/', '', $content);
        $content = preg_replace('/>\s+</', '><', $content);

        file_put_contents($cacheIndex, $content);

    }


    return file_get_contents($cacheIndex);
})->setName('page.terms');


// $app->get('/privacy-policy', function($req, $res) {
//     return get_template($this->view, '/pages/privacy.twig');
// })->setName('page.privacy');

