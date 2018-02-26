<?php

return function($req=[]) {

    $req = req($req);

    $LOG_LABEL = '[PASSWORD RESET EVENT]';

    $res = [ 'success' => true ];

    print_r($req);

    // allow user to reset password
    if (isset($req['hash'])) {
        return processPasswordReset($req);
    }

    if (!isset($req['hash'])) {
        return requestPasswordReset($req);
    }

    // if (isset($req[]))
    // $req['useremail'] = filter_var($req['useremail'], FILTER_SANITIZE_STRING);

    // if (isset($req['userpassword'])) {
    //     $req['userpassword'] = filter_var($req['userpassword'], FILTER_SANITIZE_STRING);
    // }


    // $user = getUser($req['useremail']);

    // // if user does not exist in the system
    // if (!$user) {
    //     // return a success to the user, log the event, but don't do anything else
    //     res_json($res);
    //     Debug("${LOG_LABEL} [FAILED] User does not exist: " . json_encode($req));
    //     return;
    // }

    // // log event to system
    // Debug("${LOG_LABEL} [SUCCESS] User exists: " . json_encode($req));

    // // generate a reset hash
    // $resetHash = sha1(now() . 'passwordResetEvent' . getenv('SALT_PASSWORD_RESET'));

    // Debug("${LOG_LABEL} RESET HASH : ${resetHash}");

    // // set the expire timestamp to 24 hours from now
    // $req['expires'] = (now() + (hours(24)));

    // // create the hash file
    // file_put_contents(CACHE_DIR . '/' . $resetHash, json_encode($req));

    // // write a success message to the user
    // $res['message'] = 'An email with a reset link will be sent shortly.';

    // res_json($res);
};



function processPasswordReset($req) {

    $LOG_LABEL = '[PASSWORD RESET EVENT]';

    if (isset($req['userpassword'])) {
        $req['userpassword'] = filter_var($req['userpassword'], FILTER_SANITIZE_STRING);
    }

    if (isset($req['userpasswordconfirm'])) {
        $req['userpasswordconfirm'] = filter_var($req['userpasswordconfirm'], FILTER_SANITIZE_STRING);
    }

    // check the passwords match
    if ($req['userpassword'] !== $req['userpasswordconfirm']) {
        post_error(400, 'Passwords do not match.');
        return;
    }

    $hashfile = CACHE_DIR . '/' . $req['hash'];

    // check if hash file exists
    if (!file_exists($hashfile)) {
        return;
    }

    // reset password and update user DB entry
    Info('resetting password for user ', $req['hash']);

    $resetData = json_decode(file_get_contents($hashfile), 1);

    $password = password_hash($req['userpassword'], PASSWORD_ARGON2I);

    updateUser($resetData['useremail'], ['hash' => $password]);

    $res = [
        'message' => 'success'
    ];

    res_json($res);

    unlink($hashfile);

}




function requestPasswordReset($req) {

    $LOG_LABEL = '[PASSWORD RESET REQUEST EVENT]';

    if (!isset($req['useremail'])) {
        post_error(400, 'Missing parameter "useremail".');
        return;
    }

    if (isset($req['useremail'])) {
        $req['useremail'] = filter_var($req['useremail'], FILTER_SANITIZE_STRING);
    }

    $user = getUser($req['useremail']);

    // if user does not exist in the system
    if (!$user) {
        // return a success to the user, log the event, but don't do anything else
        res_json($res);
        Error("${LOG_LABEL} [FAILED] User does not exist: " . json_encode($req));
        return;
    }

    // log event to system
    Info("${LOG_LABEL} [SUCCESS] User exists: " . json_encode($req));

    // generate a reset hash
    $resetHash = sha1(now() . 'passwordResetEvent' . getenv('SALT_PASSWORD_RESET') . $_SERVER['REMOTE_ADDR']);

    // set the expire timestamp to 24 hours from now
    $req['timestamp'] = (now() + (hours(24)));

    if (APP_DEBUG) {
        $req['date'] = (date('r')); // TODO: should this
    }

    // create the hash file
    file_put_contents(CACHE_DIR . '/' . $resetHash, json_encode($req));

    // write a success message to the user
    $res['message'] = 'An email with a reset link will be sent shortly.';

    // mail the password reset link to the user
    $to         = $req['useremail'];
    $from       = getenv('APP_PUBLIC_EMAIL');
    $subject    = 'Password Reset';
    $msg        = "Your password reset link is provided: http://localhost:3005/reset/password/" . $resetHash;

    $headers = implode("\r\n", [
        "From: ${from}"
    ,   "Reply-To: ${from}"
    ]);

    mail($to, $subject, $msg, $headers);

    // render response
    res_json($res);
}
