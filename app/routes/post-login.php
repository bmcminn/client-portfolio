<?php

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

    // init user timeout cache
    $user['cache'] = now();

    // cache the $user data in a session
    $_SESSION['user'] = $user;

    // if password is not correct
    if (!password_verify($req['userpassword'], $_SESSION['user']['hash'])) {
        $res['success'] = false;
        $res['message'] = 'Password failed.';

        status_code(400);
        res_json($res);
        return;
    }

    $res['success'] = true;

    res_json($res);

    Debug(json_encode($res));
    // print_r($_SERVER);
};
