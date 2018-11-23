<?php

if (IS_DEV) {
    $app->get('/system-init', function($req, $res) {

        $pdo = $this->get('pdo');

        $sqlData = file_get_contents(DATA_DIR . '/sql/init-db.sql');
        $sqlData = explode('---SPLIT---', $sqlData);

        foreach ($sqlData as $si => $sql) {
            $res = $pdo->query($sql);
        }
        // echo password_hash('Testing123', PASSWORD_ARGON2I);
    });
}



// Define named route
$app->get('/[{path:.*}]', function($request, $response, $path = null) {

    $cacheIndex     = CACHE_DIR . '/index.html';
    $viewIndex      = VIEWS_DIR . '/view-index.php';

    // Generate the cached index file
    if (IS_DEV || !is_file($cacheIndex)) {
        exec("php {$viewIndex} > {$cacheIndex}");

        $content = file_get_contents($cacheIndex);

        // "optimize" output HTML
        $content = preg_replace('/\s{2,}/', '', $content);
        $content = preg_replace('/>\s+</', '><', $content);

        // write markup to disk
        file_put_contents($cacheIndex, $content);

        exec('yarn styles');
    }

    return file_get_contents($cacheIndex);
})->setName('page.terms');
