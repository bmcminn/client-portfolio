<?php

namespace App;


use \Firebase\JWT\JWT;


class Auth {

    protected static $_algo;
    protected static $instance;


    public static function init() {
        if (self::$instance) {
            return;
        }

        self::$_algo = env('JWT_ALGO', 'hs256');
    }


    public static function generateToken($data) {

        $now = time();

        $secret = env('JWT_SECRET');

        $token = [];

        $token['iss'] = env('HOST_DOMAIN');
        $token['aud'] = env('HOST_DOMAIN');
        $token['iat'] = $now;
        $token['nbf'] = $now - minutes(0.5);
        $token['sub'] = $data;

        $jwt = JWT::encode($token, $secret);

        return $jwt;
    }

}


Auth::init();
