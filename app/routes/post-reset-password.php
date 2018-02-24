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

    // if (file_exists(CACHE_DIR . '/' . $req['hash']));

}




function requestPasswordReset($req) {

    $LOG_LABEL = '[PASSWORD RESET EVENT]';

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
        Debug("${LOG_LABEL} [FAILED] User does not exist: " . json_encode($req));
        return;
    }

    // log event to system
    Debug("${LOG_LABEL} [SUCCESS] User exists: " . json_encode($req));

    // generate a reset hash
    $resetHash = sha1(now() . 'passwordResetEvent' . getenv('SALT_PASSWORD_RESET'));

    Debug("${LOG_LABEL} RESET HASH : ${resetHash}");

    // set the expire timestamp to 24 hours from now
    $req['timestamp'] = (now() + (hours(24)));

    if (APP_DEBUG) {
        $req['date']      = (date('r')); // TODO: should this
    }

    // create the hash file
    file_put_contents(CACHE_DIR . '/' . $resetHash, json_encode($req));

    // write a success message to the user
    $res['message'] = 'An email with a reset link will be sent shortly.';

    res_json($res);
}
