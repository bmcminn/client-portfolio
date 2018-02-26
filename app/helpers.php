<?php

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;



// =======================================================
//  TIME HELPERS
// =======================================================


/**
 * Returns time for N minutes in seconds
 * @param  integer  $n  Number of minutes
 * @return integer      Time in seconds
 */
function minutes($n = 1) {
    return 60 * $n;
}


/**
 * Returns time for N hours in seconds
 * @param  integer  $n   Number of hours
 * @return integer       Time in seconds
 */
function hours($n = 1) {
    return minutes(60) * $n;
}


/**
 * Alias of time(); Returns the current time in seconds
 * @return [type] [description]
 */
function now() {
    return time();
    // return floor(microtime(true));
}



// =======================================================
//  NETWORK HELPERS
// =======================================================


/**
 * parses request body content into JSON
 * @param [string]  $type   Data type to be parsed (defaults to json)
 * @return [string|array]   Returns the request body in the desired $type format
 */
function req($req=[], $type='json') {
    $type = strToLower($type);

    // @sauce: https://stackoverflow.com/a/7084677/3708807
    $body = file_get_contents('php://input');
    // $body = filter_var($body, FILTER_SANITIZE_STRING);

    switch (strtolower($type)) {
        case 'json':
            $data   = json_decode($body, true);

            if (!$data) {
                $data = [];
            }

            $req = array_replace_recursive($data, $req);
            break;

        case 'raw':
        default:
            $req = $body;
            break;
    }

    return $req;
}


/**
 * [res_json description]
 * @param  [type] $data [description]
 * @return [type]       [description]
 */
function res_json($data) {
    echo json_encode($data, 1);
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
 * [error_page_handler description]
 * @param  [type] $errCode [description]
 * @return [type]          [description]
 */
function error_page_handler($errCode, $errMsg='') {
    // TODO: setup logger to capture error information
    status_code($errCode);
    require(VIEWS_DIR . "/error_page.twig");
}


/**
 * [post_error description]
 * @param  [type] $msg [description]
 * @return [type]      [description]
 */
function post_error($errCode, $msg) {
    status_code($errCode);

    $res = [
        'succes'    => false,
        'message'   => $msg
    ];

    Error('[POST ERROR] ' . $errCode . ' :: ' . $msg);

    res_json($res);
    Error($msg);
}


/**
 * Issue a redirect to the login page
 * @return [type] [description]
 */
function redirect($route) {
    header('location:'.$route);
}


// =======================================================
//  USER HELPERS
// =======================================================


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
 * [getUser description]
 * @param  [type] $email [description]
 * @return [obj]        [description]
 */
function getUser($email) {
    $email = strToLower(trim($email));

    $users = getUsers();

    $userData = false;

    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $userDate = $user;
            break;
        }
    }

    return $userDate;
}



function getUsers() {
    $usersCache = CACHE_DIR . '/users.json';

    if (!is_file($usersCache)) {
        cacheUsers();
    }

    $users = file_get_contents($usersCache);
    return json_decode($users, true);
}



/**
 * [cacheUsers description]
 * @return [type] [description]
 */
function cacheUsers() {
    $users  = glob(USERS_DIR . '/*.yaml');
    $userDB = [];

    foreach ($users as $userpath) {
        $contents   = file_get_contents($userpath);
        $data       = Yaml::parse($contents);
        array_push($userDB, $data);
    }

    Debug(json_encode($userDB, true));

    $userCache = CACHE_DIR . '/users.json';

    file_put_contents($userCache, json_encode($userDB, true));
}


/**
 * Detemines if a given password reset hash file exists
 * @param  [type] $hash [description]
 * @return [type]       [description]
 */
function hash_exists($hash) {

    $filepath = CACHE_DIR . '/' . $hash;

    if (!file_exists($filepath)) {
        return false;
    }

    // get hash cache data
    $cache = readJSON($filepath);

    // if cache is expired
    if (($cache['timestamp'] + PW_RESET_CACHE_EXPIRATION) < now()) {
        Info('[HASH EXISTS] ' . $hash . ' is expired; deleting file.');
        unlink($filepath);
        return false;
    }

    return true;
}



function updateUser($email, $model) {

    $email = strToLower(trim($email));

    $users = getUsers();

    for ($i = count($users)-1; $i > 0; $i--) {
        $user = $users[$i];

        if ($user['email'] === $email) {
            $user = array_replace_recursive([], $user, $model);

            $users[$i] = $user;
            break;
        }

    }

    $userCache = CACHE_DIR . '/users.json';

    file_put_contents($userCache, json_encode($users));

}



// =======================================================
//  SYSTEM HELPERS
// =======================================================


/**
 * Reads JSON data from file
 * @param  string  $filepath Filepath of JSON data we wish to consume
 * @param  boolean $assoc    Tells JSON parser to return an object or associative array
 * @return mixed             Returns an Object or Array based on $assoc boolean
 */
function readJSON($filepath, $assoc=true) {
    if (!file_exists($filepath)) {
        throw new Exception('"$filepath" does not exist');
    }

    $content = file_get_contents($filepath);

    return json_decode($content, $assoc);
}


/**
 * [Logger description]
 * @param [type] $type [description]
 * @param [type] $msg  [description]
 */
function Logger($type, $msg) {
    $logPath = LOGS_DIR . '/' . date('Y-m-d') . '.log';

    $date   = date('Y-m-d::H:i:sO');
    $type   = strToUpper($type);
    $caller = substr(debug_backtrace()[0]['file'], strlen(ROOT_DIR) + 1);
    $lineNo = debug_backtrace()[0]['line'];

    $data   = "[${date}] [${type}] [${caller}:${lineNo}] ${msg}" . EOL;

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


// define our render method
function render($templateName='default', $model = []) {

}



