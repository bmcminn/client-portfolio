<?php


$app->get('/api/status', function ($req, $res) {
    return $res->withJson([
        'status' => 'success',
        'data'  => [
            'serverStatus' => 'up'
        ]
    ]);
})->setName('api.status');



$app->post('/api/order/submit', function($req, $res) {

    $data = [
        'pants' => true
    ];

    return $res->withJson($data);
})->setName('api.order.submit');

