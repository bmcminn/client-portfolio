<?php

use Symfony\Component\Filesystem\Filesystem;


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
    // $body = filter_var($body, FILTER_SANITIZE_STRING);

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



function res_json($data) {
    echo json_encode($data, 1);
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
        // return false;
        redirect(ROUTE_GET_LOGIN);
        return;
    }

    $user = $_SESSION['user'];

    // check if user session is expired
    if ($user['cache'] + minutes(60) < now()) {
        // return false;
        redirect(ROUTE_GET_LOGIN);
        return;
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


/**
 * Alias for http_response_code
 * @param  [type] $statusCode [description]
 * @return [type]             [description]
 */
function status_code($statusCode) {
    http_response_code($statusCode);
}



/**
 * [Logger description]
 * @param [type] $type [description]
 * @param [type] $msg  [description]
 */
function Logger($type, $msg) {
    $logPath = LOGS_DIR . '/' . date('Y-m-d') . '.log';

    $date   = date('Y-m-d::H:i:sO');
    $caller = substr(debug_backtrace()[0]['file'], strlen(ROOT_DIR) + 1);
    $lineNo = debug_backtrace()[0]['line'];

    $data   = "[${date}] [${type}] [${caller}:${lineNo}] ${msg}" . PHP_EOL;

    file_put_contents($logPath, $data, FILE_APPEND);
}


function Debug($msg) { Logger('debug', $msg); }
function Info($msg) { Logger('info', $msg); }
function Error($msg) { Logger('error', $msg); }


/**
 * [provisionDirs description]
 * @return [type] [description]
 */
function provisionDirs() {
    $fs = new Filesystem();

    $dirs = [
        APP_DIR
    ,   VIEWS_DIR
    ,   CACHE_DIR
    ,   LOGS_DIR
    ,   CLIENTS_DIR
    ,   USERS_DIR
    ];

    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            $fs->mkdir($dir);
        }
    }
}
