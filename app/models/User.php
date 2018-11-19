<?php

namespace App;

class User {

    public static function GetUserById($db, int $id) {

        $cols   = [ 'id', 'first_name', 'last_name', 'email', 'phone', ];

        $where  = [ 'id' => $id ];

        return $db->select('users', $cols, $where);

    }


    public static function GetUserProfile($user, $db) {

        // clean up user data
        $user['email']      = trim(filter_var($user['email'],     FILTER_SANITIZE_EMAIL));
        $user['password']   = trim(filter_var($user['password'],  FILTER_SANITIZE_STRING));


        $user['password'] = password_hash($user['password'], PASSWORD_ARGON2I);

        $cols = [ 'id', 'first_name', 'last_name', 'email', 'phone', ];

        $where = $user;

        return $db->select('users', $cols, $where);
    }

}
