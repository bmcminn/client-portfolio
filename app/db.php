<?php

if (!defined('DB_PATH')) {
    define('DB_PATH', DATA_DIR.'/__db.sqlite');
}


try {
    $db = new PDO('sqlite:'.DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $db;

} catch(PDOException $e) {
    return $e->getMessage();

}
