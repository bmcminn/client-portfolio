<?php

use App\Auth;
use App\User;


$app->post('/auth/login', function($req, $res) {

    $log    = $this->get('loggerService');
    $db     = $this->get('db');

    // get POST body
    $args = $req->getParsedBody();

    // filter form args
    $args['email']      = filter_var($args['email'],    FILTER_SANITIZE_EMAIL);
    $args['password']   = filter_var($args['password'], FILTER_SANITIZE_SPECIAL_CHARS);

    // $log->debug($args);

    // get user model
    $user = User::getUserProfile($args, $db);

    // $log->debug($user);

    // if user doesn't exist
    if (!$user) {
        return $res
            ->withStatus(401)
            ->withJson([
                'success'   => false,
                'reason'    => 'login-failed',
            ]);
    }

    // Generate auth token
    $token = Auth::generateToken($user);

    // TODO: log auth token for management later

    // build response payload
    $data = [
        'user'  => $user,
        'token' => $token,
    ];

    // return response
    return $res->withJson($data);

})->setName('auth.login');
