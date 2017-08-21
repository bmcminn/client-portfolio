<?php

$db = require('db.php');


class Q {

    private function __construct() {}

    public static function TABLE_EXISTS($tableName) {
        return "SELECT name FROM sqlite_master WHERE type='table' AND name='{$tableName}';";
    }


    public static function CREATE_TABLE($tableName, $tableProps) {
        return "CREATE TABLE IF NOT EXISTS {$tableName} ("
            .join(',', $tableProps)
            .');'
            ;
    }

}



$db->exec(Q::CREATE_TABLE('users', [
    'user_id        INTEGER PRIMARY KEY'
,   'user_email     TEXT NOT NULL'
,   'user_fullname  TEXT NOT NULL'
,   'user_password  TEXT NOT NULL'
,   'user_type      TEXT NOT NULL'
]));


$db->exec(Q::CREATE_TABLE('projects', [
    'project_id        INTEGER PRIMARY KEY'
,   'project_config    TEXT NOT NULL'
,   'project_proofs    TEXT NOT NULL'
,   'project_previews  TEXT NOT NULL'
]));


$db->exec(Q::CREATE_TABLE('password_resets', [
    'reset_id       INTEGER PRIMARY KEY'    // AUTO ID
,   'reset_token    TEXT NOT NULL'          // TOKEN CREATED WHEN PASSWORD RESET WAS REQUESTED
,   'user_id        INTEGER NOT NULL'       // USER ID FOR PASSWORD RESET
,   'reset_expires  INTEGER NOT NULL'       // UNIX EPOCH TIMESTAMP
,   'reset_expired  INTEGER'                // BOOLEAN FLAG
]));



// DETERMINE IF WE HAVE ADMIN USERS OR NOT
$admins = $db->query("SELECT * FROM users WHERE user_type='admin'");

if (!$admins->fetch(\PDO::FETCH_ASSOC) && $_SERVER['REQUEST_URI'] !== ROUTES['register_admin']) {
    redirect(ROUTES['register_admin']);
}

