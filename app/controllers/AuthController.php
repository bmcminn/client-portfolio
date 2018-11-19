<?php

namespace App;


use \App\BaseController;
use \Firebase\JWT\JWT;


class AuthController extends BaseController {






    public function login($req, $res, $args) {

        $log    = $this->ci->get('loggerService');
        $db     = $this->ci->get('db');



        // get POST body
        $args = $req->getParsedBody()['params'];


        // filter form args
        $email      = trim(filter_var($args['email'],    FILTER_SANITIZE_EMAIL));
        $password   = trim(filter_var($args['password'], FILTER_SANITIZE_STRING));

        $password   = password_hash($password, PASSWORD_ARGON2I);

        $cols = [ 'id', 'first_name', 'last_name', 'email', 'phone', 'password', ];

        $where = [
            'email'     => $email,
            // 'password'  => $password,
        ];

        $user = $db->select('users', $cols, $where);

        // get user model
        // $user = User::getUserProfile($args, $db);

        // $log->debug($user);

        // if user doesn't exist
        if (!$user || !password_verify($password, $user->password)) {
            return $res
                ->withStatus(401)
                ->withJson([
                    'success'   => false,
                    'reason'    => 'login-failed',
                    'message'   => 'user credentials provided did not match',
                ]);
        }


        $user = User::GetUserById($db, $user->id);


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
