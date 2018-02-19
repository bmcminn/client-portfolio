<?php

use Symfony\Component\Yaml\Yaml;


/**
 * [postLoginHandler description]
 * @return [type] [description]
 */
return function() {
    $req = req();

    $res = [];

    $req['useremail']       = filter_var($req['useremail'], FILTER_SANITIZE_STRING);
    $req['userpassword']    = filter_var($req['userpassword'], FILTER_SANITIZE_STRING);

    $user = getUser($req['useremail']);

    // if the user could not be found by email
    if (!$user) {
        $res['success'] = false;
        $res['message'] = 'Could not find an account with the email or password provided.';

        status_code(404);
        res_json($res);
        return;
    }

    Debug(json_encode('------------------'));
    Debug(json_encode($req));
    Debug(json_encode($user));

    if (!password_verify($req['userpassword'], $_SESSION['user']['hash'])) {
        $res['success'] = false;
        $res['message'] = 'Password failed.';

        status_code(404);
        res_json($res);
        return;
    }

    $res['success'] = true;

    res_json($res);

    Debug(json_encode($res));
    // print_r($_SERVER);
};



function getUser($email) {

    $email = strToLower(trim($email));

    $usersCache = CACHE_DIR . '/users.json';

    if (!is_file($usersCache)) {
        cacheUsers();
    }

    $users = file_get_contents($usersCache);

    $users = json_decode($users, true);

    $gotUser = false;

    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $_SESSION[SESSION_USER] = $user;
            $gotUser = true;
            break;
        }
    }

    return $gotUser;
}



function cacheUsers() {

    $users = glob(USERS_DIR . '/*.yaml');

    $userDB = [];

    foreach ($users as $userpath) {
        $contents = file_get_contents($userpath);

        $data = Yaml::parse($contents);

        array_push($userDB, $data);
    }


    Debug(json_encode($userDB, true));

    $userCache = CACHE_DIR . '/users.json';

    file_put_contents($userCache, json_encode($userDB, true));

}
