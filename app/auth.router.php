<?php

$app->get('/auth', function($req, $res) {

    return $res->withJson($data);
})->setName('auth');
