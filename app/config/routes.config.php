<?php

if (!defined('ROUTES')) {

    define('ROUTES', [
        'home'                  => '/'
    ,   'static'                => '/static'
    ,   'login'                 => '/user/login'
    ,   'logout'                => '/user/logout'
    ,   'forgot_password'       => '/user/forgot-password'
    ,   'reset_password'        => '/user/reset-password'
    ,   'register_client'       => '/register/client'
    ,   'register_admin'        => '/register/admin'
    ,   'user_dashboard'        => '/user/dashboard'
    ,   'admin_dashboard'       => '/admin/dashboard'
    ]);

}
