<?php


function validateUser() {
    if (!isset($_SESSION['user'])) {
        header('Location: ' . ROUTES['login']);
    }
}


function logoutUser() {
    session_destroy();
    header('Location: ' . ROUTES['home']);
}


function message() {
    json_encode([
        'msg' => 'Username/Password combination is not valid'
    ]);
}


function userExists() {

    global $db;

    $contract = [
        'username' => FILTER_SANITIZE_ENCODED
    // ,   'password' => FILTER_SANITIZE_ENCODED
    // ,   'password-confirm' => FILTER_SANITIZE_ENCODED
    ];

    $data = validateData($_POST, $contract);

    $userQuery = "SELECT * FROM users WHERE username=:username";


    try {
        $stmt = $db->prepare($userQuery);
        $stmt->execute($data);

    } catch (PDOExecption $e){
        echo $e->getMessage();
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return;
    }

    return $user;
}


function validateData($data, $contract) {
    $data = filter_var_array($data, $contract);

    $res = [];

    foreach ($data as $key => $value) {
        $res[":${key}"] = $value;
    }

    return $res;
}


function validateUserParams() {



}
