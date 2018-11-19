<?php

use App\AuthController;


$app->group('/auth', function() {

    $this->post('/login',       AuthController::class   . ':login'      )->setName('auth.login');


});
