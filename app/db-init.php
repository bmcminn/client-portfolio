<?php

$db = require('db.php');


class Q {

    private function __construct() {}

    public static function TABLE_EXISTS($tableName) {
        return "SELECT name FROM sqlite_master WHERE type='table' AND name='{$tableName}'";
    }

}


$db->exec(Q::TABLE_EXISTS('users'));


