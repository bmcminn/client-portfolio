<?php

namespace App;

use \App\Logger as Log;
use \App\BaseController;
use \Firebase\JWT\JWT;


class AuthController extends BaseController {

    public function login($req, $res, $args) {
        // Log::header('AuthController @ login()');
        Log::debug('// ==================================================');
        Log::debug('login()');

        // $log    = $this->ci->get('loggerService');
        $db     = $this->ci->get('db');

        // get POST body
        $args   = $req->getParsedBody()['params'];

        Log::debug('$args', $args);

        // filter form args
        $email      = trim(filter_var($args['email'],    FILTER_SANITIZE_EMAIL));
        $password   = trim(filter_var($args['password'], FILTER_SANITIZE_STRING));

        $cols = [ 'id', 'password', ];

        $where = [
            'email' => $email,
        ];

        $user = $db->select('users', $cols, $where);

        if (!empty($user)) {
            $user = $user[0];
        }

        Log::debug('AuthContoller@login:', $user);

        // get user model
        // $user = User::getUserProfile($args, $db);

        // $log->debug($user);
        // Log::debug($user);

        // if user doesn't exist
        if (!$user || !password_verify($password, $user['password'])) {
            return $res
                ->withStatus(401)
                ->withJson([
                    'success'   => false,
                    'reason'    => 'login-failed',
                    'message'   => 'user credentials provided did not match',
                ]);
        }

        $user = User::GetUserById($db, $user['id']);


        // Generate auth token
        $token = Auth::generateToken($user);

        // TODO: log auth token for management later
        $db->insert('auth_tokens', ['token' => $token]);

        Log::notice('Login event:', time(), $token);

        // build response payload
        $data = [
            'user'  => $user,
            'token' => $token,
        ];

        // return response
        return $res->withJson($data);

    }



}
