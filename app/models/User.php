<?php

namespace App;

class User {

    public static function getUserProfile($user, $db) {

        // encode $user password
        // $user['password'] = password_hash();

        $cols =[ 'id', 'first_name', 'last_name', 'email', 'phone', ];

        $where = [
            'email'     => trim(filter_var($user['email'],     FILTER_SANITIZE_EMAIL)),
            'password'  => trim(filter_var($user['password'],  FILTER_SANITIZE_STRING)),
        ];

        return $db->select('users', $cols, $where);
    }

}
