<?php

use \App\ApiController;


$app->group('/api', function() {

    $this->get('/status',       ApiController::class    . ':status'     )->setName('api.status');
    $this->get('/user/{id}',    UserController::class   . ':getUserById')->setName('api.user.id');

});
