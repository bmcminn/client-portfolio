<?php

// define our render method
function render($templateName='default', $model = []) {

}


/**
 * Takes num minutes and turns it into seconds
 * @param  [int]    $min    Number of minutes to be converted
 * @return [int]            Number of minutes in seconds
 */
function minutes($min) {
    return 60 * $min;
}


/**
 * parses request body content into JSON
 * @param [string]  $type   Data type to be parsed (defaults to json)
 * @return [string|array]   Returns the request body in the desired $type format
 */
function req($type='json') {
    $type = strToLower($type);

    // @sauce: https://stackoverflow.com/a/7084677/3708807
    $body = file_get_contents('php://input');

    $req = '';

    switch ($type) {
        case 'json':
            $req = json_decode($body, true);
            break;

        case 'raw':
        default:
            $req = $body;
            break;
    }

    return $req;
}


/**
 * [now description]
 * @return [type] [description]
 */
function now() {
    return floor(microtime(true));
}


/**
 * Issue a redirect to the login page
 * @return [type] [description]
 */
function redirect($route) {
    header('location:'.$route);
}


/**
 * Middleware: checks if user session data is established and redirects to /login if not
 * @return boolean [description]
 */
function isLoggedIn() {
    // check if user session has been initialized
    if (!isset($_SESSION['user'])) {
        redirect(GET_LOGIN);
    }

    $user = $_SESSION['user'];

    // check if user session is expired
    if ($user['cache'] + minutes(60) < now()) {
        redirect(GET_LOGIN);
    }

    // update user session cache timer
    $user['cache'] = now();

    // return updated user session data
    $_SESSION['user'] = $user;
}


/**
 * [error_handler description]
 * @param  [type] $errCode [description]
 * @return [type]          [description]
 */
function error_handler($errCode) {
    // TODO: setup logger to capture error information
    http_response_code($errCode);
    require(VIEWS_DIR . "/${errCode}.twig");
}



// function Log()
