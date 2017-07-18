<?php


define('DB_PATH', DATA_DIR.'/db.sqlite');


try {

    $db = new PDO('sqlite:'.DB_PATH);

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



} catch(PDOException $e) {
    echo $e->getMessage();
}
