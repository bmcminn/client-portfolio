<?php


// TODO: register routes for testing email templates when env is not prod
//  - /test
//      - /emails : list of email templates available to view
//      - /emails/(template-name)

$router->get('/test/emails', function() use ($twig, $model) {

    $files = glob('app/views/emails/*.twig');

    $templates = [];

    foreach ($files as $file) {
        $filename = basename($file);
        $filename = preg_replace('/\..+$/', '', $filename);

        $templates[] = [
            'route' => "/test/emails/${filename}"
        ,   'title' => preg_replace('/[\-]/i', ' ', $filename)
        ];
    }

    $model['templates'] = $templates;

    echo $twig->render('test/emails.twig', $model);
});




$router->get('/test/emails/(.*)', function($template) use ($twig, $model) {

    echo $twig->render("emails/${template}.twig", $model);

});

