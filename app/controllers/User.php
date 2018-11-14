<?php

namespace App;

class User {


    public static function getUserProfile($user, $db) {

        // encode $user password
        // $user['password'] = password_hash();

        $cols =[ 'id', 'first_name', 'last_name', 'email', 'phone', ];

        $where = [
            'email'     => trim($user['email']),
            'password'  => trim($user['password']),
        ];

        return $db->select('users', $cols, $where);

    }

}
