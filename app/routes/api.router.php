<?php

$app->get('/api/status', function ($req, $res) {
    return $res->withJson([
        'status' => 'success',
        'data'  => [
            'serverStatus' => 'up'
        ]
    ]);
})->setName('api.status');



$app->get('/api/user/{id}', function($req, $res, $args) {

    $id = $args['id'] ?? null;

    // TODO: figure out better error handling for missing route params
    if (!$id) {
        return $response
            ->withStatus(500)
            ->withJson([
                'message' => '/api/user/:id requires id route param(s)'
            ]);
    }

    // TODO: add in actual user lookup via User class
    $data = [
        'accountId' => $id,
        'name'      => 'Bob Vila',
    ];

    return $res->withJson($data);

})->setName('api.user.id');
