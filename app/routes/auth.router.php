<?php

use App\Auth;
use App\AuthController;
use App\User;


$app->group('/auth', function() {

    $this->post('/login', \App\AuthController::class . ':login')->setName('auth.login');


});
