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
 * @return integer  The curren time in seconds
 */
function now() {
    return time();
    // return floor(microtime(true));
}



// =======================================================
//  NETWORK HELPERS
// =======================================================


/**
 * [req description]
 * @param  array  $req      The request body data
 * @param  string $type     What format to parse the request body as
 * @return mixed            Returns the request body data depending on the $type argument
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
 * Defines a JSON response object
 * @param  mixed    $data   Array or Object data to submit
 * @return null
 */
function res_json($data) {
    echo json_encode($data, 1);
}


/**
 * Alias for http_response_code
 * @param  integer  $statusCode     The status code to define in the response header
 * @return null
 */
function status_code($statusCode) {
    http_response_code($statusCode);
}


/**
 * Render the error page on GET requests
 * @param  integer  $errCode    An HTML response code
 * @param  string   $msg        The message to return to the user/log
 * @return null
 */
function error_page_handler($errCode, $errMsg='') {
    // TODO: setup logger to capture error information
    status_code($errCode);
    require(VIEWS_DIR . "/error_page.twig");
}


/**
 * Defines a POST error response message and logs the error to system
 * @param  integer  $errCode    An HTML response code
 * @param  string   $msg        The message to return to the user/log
 * @return null
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
 * @return string   The target route to redirect the user to
 */
function redirect($route) {
    header('location:'.$route);
}


// =======================================================
//  USER HELPERS
// =======================================================


/**
 * Checks if the user is currently logged in or not
 * @param  boolean  $redirect   If true, redirects user to login screen
 * @return boolean              True if user session is valid, false otherwise
 */
function isLoggedIn($redirect=true) {
    // check if user session has been initialized
    if (!isset($_SESSION['user'])) {
        // return false;
        $redirect ? redirect(ROUTE_GET_LOGIN) : null;
        return false;
    }

    // get the user data form our session
    $user = $_SESSION['user'];

    // check if user session is expired
    if ($user['cache'] + minutes(getenv('APP_USER_CACHE_DURATION')) < now()) {
        // return false;
        $redirect ? redirect(ROUTE_GET_LOGIN) : null;
        return false;
    }

    // update user session cache timer
    $user['cache'] = now();

    // return updated user session data
    $_SESSION['user'] = $user;

    // user session data is valid
    return true;
}


/**
 * Get the user data from our lsit of users by email
 * @param  string   $email  The target user email
 * @return mixed            Returns user data (as array) if present, false if not
 */
function getUser($email) {

    // clean up email data
    $email = strToLower(trim($email));

    // get list of users from the system
    $users = getUsers();

    // init our return data var
    $userData = false;

    // iterate over users list and
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $userDate = $user;
            break;
        }
    }

    // return the user data
    return $userDate;
}


/**
 * Finds and updates user record data by email
 * @param  string   $email  Email of the target user to be updated
 * @param  array    $model  Associative array mapping the values to be updated
 * @return null
 */
function updateUser($email, $model) {

    // clean up email data
    $email = strToLower(trim($email));

    // get the current list of cached users
    $users = getUsers();

    // iterate over each user record to find target user and update its record data
    for ($i = count($users)-1; $i > 0; $i--) {
        $user = $users[$i];

        if ($user['email'] === $email) {
            $user = array_replace_recursive([], $user, $model);

            $users[$i] = $user;
            break;
        }

    }

    // update users cache file on disk
    file_put_contents(USERS_CACHE, json_encode($users));

}


/**
 * Gets the currently cached set of user data
 * @return Array The list of cached user data
 */
function getUsers() {

    // define users cache file location
    $usersCache = USERS_CACHE;

    // if users cache file does not exist, create it
    if (!is_file($usersCache)) {
        cacheUsers();
    }

    // get users cache file contents
    $users = file_get_contents($usersCache);

    // decode JSON data and return list of users
    return json_decode($users, true);
}



/**
 * Gets list of user profile data and caches them in a user JSON file/cache
 * @return null
 */
function cacheUsers() {

    // get list of user YAML files
    $users  = glob(USERS_DIR . '/*.yaml');

    // init user data list
    $userDB = [];

    // iterate over user YAML data and add each one to the user cache
    foreach ($users as $userpath) {
        $contents   = file_get_contents($userpath);
        $data       = Yaml::parse($contents);
        array_push($userDB, $data);
    }

    Info('Users cache created.');

    // write users cache to disk
    file_put_contents(USERS_CACHE, json_encode($userDB, true));

    return;
}


/**
 * Detemines if a given password reset hash file exists
 * @param  string   $hash   Target hashed cache filename string
 * @return boolean          True if the hash cache file exists; false otherwise
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

    // all else, return true that the cache exists
    return true;
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
 * Logs messaging to app logs directory
 * @param string    $type   The type of log we wish to log
 * @param string    $msg    The log message data to be logged
 */
function Logger($type, $msg) {

    // if we're not debugging the app, bug out
    if (!APP_DEBUG && strToLower($type) === 'debug') {
        return;
    }

    // define our logger to run on a daily rotation
    $logPath = LOGS_DIR . '/' . date('Y-m-d') . '.log';

    // define the current date/time
    $date   = date('Y-m-d::H:i:sO');

    // define the log type
    $type   = strToUpper($type);

    // get the current stacktrace
    $stacktrace = debug_backtrace()[0];

    // TODO: fix this so we can actually get the right calling file
    // get the calling file
    $caller = substr($stacktrace['file'], strlen(ROOT_DIR) + 1);

    // get the line number the call was made from
    $lineNo = $stacktrace['line'];

    // define the log entry
    $data   = "[${date}] [${type}] [${caller}:${lineNo}] ${msg}" . EOL;

    // append our log message to the end of our daily log file
    file_put_contents($logPath, $data, FILE_APPEND);
}


/**
 * Logs a DEBUG message to the system; Only works if env APP_DEBUG is configured
 * @param string    $msg    The message we wish to log
 */
function Debug($msg) { Logger('debug', $msg); }


/**
 * Logs an INFO message to the system
 * @param string    $msg    The message we wish to log
 */
function Info($msg) { Logger('info', $msg); }


/**
 * Logs an ERROE message to the system
 * @param string    $msg    The message we wish to log
 */
function Error($msg) { Logger('error', $msg); }



/**
 * Initializes file system for unwatched directories
 * @return null
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



