<?php



$app->post('/auth/login', function($req, $res) {

    $log = $this->get('loggerService');

    $params = $req->getQueryParams();


    $log->debug($params);


    $user = [
        'name'      => 'John Schmidt',
        'id'        => 123456798,
        'email'     => 'jschmidt98@gmail.com',
    ];

    $data = [
        'user'  => $user,
        'token' => 'sefjklsefjslefjsklefjkslefjskljfsel',
    ];


    // TODO add some mechanism for generating auth token

    return $res->withJson($data);

})->setName('auth.login');
