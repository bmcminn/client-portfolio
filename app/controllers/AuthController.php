<?php

namespace App;


use \Firebase\JWT\JWT;
use \Interop\Container\ContainerInterface;

class AuthController {


    protected $ci;
    //Constructor
    public function __construct(ContainerInterface $ci) {
        $this->ci = $ci;
    }



    public function login($req, $res, $args) {

        $log    = $this->ci->get('loggerService');
        $db     = $this->ci->get('db');

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
                    'message'   => 'user credentials provided did not match',
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

    }



}
