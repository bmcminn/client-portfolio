<?php

namespace App;


use Rakit\Validation\Validator;


class User {

    protected $ci;
    // protected $


    function __construct($ci) {
        $this->ci = $ci;
    }


    public static function GetUserById($db, int $id) {

        $cols   = [ 'id', 'first_name', 'last_name', 'email', 'phone', ];

        $where  = [ 'id' => $id ];

        $user = $db->select('users', $cols, $where);

        if (empty($user)) {
            return false;
        }

        return $user[0];

    }


    /**
     * [GetUserData description]
     * @param [type] $user [description]
     * @param [type] $db   [description]
     */
    public static function GetUserData($login) {

        $db = $this->ci['db'];


        // validate the input is correct
        $validation = $validator->validate($login, [
            'email'     => 'required|email',
            'password'  => 'required|min:6',
        ]);

        if ($validation->fails()) {
            return $validation->errors();
        }

        // clean up user data
        $login['email']      = trim(filter_var($login['email'],     FILTER_SANITIZE_EMAIL));
        $login['password']   = trim(filter_var($login['password'],  FILTER_SANITIZE_STRING));

        $cols = [ 'id', 'first_name', 'last_name', 'email', 'phone'];

        $where = [
            'email' => $login['email']
        ];

        $user = $db->select('users', $cols, $where);


    }





}


